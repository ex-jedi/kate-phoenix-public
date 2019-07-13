<?php include('../perch/runtime.php'); ?>
<?php
    $domain = 'https://'. $_SERVER['HTTP_HOST'];
    PerchSystem::set_var('domain',$domain);

    header('Content-Type: application/rss+xml');

    echo '<?xml version="1.0"?>';
?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Journal | Kate Phoenix Art</title>
        <link><?php echo PerchUtil::html($domain) ?>/journal/</link>
        <description>The stories behind my art, what inspires me as an artist and my work processes.</description>
        <atom:link href="<?php echo PerchUtil::html($domain) ?>/journal/feed" rel="self" type="application/rss+xml" />
        <?php
        perch_blog_custom([
            'template'=>'blog/rss_post.html',
            'count'=>10,
            'sort'=>'postDateTime',
            'sort-order'=>'DESC'
        ]);
        ?>
    </channel>
</rss>