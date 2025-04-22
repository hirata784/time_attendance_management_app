@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div>詳細画面</div>
<div>idは{{$id}}</div>
@endsection