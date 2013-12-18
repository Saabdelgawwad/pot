<table id="<?= $id ?>" class="table-bordered <?= $class ?>">
    <tr>
        <th><?= CHtml::encode(ConsultationProduct::model()->getAttributeLabel('product_id')) ?></th>
        <th class="narrow"><?= CHtml::encode(ConsultationProduct::model()->getAttributeLabel('count')) ?></th>
        <th class="narrow">Preis</th>
        <th></th>
    </tr>
    <?= $content ?>
</table>
<?= $addLink ?>

<?php

Yii::app()->clientScript->registerScript('', '
    $(' . CJavaScript::encode('#' . $id) .').on("change", "input, select", function() {
        var tr = $(this).closest("tr");
        var price = tr.find("option:selected").data("price");
        var count = tr.find("input").val();
        tr.find(".price").text((price !== undefined && /^\d+$/.test(count)) ? (count * price).toFixed(2) : "");
    });
');
