<script>
    var uuuu = navigator.userAgent;
    var isAndroid = uuuu.indexOf('Android') > -1 || uuuu.indexOf('Adr') > -1; //android终端
    var isiOS = !!uuuu.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

    if (isAndroid == true || isiOS == true) {
    } else {
        window.location.href = '{{getOption("domain").(parse_url(request()->fullUrlWithoutQuery('admin_key'))['path']??"").((parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"")?"?".parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"":"")}}';
    }
</script>
