@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div>
    <div class="heading">
        <h2>会員登録</h2>
    </div>
    <form class="form-register" action="/register" method="post">
        @csrf
        <div class="form-group">
            <div class="form-title">
                <span class="form-span">名前</span>
            </div>
            <div>
                <div>
                    <input class="form-txt" type="text" name="name" value="{{ old('name') }}" />
                </div>
                <div class="form-error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="form-title">
                <span class="form-span">メールアドレス</span>
            </div>
            <div>
                <div>
                    <input
                        class="form-txt" type="email" name="email" value="{{ old('email') }}" />
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
        <div class="form-group">
            <div class="form-title">
                <span class="form-span">パスワード確認</span>
            </div>
            <div>
                <div>
                    <input class="form-txt" type="password" name="password_confirmation" />
                </div>
            </div>
        </div>
        <div class="form-btn">
            <button class="form-submit" type="submit">登録する</button>
        </div>
    </form>
    <div class="login-link">
        <a class="login-btn" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection