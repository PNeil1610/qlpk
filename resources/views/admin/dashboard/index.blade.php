@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary fw-bold"><i class="fas fa-chart-line me-2"></i>Dashboard Tổng Quan</h2>
    </div>

    <div class="row g-4">
        <!-- Cụm 1: Bệnh nhân -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 h-100 dashboard-card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <select class="form-select form-select-sm w-auto fw-bold text-primary border-primary" id="metric1" onchange="updateChart(1)">
                        <option value="new_patients">Số bệnh nhân ĐK mới</option>
                        <option value="re_exam_patients">Số lượng bệnh nhân tái khám</option>
                    </select>
                    <select class="form-select form-select-sm w-auto bg-light" id="period1" onchange="updateChart(1)">
                        <option value="day">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month">Tháng này</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="text-muted text-uppercase fw-bold small" id="title1">Tổng Số</span>
                        <div class="stat-number text-primary" id="total1">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cụm 2: Phiên khám -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 h-100 dashboard-card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <select class="form-select form-select-sm w-auto fw-bold text-success border-success" id="metric2" onchange="updateChart(2)">
                        <option value="created_consultations">Số phiên khám được tạo</option>
                        <option value="completed_consultations">Số phiên khám hoàn thành</option>
                    </select>
                    <select class="form-select form-select-sm w-auto bg-light" id="period2" onchange="updateChart(2)">
                        <option value="day">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month">Tháng này</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="text-muted text-uppercase fw-bold small" id="title2">Tổng Số</span>
                        <div class="stat-number text-success" id="total2">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chart2"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cụm 3: Đơn thuốc -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 h-100 dashboard-card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <select class="form-select form-select-sm w-auto fw-bold text-warning border-warning" style="color: #d39e00 !important;" id="metric3" onchange="updateChart(3)">
                        <option value="created_prescriptions">Số lượng đơn thuốc đã kê</option>
                        <option value="completed_prescriptions">Số lượng đơn thuốc đã hoàn thành</option>
                    </select>
                    <select class="form-select form-select-sm w-auto bg-light" id="period3" onchange="updateChart(3)">
                        <option value="day">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month">Tháng này</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="text-muted text-uppercase fw-bold small" id="title3">Tổng Số</span>
                        <div class="stat-number text-warning" style="color: #ffc107 !important;" id="total3">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chart3"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cụm 4: Thuốc & Doanh thu -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0 h-100 dashboard-card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <select class="form-select form-select-sm w-auto fw-bold text-danger border-danger" id="metric4" onchange="updateChart(4)">
                        <option value="prescribed_medicines">Số lượng thuốc đã kê</option>
                        <option value="dispensed_medicines">Số lượng thuốc đã lấy</option>
                        <option value="revenue">Số tiền đã thu (VNĐ)</option>
                    </select>
                    <select class="form-select form-select-sm w-auto bg-light" id="period4" onchange="updateChart(4)">
                        <option value="day">Hôm nay</option>
                        <option value="week">Tuần này</option>
                        <option value="month">Tháng này</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="text-muted text-uppercase fw-bold small" id="title4">Tổng Số</span>
                        <div class="stat-number text-danger" id="total4">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chart4"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuration for charts
    const chartConfigs = {
        1: { color: 'rgba(13, 110, 253, 0.8)', borderColor: 'rgba(13, 110, 253, 1)' }, // Primary
        2: { color: 'rgba(25, 135, 84, 0.8)', borderColor: 'rgba(25, 135, 84, 1)' },   // Success
        3: { color: 'rgba(255, 193, 7, 0.8)', borderColor: 'rgba(255, 193, 7, 1)' },   // Warning
        4: { color: 'rgba(220, 53, 69, 0.8)', borderColor: 'rgba(220, 53, 69, 1)' },   // Danger
    };

    // Store chart instances to destroy and re-create them
    const charts = {};

    function initChart(groupId) {
        const ctx = document.getElementById(`chart${groupId}`).getContext('2d');
        charts[groupId] = new Chart(ctx, {
            type: 'line', // You can change to 'bar' if you prefer
            data: {
                labels: [],
                datasets: [{
                    label: 'Dữ liệu',
                    data: [],
                    backgroundColor: chartConfigs[groupId].color,
                    borderColor: chartConfigs[groupId].borderColor,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We use our own header
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

    function updateChart(groupId) {
        const metric = document.getElementById(`metric${groupId}`).value;
        const period = document.getElementById(`period${groupId}`).value;
        
        // Show loading state
        document.getElementById(`total${groupId}`).innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        // Fetch data
        fetch(`{{ route('dashboard.stats') }}?metric=${metric}&period=${period}`)
            .then(response => response.json())
            .then(data => {
                // Update Total text
                let totalText = formatNumber(data.total);
                if (metric === 'revenue') {
                    totalText += ' đ';
                }
                document.getElementById(`total${groupId}`).innerText = totalText;

                // Update Chart
                const chart = charts[groupId];
                
                // Update dataset label based on metric name
                const metricText = document.getElementById(`metric${groupId}`).options[document.getElementById(`metric${groupId}`).selectedIndex].text;
                
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.values;
                chart.data.datasets[0].label = metricText;
                
                // For revenue, format tooltip
                if (metric === 'revenue') {
                    chart.options.plugins.tooltip.callbacks.label = function(context) {
                        return context.dataset.label + ': ' + formatNumber(context.raw) + ' VNĐ';
                    };
                } else {
                    chart.options.plugins.tooltip.callbacks.label = function(context) {
                        return context.dataset.label + ': ' + formatNumber(context.raw);
                    };
                }

                chart.update();
            })
            .catch(error => {
                console.error('Error fetching stats:', error);
                document.getElementById(`total${groupId}`).innerText = 'Lỗi!';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all 4 charts
        for (let i = 1; i <= 4; i++) {
            initChart(i);
            updateChart(i);
        }
    });
</script>
@endpush
