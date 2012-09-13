<div id="admin-menu" class="navbar navbar-fixed-top navbar-inverse">
    <div class="navbar-inner">
        <ul class="nav">
            <?php foreach ($menu as $element): ?>
                <?php
                if ($element->elements) {
                    $element->class = 'dropdown';
                }
                ?>
                <li class="<?php echo $element->class . ' ' . ($element->active ? 'active' : ''); ?>">
                    <?php if ($element->elements): ?>
                        <a class="dropdown-toggle"
                           data-toggle="dropdown"
                           href="<?php echo $element->link ?>">
                               <?php echo $element->label ?>
                            <b class="caret"></b>
                        </a>
                    <?php elseif ($element->link): ?>
                        <a href="<?php echo $element->link ?>"><?php echo $element->label; ?></a>
                    <?php else: ?>
                        <?php echo $element->label ?>
                    <?php endif; ?>
                    <?php if ($element->elements): ?>
                        <ul class="dropdown-menu">
                            <?php foreach ($element->elements as $el): ?>
                                <li class="<?php echo $element->class; ?>">
                                    <?php if ($el->link): ?>
                                        <a href="<?php echo $el->link ?>">
                                        <?php endif; ?>
                                        <?php echo $el->label; ?>
                                        <?php if ($el->link): ?>
                                        </a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>