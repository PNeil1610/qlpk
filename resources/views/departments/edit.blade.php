@extends('layouts.app')

@section('content')

    <h3>Sửa khoa</h3>

    <form method="POST" action="{{ route('departments.update', $department->id) }}">

        @csrf
        @method('PUT')

        @include('departments.form')

    </form>

@endsection