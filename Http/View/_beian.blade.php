<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>生活记录</title>
    <meta name="viewport" content="width=1200">
    <style>
        body {
            margin: 0;
            font-family: "微软雅黑", "Arial", sans-serif;
            background: #fff;
            color: #222;
        }
        .container {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
        }
        .left {
            flex: 1.2;
            background: url('https://s3.bmp.ovh/imgs/2025/04/28/4e9ff470fff8abae.jpeg') center center/cover no-repeat;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 0 0 60px 40px;
            color: #fff;
        }
        .left h1 {
            font-size: 2.5em;
            margin: 0 0 10px 0;
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .left p {
            font-size: 1.1em;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .right {
            flex: 2;
            background: #fff;
            padding: 40px 0 0 0;
            display: flex;
            flex-direction: column;
            min-width: 600px;
        }
        .right h2 {
            margin-left: 40px;
            font-size: 1.6em;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .articles {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin-left: 40px;
            margin-right: 40px;
        }
        .article {
            display: flex;
            flex-direction: row;
            background: #fafafa;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .article:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        }
        .article-img {
            width: 180px;
            height: 120px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .article-content {
            padding: 18px 24px 18px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex: 1;
        }
        .article-title {
            font-size: 1.15em;
            font-weight: bold;
            margin-bottom: 8px;
            color: #222;
        }
        .article-desc {
            font-size: 1em;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .article-date {
            font-size: 0.95em;
            color: #aaa;
            align-self: flex-end;
        }
        footer {
            width: 100vw;
            background: #111;
            color: #fff;
            text-align: center;
            font-size: 0.95em;
            padding: 16px 0 12px 0;
            position: fixed;
            left: 0;
            bottom: 0;
            z-index: 10;
            letter-spacing: 1px;
        }
        @media (max-width: 1100px) {
            .container {
                flex-direction: column;
            }
            .left, .right {
                min-width: 0;
                width: 100%;
                padding: 30px 10px 30px 10px;
            }
            .right {
                padding-top: 10px;
            }
            .articles {
                margin: 0 10px;
            }
        }
    </style>
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
</head>
<body>
<div class="container">
    <div class="left">
        <h1>生活记录</h1>
        <p>淡淡的日子，淡淡的心境，淡淡的阳光，淡淡的风，凡事淡淡的，就好</p>
    </div>
    <div class="right">
        <h2>最新文章</h2>
        <div class="articles">
            <div class="article">
                <img class="article-img" src="https://s3.bmp.ovh/imgs/2025/04/28/4e9ff470fff8abae.jpeg" alt="文章图片1">
                <div class="article-content">
                    <div>
                        <div class="article-title">坚持，是通向远方的路</div>
                        <div class="article-desc">人生如同一场长途跋涉，有时阳光明媚，有时风雨交加。重要的不是你走得多快，而是你有没有坚持在走。真正的成长，不是外在的积累，而是内心的沉淀。每一次跌倒，每一次失望，都是通往成熟的必经之路。不要急于看到结果，所有的坚持都会在未来的某一刻开花结果，只要你不停步，总会抵达心中的远方。</div>
                    </div>
                    <div class="article-date">2024-05-09</div>
                </div>
            </div>
            <div class="article">
                <img class="article-img" src="https://s3.bmp.ovh/imgs/2025/04/28/9812cd07d487f325.jpeg" alt="文章图片2">
                <div class="article-content">
                    <div>
                        <div class="article-title">决定命运的是坚持</div>
                        <div class="article-desc">人与人之间最大的差距，不在起点，而在于是否能坚持到最后。有的人聪明但急功近利，稍有波折便放弃；有的人平凡却笃定，默默耕耘，最终脱颖而出。世界不会辜负每一份用心生活的人，命运也不会吝啬对每一位脚踏实地者的奖赏。真正的赢家，是那些在沉寂中积蓄力量，在暗夜中守望希望的人。</div>
                    </div>
                    <div class="article-date">2024-05-09</div>
                </div>
            </div>
            <div class="article">
                <img class="article-img" src="https://s3.bmp.ovh/imgs/2025/04/28/d0e0da5eafd14a68.jpeg" alt="文章图片3">
                <div class="article-content">
                    <div>
                        <div class="article-title">平和，是人生最好的修行</div>
                        <div class="article-desc">生活中，最难得的是一种平和的心态。当你不再执着于一时得失，也就拥有了面对风雨的从容。世界喧嚣纷杂，唯有内心安定，才能看到真实的自己。焦虑来源于比较，痛苦源于执念。学会与自己和解，尊重自己的节奏，慢慢走，也能走到远方。与其在意别人的眼光，不如专注于自己脚下的路。</div>
                    </div>
                    <div class="article-date">2024-05-09</div>
                </div>
            </div>
            <div class="article">
                <img class="article-img" src="https://s3.bmp.ovh/imgs/2025/04/28/39e9d624b71d380f.jpeg" alt="文章图片4">
                <div class="article-content">
                    <div>
                        <div class="article-title">做一个温暖而坚定的人</div>
                        <div class="article-desc">人这一生，重要的不是拥有什么，而是成为怎样的人。财富、地位、名声，都可能随时消散，唯有人格的光芒能穿越时间，照亮自己，也温暖他人。真正的成功，不是站在众人之上，而是活得坦荡，活得自洽。当你能够在孤独中自得，在困难中坚韧，在平凡中微笑，那才是真正的强大与智慧。</div>
                    </div>
                    <div class="article-date">2024-05-09</div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer style="height: 60px">
    <a target="_blank" href="https://beian.miit.gov.cn/" style="color: white">{{getOption('icp')}}</a>
</footer>
</body>
</html>
