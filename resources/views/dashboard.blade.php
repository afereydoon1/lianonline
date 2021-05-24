<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ !empty($pageTitle) ? $pageTitle : '' }}</title>
    <link rel="stylesheet" href="{{ \App\Helpers\UrlHelper::vAsset('libraries/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ \App\Helpers\UrlHelper::vAsset('css/app.css') }}">
    <link rel="stylesheet" href="{{ \App\Helpers\UrlHelper::vAsset('libraries/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ \App\Helpers\UrlHelper::vAsset('libraries/Select2/css/select2.min.css') }}">
</head>

<body>
<main class="dashboard" :class="{'open' : asideStatus}" id="lian-app">
    <sidebar v-if="asideViewable"></sidebar>
    <content-viewer class="main">
        <router-view></router-view>
    </content-viewer>
</main>
<script src="{{ \App\Helpers\UrlHelper::vAsset('libraries/jquery/jquery.min.js') }}"></script>
<script src="{{ \App\Helpers\UrlHelper::vAsset('libraries/Select2/js/select2.full.js') }}"></script>
<script src="{{ \App\Helpers\UrlHelper::vAsset('js/app.js') }}"></script>
</body>
</html>
