@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<div class="detail">
    <div class="detail-heading">
        <h2>勤怠詳細</h2>
    </div>
    <form action="">
        @csrf
        <table class="detail-tb">
            <tr>
                <th>名前</th>
                <td class="group">{{$list['name']}}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="group">
                    <div>{{$list['year']}}</div>
                    <div>{{$list['month_day']}}</div>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="txt-group">
                    <input type="text" class="txt" name="attendance_time" value="{{$list['attendance_time']}}">
                    <span>〜</span>
                    <input type="text" class="txt" name="leaving_time" value="{{$list['leaving_time']}}">
                </td>
            </tr>
            @for($i = 0;$i <= count($rest_count)-1;$i++)
                <!-- 休憩1回目の時は数値を表示しない -->
                @if($i == 0)
                <tr>
                    <th>休憩</th>
                    <td class="txt-group">
                        <input type="text" class="txt" name="rest_start" value="{{$list['rest_start'][$i]}}">
                        <span>〜</span>
                        <input type="text" class="txt" name="rest_finish" value="{{$list['rest_finish'][$i]}}">
                    </td>
                </tr>
                @else
                <tr>
                    <th>休憩{{$i+1}}</th>
                    <td class="txt-group">
                        <input type="text" class="txt" name="rest_start" value="{{$list['rest_start'][$i]}}">
                        <span>〜</span>
                        <input type="text" class="txt" name="rest_finish" value="{{$list['rest_finish'][$i]}}">
                    </td>
                </tr>
                @endif
                @endfor
                <tr>
                    <th>休憩{{count($rest_count)+1}}</th>
                    <td class="txt-group">
                        <input type="text" class="txt" value="">
                        <span>〜</span>
                        <input type="text" class="txt" value="">
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
    </form>
</div>
@endsection