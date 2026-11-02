@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<div class="panel auth-panel">
    <h1 class="h3 mb-4">Đăng nhập</h1>

    @error('login')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus
                   class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required
                   class="form-control @error('password') is-invalid @enderror">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
    </form>

    <p class="text-center mt-4 mb-0">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
</div>
@endsection
