<div class="gear" id="gear-<?= $gear ?>">
     <div class="gear-title"><?= $name?> <sup class="gear-version"><?=$version?></sup>
     <?if($type != Gear::CORE):?>
         <a class="edit ajaxed" href="#/gears/<?=($active ? 'deactivate' : 'activate').'/'.$gear?>">[<?=t($active ? 'deactivate' : 'activate')?>]</a>
     <?endif;?>
     <?if($has_admin):?>
         <a class="edit" href="<?=Url::gear('admin').strtolower($gear)?>">[<?=t('Control panel')?>]</a>
     <?endif;?>
     </div>
     <div class="gear-description"><?= $description?></div>
     <div class="gear-info">
         <b><?=t('Author: ')?></b> <?=HTML::a('mailto:'.$email,t($author,'Authors'))?>
         <b><?=t('Site: ')?></b> <?=HTML::a($site,$site)?>
     </div>
</div>