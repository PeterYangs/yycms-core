<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>个人生活记事</title>
    <meta http-equiv="x-ua-compatible" content="ie=7">
    <script>
        document.oncontextmenu = function (e) {
            var e = e || window.event;
            e.returnValue = false;
            return false;
        }
        window.onkeydown = function (e) {
            if (e.ctrlKey && e.keyCode == 83) {
                alert('');
                e.preventDefault();
                e.returnValue = false;
                return false;
            }
        }

        /* 这里是禁止f12*/
        window.onkeydown = window.onkeyup = window.onkeypress = function (event) {
            // 判断是否按下F12，F12键码为123
            if (event.keyCode = 123) {
                event.preventDefault(); // 阻止默认事件行为
                window.event.returnValue = false;
            }
        }

    </script>
    <style type="text/css">
        html, body {
            min-height: 101%;
            margin: 0;
            height:100%;
            width:100%;
            background: #f5f5f5;
        }
        .main {
            width: 60%;
            margin: 10% auto;
            height: 420px;
            padding: 30px;
            text-align: center;
            background: #fff
        }
        .foot {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="main">
    <h3>爱自己是一生的事</h3>
    <p>　</p>
    <p>当你心里对自己有太多的不满意时，也许让你一下爱自己有点困难。这时，请你拿出镜子，认真的看镜子在中的自己，每天对自己说，我爱你，我接纳你的一切缺点。我相信你会慢慢爱你自己！</p>
    <p>去爱自己，去发现美好！去过自己想要的生活！在你一生去经历你想要的！</p>
    <p>这样，才能不负此生！</p>
    <p>愿每个人都学会爱他自己！爱这个世界！</p>
</div>
<div class="foot">
    <p><a href="http://beian.miit.gov.cn" rel="nofollow"
          target="_blank">{{getOption('icp')}}</a></p>
</div>



</body>

</html>
