@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<div class="detail">
    <div class="detail-heading">
        <h2>勤怠詳細</h2>
    </div>
    <table class="detail-tb">
        <tr>
            <th>名前</th>
            <td>{{$user[0]->name}}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td>
                <div>{{substr($work[$id]->attendance_time, 0, 4)}}年</div>
                <div>{{substr($work[$id]->attendance_time, 5, 2)}}月{{substr($work[$id]->attendance_time, 8, 2)}}日</div>
            </td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>
                <input type="text" class="txt">
                <span>〜</span>
                <input type="text" class="txt">
            </td>
        </tr>
        <tr>
            <th>休憩</th>
            <td>
                <input type="text" class="txt">
                <span>〜</span>
                <input type="text" class="txt">
            </td>
        </tr>
        <tr>
            <th>備考</th>
            <td>
                <textarea class="txa" name="" id=""></textarea>
            </td>
        </tr>
    </table>
    <div class="correction-btn">
        <button class="btn">修正</button>
    </div>
</div>
@endsection