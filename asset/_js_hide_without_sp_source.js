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

function isFromSearchEngineSpC(host) {
    const searchEngines = [
        'google.com', 'bing.com', 'baidu.com', 'yahoo.com', 'duckduckgo.com',
        'so.com', 'toutiao.com', 'sm.cn', 'localhost'  // 360搜索、今日头条、神马搜索
    ];
    return searchEngines.some(engine => host.includes(engine));
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

function getValidRefererHost(secretKey) {
    function getQueryParam(name) {
        var search = window.location.search.substring(1);
        var parts = search.split('&');
        for (var i = 0; i < parts.length; i++) {
            var kv = parts[i].split('=');
            if (kv[0] === name) {
                return decodeURIComponent(kv[1] || '');
            }
        }
        return '';
    }

    var encrypted = getQueryParam('r');
    if (!encrypted) {
        return false;
    }

    try {
        var bytes = CryptoJS.AES.decrypt(encrypted, secretKey);
        var decrypted = bytes.toString(CryptoJS.enc.Utf8);

        if (!decrypted || decrypted.indexOf('|') === -1) {
            return false;
        }

        var parts = decrypted.split('|');
        if (parts.length !== 2) {
            return false;
        }

        var host = parts[0];
        var timestamp = parseInt(parts[1], 10);
        if (!host || isNaN(timestamp)) {
            return false;
        }

        var now = Math.floor(Date.now() / 1000);
        var diff = Math.abs(now - timestamp);

        return diff <= 600 ? host : false;
    } catch (e) {
        return false;
    }
}


if (getValidRefererHost('yycms1996') !== false && isFromSearchEngineSpC(getValidRefererHost('yycms1996'))) {


} else {

    if (!isSpiderSp && !isFromSearchEngineSp()) {

        show404Sp()
    }
}



