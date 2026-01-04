<meta charset="utf-8">
<meta name="author" content="Jari POS">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Jari Pos adalah platform Point of Sales (POS) yang memudahkan pengelolaan transaksi dan operasional bisnis Anda secara efisien dan terintegrasi.">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Fav Icon  -->
<link rel="shortcut icon" href="{{ asset('images/brand-logo.svg') }}">

<!-- Page Title  -->
<title>{{ isset($title) ? $title : 'Jari POS' }}</title>

<!-- StyleSheets  -->
@vite(['resources/css/dashlite.css', 'resources/css/theme.css'])