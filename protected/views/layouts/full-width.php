<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'full-width'
));
$cssClass = isset($cssClass) ? $cssClass : 'full-width';
?>

<div class="container <?php echo $cssClass ?>">
    <div class="row-fluid">
        <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
        <?php echo $content; ?>
    </div>
</div>
<?php $this->endContent(); ?>
