<div class="pagination<?if($ajaxed):?> ajaxed<?endif?>"<?if($target):?> rel="<?=$target?>"<?endif?>>
    <ul>
        <?php
        $disabled ='';
        if ($current == $first) {
				$disabled ='disabled';
			}
            echo '<li class="turn-pages '.$disabled.'"><a href="' . $base_uri . ($method ? '?' . Url::extendQuery(array(Pager_Pages::ARG => $first)) : $first) . '">' . t('&laquo;', 'Pager') . '</a></li>';
            echo '<li class="turn-pages '.$disabled.'"><a href="' . $base_uri . ($method ? '?' . Url::extendQuery(array(Pager_Pages::ARG => $prev)) : $prev) . '">' . t('&lsaquo;', 'Pager') . '</a></li>';      
        if ($order == Pager_Pages::FORWARD) {
            for ($i = $first; $i <= $last; $i++) {
                if ($i != $current) {
                    echo '<li><a href="' . $base_uri . ($method ? '?' . Url::extendQuery(array(Pager_Pages::ARG => $i)) : $i) . '">' . $i . '</a></li>';
                } else {
                    echo '<li class="active"><a href="">' . $i . '</a></li>';
                }
            }
        } else {
            for ($i = $first; $i >= $last; $i--) {
                if ($i != $current) {
                    echo '<li><a href="' . $base_uri . ($method ? '?' . Url::extendQuery(array(Pager_Pages::ARG => $i)) : $i) . '">' . $i . '</a></li>';
                } else {
                    echo '<li class="active"><a href="">' . $i . '</a></li>';
                }
            }
        }
        $disabled ='';
        if ($current == $last) { 
				$disabled ='disabled'; 
			}
			echo '<li class="turn-pages '.$disabled.'"><a href="' . $base_uri . ($method ? '?' . Url::extendQuery(array(Pager_Pages::ARG => $next)) : $next) . '">' . t('&rsaquo;', 'Pager') . '</a></li>';
            echo '<li class="turn-pages '.$disabled.'"><a href="' . $base_uri . ($method ? '?' . Url::extendQuery(array(Pager_Pages::ARG => $last)) : $last) . '">' . t('&raquo;', 'Pager') . '</a></li>';
        ?>
    </ul>
</div>
