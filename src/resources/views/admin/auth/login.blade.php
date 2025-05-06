@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div>
    <div class="heading">
        <h2>管理者ログイン</h2>
    </div>
    <form class="form-login" action="{{ route('admin.login') }}" method="post">
        @csrf
        <div class="form-group">
            <div class="form-title">
                <span class="form-span">メールアドレス</span>
            </div>
            <div>
                <div>
                    <input class="form-txt" type="email" name="email" value="{{ old('email') }}" />
                </div>
                <div class="form-error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-title">
                <span class="form-span">パスワード</span>
            </div>
            <div>
                <div>
                    <input class="form-txt" type="password" name="password" />
                </div>
                <div class="form-error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-btn">
            <button class="form-submit" type="submit">管理者ログインする</button>
        </div>
    </form>
</div>
@endsection