{{-- Meta Tags --}}
<meta content="charset=UTF-8">
<meta http-equiv="Content-Type" content="text/html">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, viewport-fit=cover">
<meta name="theme-color" content="{{isAdminRoute() ? '#171529' : '#d3215a'}}">
<meta name="title" content="{{config('app.name')}} - Dress Up Elegantly">
<meta name="author" content="{{config('app.name')}}">
<meta name="language" content="English">
<meta name="robots" content="index,follow">
<meta name="revisit-after" content="2 days">
<meta name="description" content="Our artisans craft all of Grace's garments and accessories according to the highest quality standards. Our latest collection is a combination of our designers' vision and our technical experts' inventions.">
<meta name="keywords" content="grace, clothes, cloth, fashion, closet, wear, dress, cotton, jackets, accessories, pants, bags, sweatshirts, shirts, shoes, men, women, kids, amazon, egypt, jordan">
<meta name="csrf-token" content="{{csrf_token()}}">

<!--======================================================-->

{{-- Link Tags --}}
<link rel="canonical" href="{{canonicalUrl()}}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" type="text/css" href="https://unpkg.com/@icon/themify-icons/themify-icons.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.min.css">
@if (isAdminRoute())
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/aksfileupload@1.0.0/dist/aksFileUpload.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@40,400,1,0" />
{{--    <link rel="stylesheet" type="text/css" href="{{asset('css/admin-style.css')}}">--}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/yewess97/Grace@main/public/css/admin-style.css">
    @if (isset($error_status) && $error_status === 403)
        <link rel="stylesheet" type="text/css" href="{{asset('css/styles/responsive/user-responsive.css')}}">
    @endif
@else
    <link rel="stylesheet" type="text/css" href="{{asset('css/user-style.css')}}">
@endif
<link rel="icon" type="image/x-icon" sizes="32x32" href="{{imageSource('favicon.png')}}">
<link rel="icon" type="image/x-icon" sizes="16x16" href="{{imageSource('favicon.png')}}">
<link rel="shortcut icon" type="image/x-icon" sizes="32x32" href="{{imageSource('favicon.png')}}">
<link rel="shortcut icon" type="image/x-icon" sizes="16x16" href="{{imageSource('favicon.png')}}">
<link rel="apple-touch-icon" sizes="180x180" href="{{imageSource('favicon.png')}}">
<link rel="manifest" href="{{asset('manifest.json')}}">
