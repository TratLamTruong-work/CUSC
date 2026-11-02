<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản lý công việc')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark app-navbar">
    <div class="container app-container">
        <a class="navbar-brand fw-semibold" href="{{ route('tasks.index') }}">Quản lý công việc</a>
        @auth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tasks.index') }}">Danh sách công việc</a>
                    </li>
                </ul>
                <div class="d-flex flex-wrap align-items-center gap-2 py-2 py-md-0">
                    <span class="text-white">Xin chào, <strong>{{ auth()->user()->username }}</strong></span>
                    <span class="badge {{ auth()->user()->isAdmin() ? 'text-bg-warning' : 'text-bg-light' }}">{{ auth()->user()->roleLabel() }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Đăng xuất</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>

<main class="container app-container py-4">
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
