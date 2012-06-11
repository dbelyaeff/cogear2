<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
    <title><?php echo $title ?></title>
    <link><?php echo $link ?></link>
    <description></description>
    <language>ru</language>
    <image>
    <link>http://<?php echo l(TRUE)?></link>
    <url><?php echo l().'uploads'.config('theme.logo')?></url>
    <title><?php echo config('site.name')?></title>
    </image>
    <?php foreach ($items as $item): ?><item>
        <title><![CDATA[<?php echo $item->name ?>]]></title>
        <guid isPermaLink="true"><?php echo $item->getLink() ?></guid>
        <link><?php echo $item->getLink() ?></link>
        <description><![CDATA[
                    <?php echo $item->body ?>
        ]]></description>
        <author><?php echo $item->author->getName() ?></author>
        <pubDate><?php echo gmdate("D, d M Y H:i:s", strtotime($item->created_date)) . ' GMT'; ?></pubDate>
    </item><?php endforeach; ?>
    </channel>
</rss>