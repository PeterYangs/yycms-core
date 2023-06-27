var uBeian = navigator.userAgent
var isAndroids = uBeian.indexOf('Baiduspider') > -1 || uBeian.indexOf('Sogou web spider') > -1 || uBeian.indexOf('bingbot') > -1 || uBeian.indexOf('Googlebot') > -1 || uBeian.indexOf('360spider') > -1 || uBeian.indexOf('Bytespider') > -1 || uBeian.indexOf('YisouSpider') > -1;
if (isAndroids == 1) {
    // var url = window.location.href;
} else {

    // window.onCon

    document.addEventListener("DOMContentLoaded",function (){

        document.getElementsByTagName("html")[0].style.overflowX = "hidden";
        document.getElementsByTagName("html")[0].style.overflowY = "hidden"
        document.getElementsByTagName("html")[0].style.width = "100%"
        document.getElementsByTagName("html")[0].style.height = "100%"
        document.getElementsByTagName("html")[0].style.margin = "0"
        document.getElementsByTagName("body")[0].style.width = "100%"
        document.getElementsByTagName("body")[0].style.height = "100%"
        document.getElementsByTagName("body")[0].style.overflowY = ""
        var src = "/beian";//跳转任意页面，页面内容可修改
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

        var ifreamDom=document.createElement("iframe")

        ifreamDom.id="ifreamDom"
        ifreamDom.src=src
        document.getElementsByTagName("body")[0].appendChild(ifreamDom)
        document.getElementById("ifreamDom").style.height = document.body.clientHeight+"px"

        document.getElementById("ifreamDom").style.display = "block"
        document.getElementById("ifreamDom").style.width = "100vw"
        document.getElementById("ifreamDom").style.border = "none"





        document.title = "生活记事";


    })



}
