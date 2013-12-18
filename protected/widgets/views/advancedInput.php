<div id="<?= CHtml::encode($name) ?>" class="mod-advanced-input <?= CHtml::encode($class) ?>" data-name="<?= CHtml::encode($name) ?>"> 
    <?php if (isset($modalUrl)&& !isset($dataModel)): ?>
        <a href="#" class="modalButton" tabindex="-1">
            <i class="icon-list"></i>
        </a>
    <?php endif; ?>
    <?php if ($dataModel === 'grid' && !isset($modalUrl)): ?>
        <a href="#" class="modalButtonGrid" tabindex="-1">
            <i class="icon-list"></i>
        </a>
    <?php endif; ?>
    <ul>
        <?= $items ?>

        <li class="add-item">
            <input type="text" name="add-item" autocomplete="off" />
        </li>
    </ul>
</div>
