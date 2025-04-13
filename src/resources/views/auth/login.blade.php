@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div>
    <div class="heading">
        <h2>ログイン</h2>
    </div>
    <form class="form-login" action="/login" method="post">
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
            <button class="form-submit" type="submit">ログインする</button>
        </div>
    </form>
    <div class="register-link">
        <a class="register-btn" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection