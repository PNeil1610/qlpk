@extends('layouts.app')

@section('content')

    <h3>Thêm phòng</h3>

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form action="{{ route('rooms.store') }}" method="POST">

        @include('rooms.form')

    </form>

@endsection