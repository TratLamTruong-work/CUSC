@extends('layouts.app')
@section('title', 'Đăng ký')

@section('content')
<div class="panel auth-panel">
    <h1 class="h3 mb-4">Đăng ký tài khoản</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" minlength="3" maxlength="50"
                   autocomplete="username" required autofocus class="form-control @error('username') is-invalid @enderror">
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="255"
                   autocomplete="email" required class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required
                   aria-describedby="passwordHelp" class="form-control @error('password') is-invalid @enderror">
            <div id="passwordHelp" class="form-text">Tối thiểu 8 ký tự.</div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required
                   class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
    </form>

    <p class="text-center mt-4 mb-0">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
</div>
@endsection
