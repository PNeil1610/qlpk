@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary"><i class="fas fa-file-prescription me-2"></i>Danh Sách Toa Thuốc</h3>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form id="searchForm" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Tìm kiếm bệnh nhân</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="searchKeyword" name="keyword" class="form-control" placeholder="Tên, mã, SĐT bệnh nhân..." value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Trạng thái</label>
                        <select id="searchStatus" name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Chưa lấy thuốc (Đã kê)</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Đã lấy thuốc</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div id="tableDataContainer">
            <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="ps-4">ID Toa</th>
                                <th scope="col">Ngày kê</th>
                                <th scope="col">Mã bệnh nhân</th>
                                <th scope="col">Bệnh nhân</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prescriptions as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">#{{ $item->id }}</td>
                                    <td>{{ $item->prescribed_at ? \Carbon\Carbon::parse($item->prescribed_at)->format('d/m/Y H:i') : '---' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $item->consultation->patient->patient_code ?? '---' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="fw-bold text-dark">{{ $item->consultation->patient->full_name ?? 'Không rõ' }}</span>
                                    </td>
                                    <td>
                                        @if($item->status == 1)
                                            <span class="badge bg-warning text-dark">Chưa lấy thuốc</span>
                                        @elseif($item->status == 2)
                                            <span class="badge bg-success">Đã lấy thuốc</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('prescriptions.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Xem toa thuốc">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                            <form action="{{ route('prescriptions.update_status', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                @if($item->status == 1)
                                                    <button type="submit" class="btn btn-sm btn-success" title="Xác nhận đã phát thuốc" onclick="return confirm('Xác nhận bệnh nhân đã lấy thuốc cho toa #{{ $item->id }}?')">
                                                        <i class="fas fa-check"></i> Đã lấy
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Hủy xác nhận phát thuốc" onclick="return confirm('Hủy xác nhận đã lấy thuốc cho toa #{{ $item->id }}?')">
                                                        <i class="fas fa-undo"></i> Hủy
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        Chưa có toa thuốc nào trên hệ thống.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-end">
                    {{ $prescriptions->links() }}
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchKeyword = document.getElementById('searchKeyword');
        const searchStatus = document.getElementById('searchStatus');
        const tableContainer = document.getElementById('tableDataContainer');
        let timeout = null;

        function fetchPrescriptions(url = null) {
            const keyword = searchKeyword.value;
            const status = searchStatus.value;
            const queryParams = new URLSearchParams({
                keyword: keyword,
                status: status
            });
            const fetchUrl = url || "{{ route('prescriptions.index') }}?" + queryParams.toString();
            
            // Show loading state
            tableContainer.style.opacity = '0.5';

            fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableContainer = doc.getElementById('tableDataContainer');
                if (newTableContainer) {
                    tableContainer.innerHTML = newTableContainer.innerHTML;
                }
                tableContainer.style.opacity = '1';
                attachPaginationEvents();
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableContainer.style.opacity = '1';
            });
        }

        function attachPaginationEvents() {
            const links = tableContainer.querySelectorAll('.pagination a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchPrescriptions(this.href);
                });
            });
        }

        searchKeyword.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => fetchPrescriptions(), 500);
        });

        searchStatus.addEventListener('change', function () {
            fetchPrescriptions();
        });

        attachPaginationEvents();
    });
</script>
@endpush