<table id="<?= $id ?>" class="table-bordered <?= $class ?>">
    <tr>
        <th><?= CHtml::encode(ProductVaccinationEffect::model()->getAttributeLabel('disease_id')) ?></th>
        <th><?= CHtml::encode(ProductVaccinationEffect::model()->getAttributeLabel('effect')) ?></th>
        <th></th>
    </tr>
    <?= $content ?>
</table>
<?= $addLink ?>
