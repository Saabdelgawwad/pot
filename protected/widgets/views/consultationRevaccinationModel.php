<tr id="<?= $id ?>" class="<?= $class ?>" data-id="<?= $dataId ?>">
    <td>
        <?= CHtml::activeDropDownList($model, $name . 'period', ConsultationRevaccination::periods()) ?>
        <?= CHtml::error($model, 'period') ?>
    </td>
    <td>
        <?= CHtml::activeTextField($model, $name . 'comment') ?>
        <?= CHtml::error($model, 'comment') ?>
    </td>
    <td><?= $icons ?></td>
</tr>
