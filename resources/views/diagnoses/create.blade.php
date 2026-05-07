@extends('layouts.app')

@section('content')

    <h3>Thêm bệnh</h3>

    <form method="POST" action="{{ route('diagnoses.store') }}">

        @include('diagnoses.form')

    </form>

@endsection