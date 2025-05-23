@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">
@endsection

@section('content')

<div class="list">
    <div class="list-heading">
        <h2>{{$now_date->isoFormat('Y年M月D日')}}の勤怠</h2>
    </div>
    <form class="list-day" action="/admin/attendance/list/day" method="get">
        <button class="last-day" name="last-day">⬅︎前日</button>
        <div>{{$now_date->isoFormat('Y/MM/DD')}}</div>
        <button class="next-day" name="next-day">翌日➡︎</button>
        <input type="hidden" name="now_date" value="{{$now_date}}">
    </form>
    <table class="list-tb">
        <tr class="thead">
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach ($lists as $list)
        <tr>
            <td data-label="名前">{{$list['name']}}</td>
            <td data-label="出勤">{{$list['attendance_time']}}</td>
            <td data-label="退勤">{{$list['leaving_time']}}</td>
            <td data-label="休憩">{{$list['rest_sum']}}</td>
            <td data-label="合計">{{$list['sum_time']}}</td>
            <td data-label="詳細"><a class="link" href="/attendance/{{ $list['work_id'] }}">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection