<ul class="nav nav-tabs">
  <?php foreach($menu as $item):?>
  <li class="<?php if($item->active) echo 'active';?>">
      <a href="<?php echo $item->link?>"><?php echo $item->label?></a>
  </li>
  <?php endforeach;?>
</ul>