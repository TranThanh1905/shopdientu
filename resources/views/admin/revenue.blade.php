@extends('admin.layouts.admin')

@section('title', 'Thống kê doanh thu')

@section('content')
<h2 class="mb-4"><i class="fas fa-chart-bar"></i> Thống kê doanh thu</h2>

<!-- Bộ lọc -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.revenue') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-calendar"></i> Loại thống kê</label>
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Theo ngày (7 ngày)</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Theo tuần (8 tuần)</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Theo tháng (12 tháng)</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Theo năm (5 năm)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-calendar-check"></i> Từ ngày</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>

            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-calendar-times"></i> Đến ngày</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <div class="mb-2">
                    <i class="fas fa-dollar-sign fa-3x text-primary"></i>
                </div>
                <h6 class="text-muted mb-2">Tổng doanh thu</h6>
                <h2 class="text-primary mb-0">{{ number_format($totalRevenue) }}₫</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <div class="mb-2">
                    <i class="fas fa-shopping-cart fa-3x text-success"></i>
                </div>
                <h6 class="text-muted mb-2">Tổng đơn hàng</h6>
                <h2 class="text-success mb-0">{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <div class="mb-2">
                    <i class="fas fa-calculator fa-3x text-info"></i>
                </div>
                <h6 class="text-muted mb-2">Giá trị TB/Đơn</h6>
                <h2 class="text-info mb-0">{{ number_format($averageOrder) }}₫</h2>
            </div>
        </div>
    </div>
</div>

<!-- Biểu đồ -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-area"></i>
            Biểu đồ doanh thu
            @if($period == 'day') (7 ngày gần đây)
            @elseif($period == 'week') (8 tuần gần đây)
            @elseif($period == 'month') (12 tháng gần đây)
            @else (5 năm gần đây)
            @endif
        </h5>
    </div>
    <div class="card-body">
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: @json($chartData['data']),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + '₫';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value) + '₫';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
