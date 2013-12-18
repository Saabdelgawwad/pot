<?php

class SiteController extends Controller {

    public $defaultAction = 'index';

    public function filters() {
        return array(
            'accessControl',
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('index', 'error'),
                'users' => array('*'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {
        $this->layout = 'full-width';
        echo get_class(Yii::app());
        $this->render('index');
    }
    
    public function actionError() {
        $error = Yii::app()->errorHandler->error;
        if ($error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo 'Fehler ' . $error['code'];
            } else {
                $view = 'error' . intval($error['code']);
                if (!$this->getViewFile($view)) {
                    $view = 'error';
                }
                $this->render($view, $error);
            }
        }
    }
}
