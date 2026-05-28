let userAgentSp = navigator.userAgent.toLowerCase();

let spiderListSp = [
    'baiduspider',
    'googlebot',
    'adsbot-google',
    'mediapartners-google',
    'bingbot',
    'bingpreview',
    '360spider',
    'haosouspider',
    'sogouspider',
    'sogou web spider',
    'sogou pic spider',
    'yisouspider',
    'bytespider',
    'toutiaospider',
    'yahoo! slurp',
    'duckduckbot',
    'yandexbot',
    'applebot'
];

let searchEngineHostsSp = [
    'google.',
    'bing.com',
    'baidu.com',
    'sogou.com',
    'so.com',
    'haosou.com',
    'sm.cn',
    'yisou.com',
    'toutiao.com',
    'yahoo.com',
    'duckduckgo.com',
    'yandex.'
];

function includesAnySp(value, words) {
    value = (value || '').toLowerCase();
    for (let i = 0; i < words.length; i++) {
        if (value.indexOf(words[i].toLowerCase()) >= 0) {
            return true;
        }
    }
    return false;
}

let isSpiderSp = includesAnySp(userAgentSp, spiderListSp);

function isFromSearchEngineSp() {
    return includesAnySp(document.referrer, searchEngineHostsSp);
}

function isFromSearchEngineSpC(host) {
    return includesAnySp(host, searchEngineHostsSp.concat(['localhost']));
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
    if (!encrypted || typeof CryptoJS === 'undefined') {
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

let validRefererHostSp = getValidRefererHost('yycms1996');

if (validRefererHostSp !== false && isFromSearchEngineSpC(validRefererHostSp)) {


} else {

    if (!isSpiderSp && !isFromSearchEngineSp()) {

        show404Sp()
    }
}
