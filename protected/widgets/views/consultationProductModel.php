<?php
    $productOptions = array();
    foreach (Product::model()->forTravelAdvice()->findAll() as $product) {
        $productOptions[$product->id] = array(
            'data-price' => $product->getPriceForCustomer($customer),
        );
    }
?>

<tr id="<?= $id ?>" class="<?= $class ?>" data-id="<?= $dataId ?>">
    <td>
        <?= CHtml::activeDropDownList($model, $name . 'product_id', Product::getOptions('-- Bitte wählen --', 'forTravelAdvice'), array('options' => $productOptions)) ?>
        <?= CHtml::error($model, 'product_id') ?>
    </td>
    <td>
        <?= CHtml::activeTextField($model, $name . 'count') ?>
        <?= CHtml::error($model, 'count') ?>
    </td>
    <td class="price">
        <?php if (!$model->hasErrors() and isset($model->relatedProduct)): ?>
            <?= CHtml::encode(Yii::app()->format->price($model->count * $model->relatedProduct->getPriceForCustomer($customer))) ?>
        <?php endif; ?>
    </td>
    <td><?= $icons ?></td>
</tr>
