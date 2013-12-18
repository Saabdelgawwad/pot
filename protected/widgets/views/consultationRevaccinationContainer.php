<table id="<?= $id ?>" class="table-bordered <?= $class ?>">
    <tr>
        <th><?= CHtml::encode(ConsultationRevaccination::model()->getAttributeLabel('period')) ?></th>
        <th><?= CHtml::encode(ConsultationRevaccination::model()->getAttributeLabel('comment')) ?></th>
        <th></th>
    </tr>
    <?= $content ?>
</table>
<?= $addLink ?>
