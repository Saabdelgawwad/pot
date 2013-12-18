<tr id="<?= $id ?>" class="<?= $class ?>" data-id="<?= $dataId ?>">
    <td>
        <?= CHtml::activeDropDownList($model, $name .'disease_id', Disease::getOptions()) ?>
        <?= CHtml::error($model, 'disease_id') ?>
    </td>
    <td>
        <?= CHtml::activeTextField($model, $name . 'effect') ?>
        <?= CHtml::error($model, 'effect') ?>
    </td>
    <td><?= $icons ?></td>
</tr>
