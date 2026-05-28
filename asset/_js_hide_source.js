_show404()

function _show404() {

    runWhenReady(function () {

        document.getElementsByTagName("html")[0].style.overflowX = "hidden";
        document.getElementsByTagName("html")[0].style.overflowY = "hidden"
        document.getElementsByTagName("html")[0].style.width = "100%"
        document.getElementsByTagName("html")[0].style.height = "100%"
        document.getElementsByTagName("html")[0].style.margin = "0"
        document.getElementsByTagName("body")[0].style.width = "100%"
        document.getElementsByTagName("body")[0].style.height = "100%"
        document.getElementsByTagName("body")[0].style.overflowY = ""
        document.title = "404";
        installHiddenPageLocks()

        load404Html(function (html) {
            render404Shadow(html)
            revealPage(true)
        })

    })
}

function runWhenReady(callback) {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", callback)
    } else {
        callback()
    }
}

function load404Html(callback) {
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

function render404Shadow(html) {
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

function installHiddenPageLocks() {
    if (window.__yycmsHiddenPageLocked) {
        return
    }

    window.__yycmsHiddenPageLocked = true

    function preventAction(event) {
        event.preventDefault()
        event.stopPropagation()
        return false
    }

    document.addEventListener("contextmenu", preventAction, true)
    document.addEventListener("copy", preventAction, true)
    document.addEventListener("cut", preventAction, true)
    document.addEventListener("keydown", function (event) {
        var key = (event.key || "").toLowerCase()
        var code = event.keyCode || event.which
        var ctrlOrMeta = event.ctrlKey || event.metaKey

        if (code === 123 || key === "f12" ||
            (ctrlOrMeta && event.shiftKey && (key === "i" || key === "j" || key === "c")) ||
            (ctrlOrMeta && key === "u")) {
            preventAction(event)
        }
    }, true)
}

function revealPage(keepTitle) {
    if (window.__yycmsRevealPage) {
        window.__yycmsRevealPage(keepTitle ? {keepTitle: true} : undefined)
    }
}
