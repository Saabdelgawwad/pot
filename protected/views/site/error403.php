<?php
$this->layout = 'one-pager';
$this->_pageTitle = 'Error 403';
?>

<div class="row-fluid">
    <?php echo $this->title = '<h1>Keine Berechtigung</h1>'; ?>
    <p> Sie haben keine Berechtigung, um diese Seite zu sehen. Folgende Ursachen könnten dazu geführt haben:</p>
    <ul>
        <li> Die Seite befindet sich in einem geschützten Bereich</li>
        <li> Sie sind nicht eingeloggt</li>
        <li> Sie wurden zu einer unberechtigten Bearbeitungsansicht einer Seite weitergeleitet, bei der Sie lediglich Leserechte haben</li>
    </ul>
    <p>Bei Fragen wenden Sie sich an den <a href='mailto:<?=Yii::app()->settings->get('adminEmail');?>'>Traveladvice Support</a>.</p>
</div>
