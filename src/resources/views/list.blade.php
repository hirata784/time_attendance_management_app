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
        <tr class="thead">
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach ($works as $work)
        <tr>
            <!-- <td>{{\Carbon\Carbon::now()->format('Y/m')}}</td> -->
            <td>{{\Carbon\Carbon::parse($work->attendance_time)->isoFormat('MM/DD(ddd)')}}</td>
            <td data-label="出勤">{{substr($work->attendance_time, 11, 5)}}</td>
            <td data-label="退勤">{{substr($work->leaving_time, 11, 5)}}</td>
            <td data-label="休憩">{{$work->rest_sum}}</td>
            <td data-label="合計">{{$work->sum_time}}</td>
            <td data-label="詳細"><a class="link" href="/attendance/{{ $work['id'] }}">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection