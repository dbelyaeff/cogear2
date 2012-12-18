<table class="table table-bordered table-striped">
    <thead>
        <tr>
           <?php
            $thead = new Stack(array('name' => $options->name.'.thead'));
            $thead->append('<th>'.t('Имя пользователя').'</th>');
            $thead->append('<th>'.t('Постов').'</th>');
            cogear()->gears->Comments && $thead->append('<th>'.t('Комментариев').'</th>');
            $thead->append('<th>'.t('Зарегистрирован').'</th>');
            echo $thead->render();
           ?>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($users as $user){
            $tr = new Stack(array('name' => $options->name.'.tr'));
            $tr->append('<td>'.$user->getLink('avatar').' '.$user->getLink('profile').'</td>');
            $tr->append('<td>'.$user->posts.'</td>');
            cogear()->gears->Comments &&  $tr->append('<td>'.$user->comments.'</td>');
            $tr->append('<td>'.df($user->reg_date,'d M Y').'</td>');
            echo '<tr>'.$tr->render().'</tr>';
        }
        ?>
    </tbody>
</table>