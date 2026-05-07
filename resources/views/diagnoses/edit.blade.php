@extends('layouts.app')

@section('content')

    <h3>Sửa bệnh</h3>

    <form method="POST" action="{{ route('diagnoses.update', $diagnosis->id) }}">

        @csrf
        @method('PUT')

        @include('diagnoses.form')

    </form>

@endsection