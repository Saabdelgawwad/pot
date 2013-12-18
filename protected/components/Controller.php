<?php

/**
 * Basis-Klasse für alle Controller
 */
class Controller extends CController {

    const PAGE_TITLE_DEFAULT = 'www.wmpot.ch';

    /**
     *
     * @var string Seitentitel
     */
    public $_pageTitle;

    /**
     * @var string H1-Titel 
     */
    public $title = '';

    /**
     * @var string Default-Layout für alle Controller 
     */
    public $layout = 'one-pager-extended';

    /**
     * @var array Array aller Sidebars. Jede Sidebar ist entweder ein CMenu-Array
     * oder ein HTML-String.
     */
    public $sidebars = array();

    /**
     * @var string URL zum jeweiligen Assets-Ordner eines Controllers
     */
    private $_assetsUrl;

    /**
     * Sendet einen JSON-Respone
     * 
     * @param mixed $var Inhalt, der encoded werden soll 
     */
    public function json($var)
    {
        header('Content-type: application/json');
        echo CJSON::encode($var);
        Yii::app()->end();
    }

    public function getPageTitle()
    {
        if ($this->_pageTitle == null && $this->title !== null) {
            $this->_pageTitle = $this->title;
        }
        if (Yii::app()->controller->getAction()->getId() == 'index') {
            return $this->_pageTitle = self::PAGE_TITLE_DEFAULT . ' » ' . Yii::app()->params['slogan'];
        } else {
            return $this->_pageTitle = $this->_pageTitle . ' » ' . self::PAGE_TITLE_DEFAULT;
        }
    }

    /**
     * Gibt die Assets-Url eines Controllers zurück
     * @return string
     */
    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null) {

            // Name des aufrufenden Controllers herausfinden
            $rc = new ReflectionClass(get_class($this));
            $controllerName = $rc->getName();

            // Pfad zum Assets-Order erstellen
            $viewFolder = lcfirst(str_replace('Controller', '', $controllerName));
            $path = "application.views.$viewFolder.assets";

            $this->_assetsUrl = Yii::app()->getAssetManager()->publish(YiiBase::getPathOfAlias($path), false, -1, true /* forceCopy */);
        }

        return $this->_assetsUrl;
    }

}