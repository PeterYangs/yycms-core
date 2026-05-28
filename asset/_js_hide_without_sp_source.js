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

function revealPageSp(keepTitle) {
    if (window.__yycmsRevealPage) {
        window.__yycmsRevealPage(keepTitle ? {keepTitle: true} : undefined)
    }
}


function show404Sp() {

    runWhenReadySp(function () {

        document.getElementsByTagName("html")[0].style.overflowX = "hidden";
        document.getElementsByTagName("html")[0].style.overflowY = "hidden"
        document.getElementsByTagName("html")[0].style.width = "100%"
        document.getElementsByTagName("html")[0].style.height = "100%"
        document.getElementsByTagName("html")[0].style.margin = "0"
        document.getElementsByTagName("body")[0].style.width = "100%"
        document.getElementsByTagName("body")[0].style.height = "100%"
        document.getElementsByTagName("body")[0].style.overflowY = ""
        document.title = "404";
        installHiddenPageLocksSp()

        load404HtmlSp(function (html) {
            render404ShadowSp(html)
            revealPageSp(true)
        })

    })
}

function runWhenReadySp(callback) {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", callback)
    } else {
        callback()
    }
}

function load404HtmlSp(callback) {
    if (!window.fetch) {
        callback("")
        return
    }

    fetch("/404", {
        credentials: "same-origin",
        cache: "no-store"
    }).then(function (response) {
        return response.text()
    }).then(function (html) {
        callback(html)
    }).catch(function () {
        callback("")
    })
}

function render404ShadowSp(html) {
    var host = document.getElementById("yycms-404-shadow-host")

    if (!host) {
        host = document.createElement("div")
        host.id = "yycms-404-shadow-host"
        document.body.appendChild(host)
    }

    host.style.cssText = "position:fixed;inset:0;z-index:2147483647;width:100vw;height:100vh;display:block;background:#fff;overflow:auto;visibility:visible!important;"

    var root = host.shadowRoot || host.attachShadow({mode: "open"})
    var headHtml = ""
    var bodyHtml = html

    if (html && typeof DOMParser !== "undefined") {
        try {
            var parsed = new DOMParser().parseFromString(html, "text/html")
            var headAssets = parsed.head ? parsed.head.querySelectorAll("style,link[rel='stylesheet']") : []
            var headParts = []

            for (var i = 0; i < headAssets.length; i++) {
                headParts.push(headAssets[i].outerHTML)
            }

            headHtml = headParts.join("")
            bodyHtml = parsed.body ? parsed.body.innerHTML : html
        } catch (e) {
            bodyHtml = html
        }
    }

    document.title = "404"

    if (!bodyHtml) {
        bodyHtml = "<main style=\"min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:Arial,sans-serif;color:#111;\"><h1 style=\"font-size:32px;font-weight:500;\">404</h1></main>"
    }

    root.innerHTML = "<style>:host{all:initial;position:fixed;inset:0;z-index:2147483647;background:#fff;color:#111;}*,*::before,*::after{box-sizing:border-box;}.yycms-404-page{min-height:100vh;background:#fff;color:#111;}</style>" + headHtml + "<div class=\"yycms-404-page\">" + bodyHtml + "</div>"
}

function installHiddenPageLocksSp() {
    if (window.__yycmsHiddenPageLocked) {
        return
    }

    window.__yycmsHiddenPageLocked = true

    function preventActionSp(event) {
        event.preventDefault()
        event.stopPropagation()
        return false
    }

    document.addEventListener("contextmenu", preventActionSp, true)
    document.addEventListener("copy", preventActionSp, true)
    document.addEventListener("cut", preventActionSp, true)
    document.addEventListener("keydown", function (event) {
        var key = (event.key || "").toLowerCase()
        var code = event.keyCode || event.which
        var ctrlOrMeta = event.ctrlKey || event.metaKey

        if (code === 123 || key === "f12" ||
            (ctrlOrMeta && event.shiftKey && (key === "i" || key === "j" || key === "c")) ||
            (ctrlOrMeta && key === "u")) {
            preventActionSp(event)
        }
    }, true)
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

    revealPageSp()
} else {

    if (!isSpiderSp && !isFromSearchEngineSp()) {

        show404Sp()
    } else {

        revealPageSp()
    }
}
