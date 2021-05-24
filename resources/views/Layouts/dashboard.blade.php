!DOCTYPE html>
<html dir="rtl" lang="fa-IR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $page_title }}</title>
    <!-- GENERAL -->
    <link type="text/css" rel="stylesheet" href="/plugins/bootstrap/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="/plugins/fontawesome/css/all.min.css">
    <link type="text/css" rel="stylesheet" href="/css/main.css">

    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/js/pace.min.js"></script>
</head>
<body>
<section id="lian-app">
    @if (!Auth()->guard('admin-api')->check())
        @include('Dashboard.auth');
    @endif
</section>
<!-- GENERAL -->
<script type="text/javascript" src="/plugins/bootstrap/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="/plugins/sweetalert/sweetalert.min.js"></script>
<script type="text/javascript" src="/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="/js/jquery.touchswipe.min.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
</body>
</html>
