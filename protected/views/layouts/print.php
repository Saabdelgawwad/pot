<!DOCTYPE html>
<html>
    <head>
        <title><?= CHtml::encode($this->pageTitle) ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="/css/print.css?m=<?php echo filemtime(Yii::getPathOfAlias('webroot.css') . '/print.css') ?>" rel="stylesheet" />
    </head>
    <body>
        <div class="container">            
            <?php echo $content; ?>
        </div>
    </body>
</html>
