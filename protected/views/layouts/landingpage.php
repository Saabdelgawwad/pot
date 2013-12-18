<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'landingpage'
));
$cssClass = isset($cssClass) ? $cssClass : 'landingpage';
?>

<div class="container-fluid <?php echo $cssClass ?>">
    <div class="row-fluid">
        <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
        <?php echo $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
