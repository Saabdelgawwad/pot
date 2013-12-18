<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'front-layout-sidebar-right',
));
$cssClass = isset($cssClass) ? $cssClass : 'front-layout';
?>
<div class="container <?php echo $cssClass; ?>">
        <div class="row-fluid">
            <div class="span7 offset1">
                <div class="content content-container">
                    <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
                    <?php echo $content; ?>
                </div>
            </div><!--/span-->
            <?php $this->renderPartial('application.views.layouts.partials._sidebar'); ?>
    </div><!--/.fluid-container-->
</div>


<?php $this->endContent(); ?>