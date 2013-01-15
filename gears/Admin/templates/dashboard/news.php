<ul>
<?php
$data = file_get_contents('http://cogear.ru/?rss');
$feed = simplexml_load_string($data);
//$feed = new SimpleXMLElement($data);
foreach ($feed->xpath('//item') as $item) {
    ?>
    <li><?php echo HTML::a('http://cogear.ru'.$item->link, $item->title,array('target'=>'_blank')) ?>
        <small>
            <?php echo df(strtotime($item->pubDate)) ?>
        </small>
    </li>
    <?
}
?>
</ul>