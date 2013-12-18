<table id="<?= $id ?>" class="table-bordered <?= $class ?>">
    <tr>
        <th class="col1"><?= CHtml::encode(VaccinationStatus::model()->getAttributeLabel('disease_id')) ?></th>
        <th><?= CHtml::encode(VaccinationStatus::model()->getAttributeLabel('last_vaccination')) ?></th>
        <th class="narrow"><?= CHtml::encode(VaccinationStatus::model()->getAttributeLabel('protection')) ?></th>
        <th></th>
    </tr>
    <?= $content ?>
</table>
<?= $addLink ?>
