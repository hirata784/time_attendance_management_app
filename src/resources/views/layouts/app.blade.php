<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-utilities">
                <img src="{{ asset('storage/images/logo.svg') }}" width="300" height="80">
                <div class="hamburger" id="hamburger">
                    <div class="icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <nav class="sm">
                    <ul class="nav-items">
                        @if (Auth::check())
                        <li class="nav-item">
                            <form action="">
                                @csrf
                                <button class="nav-btn">勤怠</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <form action="">
                                @csrf
                                <button class="nav-btn">勤怠一覧</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <form action="">
                                @csrf
                                <button class="nav-btn">申請</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="nav-btn">ログアウト</button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </nav>
                <nav class="pc">
                    <ul class="nav-items">
                        @if (Auth::check())
                        <li class="nav-item">
                            <form action="">
                                @csrf
                                <button class="nav-btn">勤怠</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <form action="">
                                @csrf
                                <button class="nav-btn">勤怠一覧</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <form action="">
                                @csrf
                                <button class="nav-btn">申請</button>
                            </form>
                        </li>
                        <li class="nav-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="nav-btn">ログアウト</button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#hamburger').on('click', function() {
            $('.icon').toggleClass('close');
            $('.sm').slideToggle();
        });
    </script>
    <main>
        @yield('content')
    </main>
</body>

</html>