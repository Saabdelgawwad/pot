<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'one-pager-medium'
));
$cssClass = isset($cssClass) ? $cssClass : 'one-pager-medium';
?>

<div class="container <?php echo $cssClass ?>">
    <div class="row-fluid">
        <div class="span8 offset2">
            <div class="content">
                <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
                <?php echo $content; ?>
            </div><!--/span-->
        </div>
    </div><!--/.fluid-container-->
</div>

<?php $this->endContent(); ?>
