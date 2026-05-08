@extends('layouts.app')

@section('content')

    <h3>Thêm khoa</h3>

    <form method="POST" action="{{ route('departments.store') }}">

        @include('departments.form')

    </form>

@endsection