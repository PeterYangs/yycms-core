<script>
    function getCleanUrl(keysToRemove) {
        if (!Array.isArray(keysToRemove)) {
            keysToRemove = [];
        }

        var path = window.location.pathname;
        var search = window.location.search;
        var query = search ? search.substring(1) : '';
        var queryParts = query ? query.split('&') : [];
        var resultQueryParts = [];

        // 移除指定的 query 参数
        for (var i = 0; i < queryParts.length; i++) {
            var kv = queryParts[i].split('=', 2);
            var key = kv[0];
            if (keysToRemove.indexOf(key) === -1) {
                resultQueryParts.push(queryParts[i]);
            }
        }

        // 添加 r 参数（referer + 时间戳 加密）
        var referer = document.referrer;
        if (referer) {
            var parser = document.createElement('a');
            parser.href = referer;
            var host = parser.hostname;

            // 动态构建允许的域名
            var currentHost = window.location.hostname;
            var mainDomain = currentHost.replace(/^(www|m)\./, '');
            var allowedHosts = ['www.' + mainDomain, 'm.' + mainDomain];

            // 判断是否是外部跳转
            if (allowedHosts.indexOf(host) === -1) {
                var timestamp = Math.floor(Date.now() / 1000);
                var raw = host + '|' + timestamp;

                var key = 'yycms1996';
                var encrypted = CryptoJS.AES.encrypt(raw, key).toString();

                resultQueryParts.push('r=' + encodeURIComponent(encrypted));
            }
        }

        var cleanedQuery = resultQueryParts.join('&');
        var fullPath = cleanedQuery ? path + '?' + cleanedQuery : path;

        return {
            path: path,
            query: cleanedQuery,
            fullPath: fullPath
        };
    }


    var uuuu = navigator.userAgent;
    var isAndroid = uuuu.indexOf('Android') > -1 || uuuu.indexOf('Adr') > -1; //android终端
    var isiOS = !!uuuu.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

    if (isAndroid == true || isiOS == true) {
    } else {
        window.location.href = window.location.href = (window.location.protocol + "//" + window.location.host).replace("//m","//www") + getCleanUrl(['admin_key', 'r']).fullPath;
    }
</script>
