@extends('layouts.app')

@section('content')

    <h3>Cập nhật phòng cho phiên khám</h3>

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form action="{{ route('consultation_rooms.update', [
        $consultationRoom->consultation_id,
        $consultationRoom->room_id
    ]) }}" method="POST">

        @csrf
        @method('PUT')

        @include('consultation_rooms.form')

    </form>

@endsection