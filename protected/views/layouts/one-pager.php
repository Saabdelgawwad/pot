<?php
$this->beginContent('application.views.layouts.main', array(
    'cssClass' => 'one-pager'
));
$cssClass = isset($cssClass) ? $cssClass : 'one-pager';
?>

<div class="<?php echo $cssClass ?>">
        <div class="row-fluid">
            <div class="span6 offset4">
                <div class="content">
                    <?php $this->renderPartial('application.views.layouts.partials._flashMessages'); ?>
                    <?php echo $content; ?>
                </div><!--/span-->
            </div>
        </div><!--/.fluid-container-->
</div>

<?php $this->endContent(); ?>
