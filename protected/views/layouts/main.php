<?php
header('Content-Type: text/html; charset=UTF-8');

Yii::app()->bootstrap->registerAssetCss('bootstrap-datepicker.css');
Yii::app()->clientScript->registerCssFile('/css/screen.css');
Yii::app()->clientScript->registerScriptFile('/js/screen.js');
Yii::app()->clientScript->registerScriptFile('/js/lib/plugins/bootstrap-typeahead-custom.js');
Yii::app()->clientScript->registerScriptFile('/js/lib/plugins/bootstrap-datepicker.js');
Yii::app()->clientScript->registerScript('baseUrl', 'var baseUrl = ' . CJavaScript::encode(Yii::app()->createAbsoluteUrl('/')) . ';', CClientScript::POS_BEGIN);
?>

<!DOCTYPE html>

<!--[if !IE]> -->
<html>
    <!-- <![endif]-->

    <!--[if IE]>
    <html class="ie">
    <![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    </head>

    <body>
        <div class="box">
            <?php $this->renderPartial('application.views.layouts.partials._navbarTop'); ?>
            <?php echo $content; ?>
        </div>
    </body>
</html>
