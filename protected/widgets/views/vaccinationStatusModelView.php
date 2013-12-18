<tr id="<?= $id ?>" class="<?= $class ?>" data-id="<?= $dataId ?>">
    <td><?= CHtml::encode($model->getDiseaseName()) ?></td>
    <td><?= isset($model->last_vaccination) ? $model->last_vaccination : '-' ?></td>
    <td><?= (float) $model->protection ?></td>
    <td><?= $icons ?></td>
</tr>
