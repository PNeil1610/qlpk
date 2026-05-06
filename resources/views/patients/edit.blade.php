@extends('layouts.app')

@section('title', 'Sửa bệnh nhân')
@section('page_title', 'Cập nhật bệnh nhân')

@section('content')

<form action="{{ route('patients.update', $patient->id) }}" method="POST">
    @method('PUT')
    @include('patients.form', ['patient' => $patient])
</form>

@endsection