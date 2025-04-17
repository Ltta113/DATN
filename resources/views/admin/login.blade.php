<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Admin</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-300 bg-opacity-60 min-h-screen flex items-center justify-center"
    style="background-image: url('https://res.cloudinary.com/dswj1rtvu/image/upload/v1744021608/BookStore/picture_fwmwiy.png'); background-position: left top; background-size: cover;">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg flex flex-col lg:flex-row overflow-hidden">

        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12 bg-white">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Chào mừng trở lại với trang quản lý</h1>
                <p class="text-base text-gray-500">Đăng nhập để tiếp tục hành trình khám phá tri thức</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 p-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Đăng nhập tài khoản</h2>

            @if (session('error'))
                <div class="mb-4 p-2 text-center text-red-600 bg-red-50 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="text" class="block text-sm font-medium text-gray-700 text-left mb-1">Tài khoản</label>
                    <div class="relative">
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-left" required>
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-pencil"></i>
                        </div>
                    </div>
                    @error('username')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 text-left mb-1">Mật
                        khẩu</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-left" required>
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer">
                            <i class="fas fa-eye"></i> {{-- Bạn có thể dùng AlpineJS để toggle kiểu hiển thị mật khẩu nếu muốn --}}
                        </div>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                    Đăng nhập
                </button>
            </form>

        </div>
    </div>
</body>

</html>
