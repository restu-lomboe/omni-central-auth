<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SSO Authorized</title>
</head>
<body>
    <p style="text-align:center;padding-top:40vh;font-family:sans-serif;color:#666;font-size:14px;">
        Authorized — menutup jendela...
    </p>

    <script>
        var ssoData = @json($sso_data);
        var origin  = @json($client_origin);

        if (window.opener) {
            window.opener.postMessage({
                source:   'omni_sso',
                sso_data: ssoData,
            }, origin);
            window.close();
        } else {
            window.location.href = origin + '/omni/callback?sso_data=' + encodeURIComponent(ssoData);
        }
    </script>
</body>
</html>