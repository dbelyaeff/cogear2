<ul>
<?foreach($accounts as $account):?>
    <?
        $site = parse_url($account->identity,PHP_URL_HOST);
        $favicon = 'http://'.$site.'/favicon.ico';
    ?>
    <li id="loginza-<?=$account->id?>" style="list-style-image: url('<?=$favicon?>')"><?if($account->photo):?>
      <?if($account->photo):?>  <a title="<?=t('If you want to use this avatar, just click on the image.','Loginza')?>" href="#/loginza/avatar/<?=$account->id?>"><img src="<?=$account->photo?>" class="avatar" height="24"></a><?endif;?>
    <?endif?>
         <a href="<?=$account->identity?>"><?=$account->full_name?></a> <a href="#/loginza/delete/<?=$account->id?>" class="edit delete">[x]</a>
    </li>    
<? endforeach; ?>
</ul>