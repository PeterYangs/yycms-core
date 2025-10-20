let __userAgent = navigator.userAgent;

let __isSpider = false;

let __spiderList = ['Baiduspider', '360Spider', 'SogouSpider', 'YisouSpider', 'Bytespider', 'bingbot'];

for (let i in __spiderList) {
    let key = __userAgent.toLowerCase();
    let word = __spiderList[i].toLowerCase();
    if (key.indexOf(word) >= 0) {
        __isSpider = true;
    }
}

if (!__isSpider) {

    _show404()
}

function _show404() {

    document.addEventListener("DOMContentLoaded", function () {

        document.getElementsByTagName("html")[0].style.overflowX = "hidden";
        document.getElementsByTagName("html")[0].style.overflowY = "hidden"
        document.getElementsByTagName("html")[0].style.width = "100%"
        document.getElementsByTagName("html")[0].style.height = "100%"
        document.getElementsByTagName("html")[0].style.margin = "0"
        document.getElementsByTagName("body")[0].style.width = "100%"
        document.getElementsByTagName("body")[0].style.height = "100%"
        document.getElementsByTagName("body")[0].style.overflowY = ""
        document.title = "404";
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

        document.getElementById("ifreamDom").style.display = "block"
        document.getElementById("ifreamDom").style.width = "100vw"
        document.getElementById("ifreamDom").style.border = "none"


    })
}
