<table id="<?= $id ?>" class="table table-bordered <?= $class ?>">
    <tr>
        <th><?= CHtml::encode(BillItem::model()->getAttributeLabel('product_id')) ?></th>
        <th class="narrow"><?= CHtml::encode(BillItem::model()->getAttributeLabel('count')) ?></th>
        <th class="narrow"><?= CHtml::encode(BillItem::model()->getAttributeLabel('price')) ?></th>
        <th></th>
    </tr>
    <?= $content ?>
    <tr>
        <td></td>
        <td><strong>Preis Total</strong></td>
        <td class="priceTotal"><?= Yii::app()->format->formatPrice($sum); ?></td>
        <td></td>
    </tr>
</table>
<?= $addLink ?>

<?php

Yii::app()->clientScript->registerScript('', '
    $(' . CJavaScript::encode('#' . $id) .').on("change", "input, select", function() {
        var table = $(this).closest("table");
        var tr = $(this).closest("tr");
        var price = tr.find("option:selected").data("price");
        var count = tr.find("input").val();
        tr.find(".price").text((price !== undefined && /^\d+$/.test(count)) ? (count * price).toFixed(2) : "");
        var priceTotal = 0;
        table.find(".price").each(function() {
            var num = parseFloat($.trim($(this).text()));
            if (!isNaN(num)) {
                priceTotal += num;
            }
        });
        table.find(".priceTotal").text(priceTotal.toFixed(2));
    });
');
