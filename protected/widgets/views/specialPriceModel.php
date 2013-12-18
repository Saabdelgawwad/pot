<tr id="<?= $id ?>" class="<?= $class ?>" data-id="<?= $dataId ?>">
    <td>
        <?= CHtml::activeDropDownList($model, $name .'customer_category', CustomerCategory::getOptions()) ?>
        <?= CHtml::error($model, 'customer_category') ?>
    </td>
    <td>
        <?= CHtml::activeTextField($model, $name . 'price') ?>
        <?= CHtml::error($model, 'price') ?>
    </td>
    <td><?= $icons ?></td>
</tr>
