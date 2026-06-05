<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentationTitle }}</title>

```
<link rel="stylesheet" type="text/css"
      href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">

<link rel="icon" type="image/png"
      href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}"
      sizes="32x32"/>

<link rel="icon" type="image/png"
      href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}"
      sizes="16x16"/>
</head>

<body>

<div id="swagger-ui"></div>

<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>

<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>

<script>
window.onload = function () {

    const urls = [];

    @foreach($urlsToDocs as $title => $url)
        urls.push({
            name: "{{ $title }}",
            url: "{{ $url }}"
        });
    @endforeach

    const ui = SwaggerUIBundle({
        dom_id: '#swagger-ui',
        urls: urls,
        "urls.primaryName": "{{ $documentationTitle }}",

        operationsSorter:
        {!! isset($operationsSorter)
        ? '"' . $operationsSorter . '"'
        : 'null' !!},

        configUrl:
        {!! isset($configUrl)
        ? '"' . $configUrl . '"'
        : 'null' !!},

        validatorUrl:
        {!! isset($validatorUrl)
        ? '"' . $validatorUrl . '"'
        : 'null' !!},

        oauth2RedirectUrl:
        "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

        requestInterceptor: function(request){
            request.headers['X-CSRF-TOKEN'] =
                '{{ csrf_token() }}';
            return request;
        },

        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],

        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],

        layout: "StandaloneLayout",

        docExpansion:
        "{!! config('l5-swagger.defaults.ui.display.doc_expansion','none') !!}",

        deepLinking: true,

        filter:
        {!! config('l5-swagger.defaults.ui.display.filter')
        ? 'true'
        : 'false' !!},

        persistAuthorization:
        "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization')
        ? 'true'
        : 'false' !!}"
    });

    window.ui = ui;
}
</script>

</body>
</html>
