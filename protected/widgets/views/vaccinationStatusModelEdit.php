<tr id="<?= $id ?>" class="<?= $class ?>" data-id="<?= $dataId ?>">
    <td class="diseaseInput">
        <div class="dropdown <?= isset($model->disease_free_text) ? 'hide' : '' ?>">
            <?= CHtml::activeDropDownList($model, $name . 'disease_id', Disease::getOptions('-- Bitte wählen --')) ?>
            <span class="btn js-change-to-free-text" title="Freitext" rel="tooltip"><i class="icon-pencil"></i></span>
            <?= CHtml::error($model, 'disease_id') ?>
        </div>
        <div class="free-text <?= isset($model->disease_free_text) ? '' : 'hide' ?>">
            <?= CHtml::activeTextField($model, $name . 'disease_free_text') ?>
            <span class="btn js-change-to-dropdown" title="Dropdown" rel="tooltip"><i class="icon-list"></i></span>
            <?= CHtml::error($model, 'disease_free_text') ?>
        </div>
    </td>
    <td>
        <?= CHtml::activeTextField($model, $name . 'last_vaccination', array('placeholder' => 'tt.mm.jjjj')) ?>
        <?= CHtml::error($model, 'last_vaccination') ?>
    </td>
    <td>
        <?= CHtml::activeTextField($model, $name . 'protection') ?>
        <?= CHtml::error($model, 'protection') ?>
    </td>
    <td><?= $icons ?></td>
</tr>
