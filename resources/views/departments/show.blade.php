@extends('layouts.app')

@section('content')

    <h3>Chi tiết khoa</h3>

    <div class="card">

        <div class="card-body">

            <div class="mb-3">

                <label class="fw-bold">
                    Mã khoa
                </label>

                <div>
                    {{ $department->dept_code }}
                </div>

            </div>

            <div class="mb-3">

                <label class="fw-bold">
                    Tên khoa
                </label>

                <div>
                    {{ $department->dept_name }}
                </div>

            </div>

            <div class="mb-3">

                <label class="fw-bold">
                    Mô tả
                </label>

                <div>
                    {{ $department->description ?? 'Không có mô tả' }}
                </div>

            </div>

            <div class="mb-3">

                <label class="fw-bold">
                    Trạng thái
                </label>

                <div>

                    @if($department->status == 1)

                        <span class="badge bg-success">
                            Hoạt động
                        </span>

                    @elseif($department->status == 0)

                        <span class="badge bg-secondary">
                            Không hoạt động
                        </span>

                    @else($department->status == 2)

                        <span class="badge bg-warning">
                            Đang xây dựng
                        </span>

                    @endif

                </div>

            </div>

            <div class="mb-3">

                <label class="fw-bold">
                    Ngày tạo
                </label>

                <div>
                    {{ $department->created_at?->format('d/m/Y H:i') }}
                </div>

            </div>

            <div class="mb-3">

                <label class="fw-bold">
                    Cập nhật lần cuối
                </label>

                <div>
                    {{ $department->updated_at?->format('d/m/Y H:i') }}
                </div>

            </div>

        </div>

    </div>

    <div class="mt-3">

        <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning">

            Sửa

        </a>

        <a href="{{ route('departments.index') }}" class="btn btn-secondary">

            Quay lại

        </a>

    </div>

@endsection