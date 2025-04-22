@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')

<div class="list">
    <div class="list-heading">
        <h2>勤怠一覧</h2>
    </div>
    <form class="list-month" action="/attendance/list/month" method="get">
        <button class="last-month" name="last-month">⬅︎前月</button>
        <div>{{$now_date->isoFormat('Y/MM')}}</div>
        <button class="next-month" name="next-month">翌月➡︎</button>
        <input type="hidden" name="now_date" value="{{$now_date}}">
    </form>
    <table class="list-tb">
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach ($works as $work)
        <tr>
            <td>{{\Carbon\Carbon::now()->format("Y年m月d日")}}</td>
            <!-- <td>{{$work->attendance_time}}</td> -->
            <td>{{substr($work->attendance_time, 11, 5)}}</td>
            <td>{{substr($work->leaving_time, 11, 5)}}</td>
            <!-- ログインユーザーの休憩終了-休憩開始 -->
            <td>{{$work->rests()->get()}}</td>
            <td>8:00</td>
            <td><a class="link" href="/attendance/{{ $work['id'] }}">詳細</a></td>
        </tr>
        @endforeach
        <tr>
            <td>06/02(金)</td>
            <td>09:00</td>
            <td>18:00</td>
            <td>1:00</td>
            <td>8:00</td>
            <td>詳細</td>
        </tr>
    </table>
</div>
@endsection