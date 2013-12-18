<?php

class WebUser extends CWebUser {

    protected $_model;

    public function getModel()
    {
        if (!$this->isGuest and !isset($this->_model)) {
            $this->_model = User::model()->active()->findByPk($this->id);
        }
        return $this->_model;
    }

    public function getName()
    {
        return $this->isGuest ? 'Gast' : $this->model->email;
    }

    public function setName($name)
    {
        // Name wird nicht gesetzt, da er direkt aus dem User-Model gelesen wird.
    }
}
