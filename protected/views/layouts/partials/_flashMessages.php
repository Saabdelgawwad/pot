<?php
foreach (Yii::app()->user->getFlashes() as $key => $message) {
    // Kein CHtml::encode(), da $message aus HTML besteht
    echo '<div class="alert alert-' . $key . '">' . $message . '<a class="close" data-dismiss="alert" href="#">&times;</a></div>';
}
?>