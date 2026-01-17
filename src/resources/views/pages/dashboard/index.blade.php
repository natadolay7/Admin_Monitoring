@extends('layouts.master')
@section('content')
    <div class="container-fluid mt-5">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line text-primary me-2"></i>Dashboard Laporan</h1>
                <p class="mb-0 text-muted">Analisis data absensi dan patroli harian</p>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-3">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filter Tanggal
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tanggal Mulai</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-start"></i></span>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tanggal Akhir</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-end"></i></span>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <!-- Total Absensi -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <i class="fas fa-clipboard-check me-1"></i>Total Absensi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAbsen }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $absenPerHari->count() }} hari aktif
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-primary opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terlambat -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    <i class="fas fa-clock me-1"></i>Terlambat
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $late }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        Rata² {{ $avgLate }} menit
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tepat Waktu -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <i class="fas fa-check-circle me-1"></i>Tepat Waktu
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ontime }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        {{ number_format(($ontime / $totalAbsen) * 100, 1) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-thumbs-up fa-2x text-success opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Belum Checkout -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    <i class="fas fa-sign-out-alt me-1"></i>Belum Checkout
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $notCheckout }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        Perlu tindak lanjut
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-running fa-2x text-warning opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Absensi Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-line me-2"></i>Trend Absensi Harian
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M') }} -
                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="absenChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Statistics -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie me-2"></i>Statistik Ketepatan Waktu
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4">
                            <canvas id="lateChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="me-3">
                                <i class="fas fa-circle text-success"></i> Tepat Waktu
                                ({{ $lateStats->tepat_waktu }})
                            </span>
                            <span>
                                <i class="fas fa-circle text-danger"></i> Terlambat
                                ({{ $lateStats->terlambat }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patroli Reports -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-shield-alt me-2"></i>Aktivitas Patroli
                        </h6>
                        <div>
                            <span class="badge bg-info bg-opacity-10 text-info me-2">
                                <i class="fas fa-file-alt me-1"></i>
                                Total Laporan: {{ $totalPatroli }}
                            </span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $patroliPerHari->count() }} hari aktif
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="patroliChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Data -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Data Absensi
                </h6>
                <span class="badge bg-primary">{{ $absen->count() }} records</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th><i class="fas fa-user me-1"></i> Nama</th>
                                <th><i class="fas fa-calendar me-1"></i> Jadwal</th>
                                <th><i class="fas fa-sign-in-alt me-1"></i> Check In</th>
                                <th><i class="fas fa-sign-out-alt me-1"></i> Check Out</th>
                                <th><i class="fas fa-clock me-1"></i> Keterlambatan</th>
                                <th><i class="fas fa-tag me-1"></i> Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absen as $row)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle-sm bg-primary bg-opacity-10 text-primary me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            {{ $row->name }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ $row->schedule_name }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if ($row->check_in)
                                            <span class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ \Carbon\Carbon::parse($row->check_in)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if ($row->check_out)
                                            <span class="text-primary">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ \Carbon\Carbon::parse($row->check_out)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-warning">
                                                <i class="fas fa-clock me-1"></i>Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $minutes = $row->late_minutes;
                                            $hours = floor($minutes / 60);
                                            $remain = $minutes % 60;
                                        @endphp
                                        @if ($minutes > 0)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                <div>
                                                    @if ($hours > 0)
                                                        <span class="text-danger fw-bold">{{ $hours }}j
                                                            {{ $remain }}m</span>
                                                    @else
                                                        <span class="text-danger fw-bold">{{ $minutes }} menit</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Tepat waktu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if ($row->check_in && !$row->check_out)
                                            <span class="badge bg-warning bg-opacity-20 text-warning">
                                                <i class="fas fa-clock me-1"></i>Belum Checkout
                                            </span>
                                        @elseif($row->late_minutes > 0)
                                            <span class="badge bg-danger bg-opacity-20 text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Terlambat
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-20 text-success">
                                                <i class="fas fa-check-circle me-1"></i>Tepat Waktu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Patroli Data -->
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-shield-alt me-2"></i>Data Laporan Patroli
                </h6>
                <span class="badge bg-primary">{{ $patroli->count() }} records</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th><i class="fas fa-user-shield me-1"></i> TAD</th>
                                <th><i class="fas fa-map-marker-alt me-1"></i> Lokasi</th>
                                <th><i class="fas fa-building me-1"></i> Cabang</th>
                                <th><i class="fas fa-file-alt me-1"></i> Deskripsi</th>
                                <th><i class="fas fa-calendar-day me-1"></i> Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patroli as $row)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle-sm bg-secondary bg-opacity-10 text-secondary me-2">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            {{ $row->tad_name }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-primary">
                                            <i class="fas fa-map-pin me-1"></i>
                                            {{ $row->nama_lokasi }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $row->branch_name }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-truncate" style="max-width: 300px;"
                                            title="{{ $row->deskripsi }}">
                                            {{ $row->deskripsi }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($row->created_at)->format('d M Y H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .avatar-circle-sm {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .chart-area,
        .chart-bar,
        .chart-pie {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-danger {
            border-left: 4px solid #e74a3b !important;
        }

        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }

        .card:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>

    <script>
        // Absensi Chart
        const absenLabels = @json($absenPerHari->pluck('tanggal'));
        const absenData = @json($absenPerHari->pluck('total'));

        const absenCtx = document.getElementById('absenChart').getContext('2d');
        new Chart(absenCtx, {
            type: 'line',
            data: {
                labels: absenLabels,
                datasets: [{
                    label: 'Total Absensi',
                    data: absenData,
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: '#4e73df',
                    borderWidth: 2,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#4e73df',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#fff',
                        borderWidth: 1
                    }
                }
            }
        });

        // Late Chart
        const lateCtx = document.getElementById('lateChart').getContext('2d');
        new Chart(lateCtx, {
            type: 'doughnut',
            data: {
                labels: ['Terlambat', 'Tepat Waktu'],
                datasets: [{
                    data: [{{ $lateStats->terlambat }}, {{ $lateStats->tepat_waktu }}],
                    backgroundColor: ['#e74a3b', '#1cc88a'],
                    hoverBackgroundColor: ['#d52a1a', '#17a673'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Patroli Chart
        const patroliLabels = @json($patroliPerHari->pluck('tanggal'));
        const patroliData = @json($patroliPerHari->pluck('total'));

        const patroliCtx = document.getElementById('patroliChart').getContext('2d');
        new Chart(patroliCtx, {
            type: 'bar',
            data: {
                labels: patroliLabels,
                datasets: [{
                    label: 'Laporan Patroli',
                    data: patroliData,
                    backgroundColor: 'rgba(54, 185, 204, 0.5)',
                    borderColor: '#36b9cc',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: '#36b9cc'
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#36b9cc',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                }
            }
        });
    </script>
@endsection
