<?php

class ActiveRecord extends CActiveRecord {

    protected $tableName;
    protected $tablePrefix = 'ta_';

    public function tableName()
    {
        return $this->tablePrefix . $this->tableName;
    }

    public static function model($className = __CLASS__)
    {
        if (isset($className)) {
            $className = get_called_class();
        }
        return parent::model($className);
    }

    /**
     * Entfernt alle von Yii gesetzten Default Attribute.
     * Wird benötigt, da sonst die Filterfunktionen in der Admin Ansicht nicht funktionieren.
     * Bsp: accessRight im Group Model ist dann 0 (Datenbank Standard) anstelle von null -> Wird nach 0 gefiltert = leeres Resultset.
     * 
     * Um doch Default Werte zu setzen, wird in den rules() im jeweiligen Model eine entsprechende Regel pro Attribut festgelegt.
     */
    public function init()
    {
        parent::init();
        $this->unsetAttributes();
    }

    /**
     * Datumsfeld parsen
     * Mögliche Formate:
     * - DateField-Klasse
     * - Timestamp als Integer
     * - Datum als String im Schweizer-Format
     */
    public static function convertDate($value, $format)
    {
        if ($value instanceof DateField) {
            return $value->getTimestamp();
        } elseif (is_integer($value)) {
            return $value;
        } elseif (is_string($value)) {
            return DateField::parse($value, $format);
        } else {
            return null;
        }
    }

    public function __get($name)
    {
        /*
         * Mit $model->relatedEntitySafe kann in Views ein "sicheres" nicht-null-
         * Model aus einer Relation geholt werden. Falls $model->relatedEntity
         * null ist, wird ein neues Entity zurückgegeben.
         */
        if (substr($name, -4) === 'Safe') {
            $relation = substr($name, 0, -4);
            if (isset($this->getMetaData()->relations[$relation])) {
                $model = parent::__get($relation);
                if (!isset($model)) {
                    $className = $this->getMetaData()->relations[$relation]->className;
                    $model = new $className;
                }
                return $model;
            }
        }
        
        return parent::__get($name);
    }

    /**
     * After Find: Datumsfelder durch DateField-Klassen ersetzen
     */
    protected function afterFind()
    {
        parent::afterFind();

        foreach ($this->metadata->tableSchema->columns as $columnName => $column) {
            if (isset($this->$columnName)) {
                if ($column->dbType == 'date') {
                    $this->$columnName = new DateField(strtotime($this->$columnName), DateField::DATE_FORMAT);
                } elseif ($column->dbType == 'datetime') {
                    $this->$columnName = new DateField(strtotime($this->$columnName), DateField::DATE_TIME_FORMAT);
                } elseif ($column->dbType == 'time') {
                    $this->$columnName = new DateField(strtotime($this->$columnName), DateField::TIME_FORMAT);
                }
            }
        }
    }

    /**
     * Before Save: Datumsfelder parsen (siehe convertDate)
     */
    protected function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }

        foreach ($this->metadata->tableSchema->columns as $columnName => $column) {
            if (isset($this->$columnName)) {
                if ($column->dbType == 'date') {
                    $this->$columnName = date('Y-m-d', self::convertDate($this->$columnName, DateField::DATE_FORMAT));
                } elseif ($column->dbType == 'datetime') {
                    $this->$columnName = date('Y-m-d H:i:s', self::convertDate($this->$columnName, DateField::DATE_TIME_FORMAT));
                } elseif ($column->dbType == 'time') {
                    $this->$columnName = date('H:i:s', self::convertDate($this->$columnName, DateField::TIME_FORMAT));
                }
            }
        }

        return true;
    }

    /**
     * After Save: afterFind() aufrufen, damit Datumsfelder wieder umgewandelt werden
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->afterFind();
    }

    /**
     * date-Validator (Format: siehe convertDate)
     */
    public function date($attribute, $params)
    {
        if (isset($params['allowEmpty']) and $params['allowEmpty'] and ($this->$attribute === null or $this->$attribute === '')) {
            $this->$attribute = null;
            return;
        }
        if (self::convertDate($this->$attribute, DateField::DATE_FORMAT) === null) {
            $this->addError($attribute, 'Ungültiges Datumsformat');
        }
    }

    /**
     * datetime-Validator (Format: siehe convertDate)
     */
    public function datetime($attribute, $params)
    {
        if (isset($params['allowEmpty']) and $params['allowEmpty'] and ($this->$attribute === null or $this->$attribute === '')) {
            $this->$attribute = null;
            return;
        }
        if (self::convertDate($this->$attribute, DateField::DATE_TIME_FORMAT) === null) {
            $this->addError($attribute, 'Ungültiges Datumsformat');
        }
    }

    /**
     * time-Validator (Format: siehe convertDate)
     */
    public function time($attribute, $params)
    {
        if (isset($params['allowEmpty']) and $params['allowEmpty'] and ($this->$attribute === null or $this->$attribute === '')) {
            $this->$attribute = null;
            return;
        }
        $timestamp = self::convertDate($this->$attribute, DateField::TIME_FORMAT);
        if ($timestamp === null or $timestamp >= strtotime('tomorrow')) {
            $this->addError($attribute, 'Ungültiges Zeitformat');
        }
    }

    /**
     * multipleUnique-Validator
     * Validiert, wenn mehrere Felder zusammen unique sein müssen.
     * Felder mit "+" voneinander abtrennen.
     */
    public function multipleUnique($attributes)
    {
        $attributes = explode('+', $attributes);
        $criteria = new DbCriteria;

        foreach ($attributes as $attribute) {
            $criteria->mergeWith(array(
                'condition' => $attribute . ' = :' . $attribute,
                'params' => array(
                    $attribute => $this->$attribute,
                ),
            ));
        }

        $validator = new CUniqueValidator;
        $validator->allowEmpty = false;
        $validator->criteria = $criteria;
        $validator->attributes = array($attributes[0]);
        $validator->message = 'Diese Kombination existiert schon';
        $validator->validate($this);
    }

    /**
     * Validator für Zeitfenster, die sich nicht überschneiden dürfen
     * Format für Parameter: StartFeld+EndFeld+DatumsFeld
     */
    public function noTimeframeIntersection($attributes)
    {
        $attributes = explode('+', $attributes);
        if (count($attributes) !== 3) {
            throw new CException;
        }
        list($startField, $endField, $dateField) = $attributes;

        if ($this->getErrors($startField) !== array() or $this->getErrors($endField) !== array() or $this->getErrors($dateField) !== array()) {
            return;
        }

        $id = isset($this->primaryKey) ? $this->primaryKey : 0;
        $start = self::convertDate($this->$startField, DateField::TIME_FORMAT);
        $end = self::convertDate($this->$endField, DateField::TIME_FORMAT);

        $date = $this->$dateField;
        if ($this->metadata->tableSchema->columns[$dateField]->dbType == 'date') {
            $date = date('Y-m-d', self::convertDate($this->$dateField, DateField::DATE_FORMAT));
        }

        if ($start >= $end) {
            $this->addError($endField, 'Ungültiges Ende');
        } else {
            $criteria = new DbCriteria;
            $criteria->compare($this->tableSchema->primaryKey, '<>' . $id)
                    ->compare(Yii::app()->db->quoteColumnName($dateField), '=' . $date)
                    ->compare(Yii::app()->db->quoteColumnName($startField), '<' . date('H:i:s', $end))
                    ->compare(Yii::app()->db->quoteColumnName($endField), '>' . date('H:i:s', $start));
            if (self::model()->exists($criteria)) {
                $this->addError('', 'Überlappende Zeitfenster');
            }
        }
    }

}
