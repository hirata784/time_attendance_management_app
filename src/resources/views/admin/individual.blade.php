@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/individual.css') }}">
@endsection

@section('content')
<div class="list">
    <div class="list-heading">
        <h2><span>{{$user_name}}</span><span>さんの勤怠</span></h2>
    </div>
    <form class="list-month" action="/admin/attendance/staff/{{$user_id}}/month" method="get">
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
        @foreach ($lists as $list)
        <tr>
            <td>{{$list['date']}}</td>
            <td data-label="出勤">{{$list['attendance_time']}}</td>
            <td data-label="退勤">{{$list['leaving_time']}}</td>
            <td data-label="休憩">{{$list['rest_sum']}}</td>
            <td data-label="合計">{{$list['sum_time']}}</td>
            <td data-label="詳細"><a class="link" href="/attendance/{{ $list['work_id'] }}">詳細</a></td>
        </tr>
        @endforeach
    </table>
    <div class="correction-btn">
        <button class="btn">CSV出力</button>
    </div>
</div>
@endsection