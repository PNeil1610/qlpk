@extends('layouts.app')

@section('title', 'Thêm bệnh nhân')
@section('page_title', 'Thêm bệnh nhân')

@section('content')

<form action="{{ route('patients.store') }}" method="POST">
    @include('patients.form')
</form>

@endsection