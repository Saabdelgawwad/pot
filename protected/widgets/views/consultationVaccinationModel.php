<?php
    $this->beginWidget('bootstrap.widgets.TbBox', array(
        'title' => CHtml::activeDropDownList($model, $name . 'product_id', ProductVaccinationEffect::getProducts('-- Bitte wählen --')),
        'htmlOptions' => array(
            'id' => $id,
            'class' => 'consultation-vaccination ' . $class,
            'data-id' => $dataId,
        ),
        'headerButtons' => array(
            array(
                'class' => 'bootstrap.widgets.TbButtonGroup',
                'size' => 'small',
                'buttons' => array(
                    array(
                        'icon' => 'remove',
                        'url' => '#',
                        'htmlOptions' => array(
                            'class' => $deleteClass,
                            'rel' => 'tooltip',
                            'title' => 'Entfernen'
                        ),
                    ),
                ),
            ),
        ),
    ));
?>
    <?= Chtml::error($model, 'product_id') ?>

    <div class="row">
        <?= CHtml::activeLabelEx($model, 'comment') ?>
        <?= CHtml::activeTextArea($model, $name . 'comment') ?>
        <?= CHtml::error($model, 'comment') ?>
    </div>

    <h3>Nachimpfungen</h3>
    <?php $revaccinationWidgets[$model->id]->run(); ?>

<?php $this->endWidget(); ?>
