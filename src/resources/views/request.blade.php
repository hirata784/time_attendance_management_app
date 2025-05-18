@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
<div class="request">
    <div class="request-heading">
        <h2>申請一覧</h2>
    </div>
    <div class="approval">
        <form class="form-wait" action="/stamp_correction_request/list/index_wait" method="get">
            @csrf
            <button class="btn {{($data == 'wait') ? 'choice' : 'not-choice'}}">承認待ち</button>
        </form>
        <form class="form-approved" action="/stamp_correction_request/list/index_approved" method="get">
            @csrf
            <button class="btn {{($data == 'approved') ? 'choice' : 'not-choice'}}">承認済み</button>
            <input type="hidden" name="tab" value="approved">
        </form>
    </div>
    <div class="table-wrap">
        <table class="request-tb">
            <tr class="thead">
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            @foreach ($lists as $list)
            <tr>
                <td data-label="状態">{{$list['application_status']}}</td>
                <td data-label="名前">{{$list['name']}}</td>
                <td data-label="対象日時">{{$list['attendance_time']}}</td>
                <td data-label="申請理由">{{$list['remarks']}}</td>
                <td data-label="申請日時">{{$list['application_date']}}</td>
                @if(Auth::guard('admin')->check())
                <!-- 管理者の場合：修正申請承認画面へ遷移する -->
                <td data-label="詳細"><a class="link" href="/stamp_correction_request/approve/{{ $list['work_id'] }}">詳細</a></td>
                @else
                <!-- 一般ユーザーの場合：勤怠詳細画面へ遷移する -->
                <td data-label="詳細"><a class="link" href="/attendance/{{ $list['work_id'] }}">詳細</a></td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection