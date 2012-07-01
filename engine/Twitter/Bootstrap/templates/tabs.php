<ul class="nav nav-tabs">
  <?php foreach($menu as $item):?>
  <li class="<?php echo $item->class?> <?php if($item->active) echo 'active';?>">
      <a class="<?php echo $item->link_class?>" href="<?php echo $item->link?>"><?php echo $item->label?></a>
  </li>
  <?php endforeach;?>
</ul>