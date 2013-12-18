<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'front-layout-sidebar-left',
));
$cssClass = isset($cssClass) ? $cssClass : 'front-layout';
?>
<div class="container <?php echo $cssClass; ?>">
    <div class="row-fluid">
        <?php $this->renderPartial('application.views.layouts.partials._sidebar'); ?>
        <div class="span7">
            <div class="content content-container">
                <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
                <?php echo $content; ?>
            </div>
        </div><!--/span-->
    </div><!--/.fluid-container-->
</div>
<?php $this->endContent(); ?>