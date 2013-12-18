<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'one-pager-extended'
));
$cssClass = isset($cssClass) ? $cssClass : 'one-pager-extended';
?>

<div class="container <?php echo $cssClass ?>">
    <div class="row-fluid">
        <div class="span12">
            <div class="content content-container">
                <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
                <?php echo $content; ?>
            </div><!--/span-->
        </div>
    </div><!--/.fluid-container-->
</div>

<?php $this->endContent(); ?>
