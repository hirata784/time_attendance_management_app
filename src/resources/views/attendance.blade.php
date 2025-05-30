@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance">
    @if($work_status == "1")
    <form class="attendance-form" action="/attendance/store" method="post">
        @csrf
        <div class="work-status">
            <p>勤務外</p>
        </div>
        <div class="now-date">
            <p>{{$now_date}}</p>
        </div>
        <div class="now-time">
            <p id="RealtimeClockArea2">{{$now_time}}</p>
        </div>
        <div>
            <button class="btn-black">出勤</button>
        </div>
    </form>
    @elseif($work_status == "2")
    <form class="attendance-form" action="/attendance/update_work" method="post">
        @csrf
        <div class="work-status">
            <p>出勤中</p>
        </div>
        <div class="now-date">
            <p>{{$now_date}}</p>
        </div>
        <div class="now-time">
            <p id="RealtimeClockArea2">{{$now_time}}</p>
        </div>
        <div class="btn">
            <button name="work" class="btn-black">退勤</button>
            <button name="rest" class="btn-white">休憩入</button>
        </div>
        <!-- コントローラーに時間のみ渡す -->
        <input type="hidden" name="now_time" value="{{$now_time}}">
    </form>
    @elseif($work_status == "3")
    <form class="attendance-form" action="/attendance/update_rest" method="post">
        @csrf
        <div class="work-status">
            <p>休憩中</p>
        </div>
        <div class="now-date">
            <p>{{$now_date}}</p>
        </div>
        <div class="now-time">
            <p id="RealtimeClockArea2">{{$now_time}}</p>
        </div>
        <div>
            <button class="btn-white">休憩戻</button>
        </div>
        <!-- コントローラーに時間のみ渡す -->
        <input type="hidden" name="now_time" value="{{$now_time}}">
    </form>
    @elseif($work_status == "4")
    <div class="work-status">
        <p>退勤済</p>
    </div>
    <div class="now-date">
        <p>{{$now_date}}</p>
    </div>
    <div class="now-time">
        <p id="RealtimeClockArea2">{{$now_time}}</p>
    </div>
    <div class="message">
        お疲れ様でした。
    </div>
    @endif
</div>

<script type="text/javascript">
    function set2fig(num) {
        // 桁数が1桁だったら先頭に0を加えて2桁に調整する
        var ret;
        if (num < 10) {
            ret = "0" + num;
        } else {
            ret = num;
        }
        return ret;
    }

    function showClock2() {
        var nowTime = new Date();
        var nowHour = set2fig(nowTime.getHours());
        var nowMin = set2fig(nowTime.getMinutes());
        var nowSec = set2fig(nowTime.getSeconds() + 1);
        var msg = nowHour + ":" + nowMin;
        document.getElementById("RealtimeClockArea2").innerHTML = msg;
    }
    setInterval('showClock2()', 1000);
</script>
@endsection