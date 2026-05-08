@extends('layouts.app')

@section('content')

    <h3>Sửa phòng</h3>

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form action="{{ route('rooms.update', $room->id) }}" method="POST">

        @method('PUT')

        @include('rooms.form')

    </form>

@endsection