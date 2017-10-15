<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Laravel 5.5 with Angular 4</title>
    <base href="/">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    @prod
    @ngStyle(styles)
    @endprod
</head>
<body class="hold-transition skin-blue sidebar-mini">
<deo-root></deo-root>

@ngScript(inline)
@ngScript(polyfills)

@local
@ngScript(styles)
@endlocal

@ngScript(scripts)
@ngScript(vendor)
@ngScript(main)

</body>
</html>
