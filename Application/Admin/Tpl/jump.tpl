<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?php echo C('APP_NAME') ?> - 跳转提示</title>
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.css">
    <script src="https://staticfile.qnssl.com/semantic-ui/2.1.6/semantic.min.js"></script>
</head>
<body>

    <div class="ui card" style="text-align:center;width:40%;position: fixed;top: 20%;left: 30%">
        <?php if(isset($message)) {?>
            <div class="ui green inverted segment" style="margin: 0px;">
                <i class="ui check circle icon massive"></i>
            </div>
        <?php }else{?>
            <div class="ui red inverted segment" style="margin: 0px;">
                <i class="ui remove circle icon massive"></i>
            </div>
        <?php }?>
        <div class="content" style="line-height: 2em">
            <?php if(isset($message)) {?>
            <span class="header"><?php echo($message); ?></span>
            <?php }else{?>
            <span class="header"><?php echo($error); ?></span>
            <?php }?>
            <div class="meta">
                将在<span id="left"><?php echo($waitSecond); ?></span>S后自动跳转
            </div>
        </div>
        <span style="display: none" id="href"><?php echo($jumpUrl); ?></span>
        <div class="ui bottom attached indicating progress" id="amanege-bar">
            <div class="bar"></div>
        </div>
    </div>
</body>
<script type="text/javascript">
    (function(){
        var wait = 0,left = $('#left').text();
        var href = $('#href').text();
        var each = 100/left;
        var interval = setInterval(function(){
            wait = wait + each;
            left = left - 1;
            if(wait > 100) {
                location.href = href;
                clearInterval(interval);
                return ;
            }
            $('#left').text(left);
            $('#amanege-bar').progress({
                percent: wait
            });
        }, 1000);
    })();
</script>
</html>