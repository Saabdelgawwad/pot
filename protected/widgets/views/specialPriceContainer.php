<table id="<?= $id ?>" class="table-bordered <?= $class ?>">
    <tr>
        <th><?= CHtml::encode(SpecialPrice::model()->getAttributeLabel('customer_category')) ?></th>
        <th><?= CHtml::encode(SpecialPrice::model()->getAttributeLabel('price')) ?></th>
        <th></th>
    </tr>
    <?= $content ?>
</table>
<?= $addLink ?>
