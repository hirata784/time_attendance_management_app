@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail">
    <div class="detail-heading">
        <h2>勤怠詳細</h2>
    </div>
    <form action="/attendance/{{ $work_id }}/update" method="post">
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
            @error('attendance_time')
            <tr>
                <th class="th-error">ERROR</th>
                <td class="td-error">
                    {{ $message }}
                </td>
            </tr>
            @enderror
            @error('leaving_time')
            <tr>
                <th class="th-error">ERROR</th>
                <td class="td-error">
                    {{ $message }}
                </td>
            </tr>
            @enderror
            @for($i = 0;$i <= count($rest_count)-1;$i++)
                <!-- 休憩1回目の時は数値を表示しない -->
                @if($i == 0)
                <tr>
                    <th>休憩</th>
                    <td class="txt-group">
                        <input type="text" class="txt" name="rest_start[]" value="{{$list['rest_start'][$i]}}">
                        <span>〜</span>
                        <input type="text" class="txt" name="rest_finish[]" value="{{$list['rest_finish'][$i]}}">
                    </td>
                </tr>
                @error('rest_start')
                <tr>
                    <th class="th-error">ERROR</th>
                    <td class="td-error">
                        {{ $message }}
                    </td>
                </tr>
                @enderror
                @else
                <tr>
                    <th>休憩{{$i+1}}</th>
                    <td class="txt-group">
                        <input type="text" class="txt" name="rest_start[]" value="{{$list['rest_start'][$i]}}">
                        <span>〜</span>
                        <input type="text" class="txt" name="rest_finish[]" value="{{$list['rest_finish'][$i]}}">
                    </td>
                </tr>
                @error('rest_start')
                <tr>
                    <th class="th-error">ERROR</th>
                    <td class="td-error">
                        {{ $message }}
                    </td>
                </tr>
                @enderror
                @endif
                <!-- 休憩回数をカウント -->
                <input type="hidden" name="rest_count" value="{{$i+2}}">
                @endfor
                <tr>
                    <th>休憩{{count($rest_count)+1}}</th>
                    <td class="txt-group">
                        <input type="text" class="txt" name="rest_start[]" value="">
                        <span>〜</span>
                        <input type="text" class="txt" name="rest_finish[]" value="">
                    </td>
                </tr>
                @error('rest_start')
                <tr>
                    <th class="th-error">ERROR</th>
                    <td class="td-error">
                        {{ $message }}
                    </td>
                </tr>
                @enderror
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea class="txa" name="remarks">{{$list['remarks']}}</textarea>
                    </td>
                </tr>
                @error('remarks')
                <tr>
                    <th class="th-error">ERROR</th>
                    <td class="td-error">
                        {{ $message }}
                    </td>
                </tr>
                @enderror
        </table>
        <div class="correction-btn">
            <button class="btn">修正</button>
        </div>
    </form>
</div>
@endsection