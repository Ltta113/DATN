<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Admin</title>
    @vite('resources/css/app.css')
</head>

<body class="flex">
    @include('sidebar')

    <main class="flex-1 p-6">
        @yield('content')
    </main>
</body>

</html>
