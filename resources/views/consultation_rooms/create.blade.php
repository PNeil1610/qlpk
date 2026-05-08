@extends('layouts.app')

@section('content')

    <h3>Thêm phòng cho phiên khám</h3>

    <form action="{{ route('consultation_rooms.store') }}" method="POST">

        @csrf

        @include('consultation_rooms.form')

    </form>

@endsection