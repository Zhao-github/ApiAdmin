<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?php echo C('APP_NAME') ?> - 系统发生错误</title>
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.css">
    <script src="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.js"></script>
</head>
<body>
<div class="ui very padded piled red text container segment" style="margin-top:3%; max-width: none !important;">
    <h1 class="ui orange header huge"><i class="warning sign icon"></i></h1>
    <h2 class="ui header huge"><?php echo strip_tags($e['message']);?></h2>
    <?php if(isset($e['file'])) {?>
        <h4>错误位置</h4>
        <p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
    <?php }?>
    <?php if(isset($e['trace'])) {?>
        <h4>TRACE</h4>
        <p><?php echo nl2br($e['trace']);?></p>
    <?php }?>
    <div class="ui clearing divider"></div>
    <div class="ui right aligned container">
        <p><a href=""><?php echo C('APP_NAME') ?></a><sup><?php echo C('APP_VERSION') ?></sup> { Fast & Simple API Framework } -- [ MANAGE YOUR API EASY ]</p>
    </div>
</div>
</body>
</html>