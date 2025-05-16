@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">
@endsection

@section('content')

<div class="staff">
    <div class="staff-heading">
        <h2>スタッフ一覧</h2>
    </div>
    <div class="table-wrap">
        <table class="staff-tb">
            <tr class="thead">
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
            @foreach ($lists as $list)
            <tr>
                <td>{{$list['name']}}</td>
                <td data-label="メールアドレス">{{$list['email']}}</td>
                <td data-label="詳細"><a class="link" href="/admin/attendance/staff/{{ $list['id'] }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection