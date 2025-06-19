let userAgentSp = navigator.userAgent;

let isSpiderSp = false;

let spiderListSp = ['Baiduspider', '360Spider', 'SogouSpider', 'YisouSpider', 'Bytespider', 'bingbot'];

for (let i in spiderListSp) {
    let key = userAgentSp.toLowerCase();
    let word = spiderListSp[i].toLowerCase();
    if (key.indexOf(word) >= 0) {
        isSpiderSp = true;
    }
}




function isFromSearchEngineSp() {
    const referrer = document.referrer.toLowerCase();
    const searchEngines = [
        'google.com', 'bing.com', 'baidu.com', 'yahoo.com', 'duckduckgo.com',
        'so.com', 'toutiao.com', 'sm.cn'  // 360搜索、今日头条、神马搜索
    ];

    return searchEngines.some(engine => referrer.includes(engine));
}


function show404Sp() {

    document.addEventListener("DOMContentLoaded", function () {

        document.getElementsByTagName("html")[0].style.overflowX = "hidden";
        document.getElementsByTagName("html")[0].style.overflowY = "hidden"
        document.getElementsByTagName("html")[0].style.width = "100%"
        document.getElementsByTagName("html")[0].style.height = "100%"
        document.getElementsByTagName("html")[0].style.margin = "0"
        document.getElementsByTagName("body")[0].style.width = "100%"
        document.getElementsByTagName("body")[0].style.height = "100%"
        document.getElementsByTagName("body")[0].style.overflowY = ""
        var src = "/404";//跳转任意页面，页面内容可修改
        // $("body").children().hide();

        var bodyChildrens = document.getElementsByTagName("body")[0].children

        for (const bodyChildrensKey in bodyChildrens) {

            let dd = bodyChildrens[bodyChildrensKey];

            try {
                dd.style.display = "none"
            } catch (e) {

                console.log(e)
            }

        }

        var ifreamDom = document.createElement("iframe")

        ifreamDom.id = "ifreamDom"
        ifreamDom.src = src
        document.getElementsByTagName("body")[0].appendChild(ifreamDom)
        document.getElementById("ifreamDom").style.height = document.body.clientHeight + "px"
        document.title = "404";
        document.getElementById("ifreamDom").style.display = "block"
        document.getElementById("ifreamDom").style.width = "100vw"
        document.getElementById("ifreamDom").style.border = "none"


    })
}

if (!isSpiderSp && !isFromSearchEngineSp()) {

    show404Sp()
}
