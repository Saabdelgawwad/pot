<?php foreach ($this->sidebars as $id => $sidebar): ?>
<div class="content sidebar span3 <?= $this->layout == 'front-layout-sidebar-left' ? 'offset1' : ''; ?> <?= is_array($sidebar) ? 'sidebar-nav' : ''; ?>" id="sidebar-<?= CHtml::encode($id); ?>">
        <?php if (is_array($sidebar)): ?>
            <?php
                $this->widget('bootstrap.widgets.TbMenu', array(
                    'type' => 'list',
                    'items' => $sidebar,
                ));
            ?>
        <?php else: ?>
            <?= $sidebar; ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
