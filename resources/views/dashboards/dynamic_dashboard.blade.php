@extends('layouts._master')
@section('content')
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Welcome {{ auth()->user()->name }} <span
                        class="badge light badge-info">{{ auth()->user()->role_name }}</span>
                    @if ($productLabel ?? null)
                        <span class="badge light badge-primary">{{ $productLabel }}</span>
                    @endif
                </h2>
                <p class="mb-0" id="date_time"></p>
                
                <p class="mb-0" id="date_time"></p>
            </div>
            <div class="date">
                <p class="mb-1">Show by date</p>
                <input name="datepicker" class="datepicker-default form-control" placeholder="choose date" id="datepicker"
                    style="background-color: #0061f2 !important; color: #fff !important;" />
            </div>

        </div>



        {{-- Dashboard cards — a KodaSMS client talks about messages, a KodaFreight client about loads --}}
        <div class="row">
            @if (($hasCargo ?? false) === false)
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $total_customer }}</h2>
                                <span>Total Customers</span>
                            </div>
                            <i class="fa-solid fa-user text-primary" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-secondary" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $messentsent }}</h2>
                                <span>Messages Sent</span>
                            </div>
                            <i class="fa-solid fa-comments text-primary" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-primary" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $messagefailed }}</h2>
                                <span>Messages Failed</span>
                            </div>
                            <i class="fa-solid fa-triangle-exclamation text-danger" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-danger" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $smsLeft ?? 0 }}</h2>
                                <span>SMS Left In Package</span>
                            </div>
                            <i class="fa-solid fa-envelope-circle-check text-success" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-success" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $loadcreate ?? 0 }}</h2>
                                <span>Total Loads</span>
                            </div>
                            <i class="fa-solid fa-truck text-primary" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-secondary" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $total_customer }}</h2>
                                <span>Total Customers</span>
                            </div>
                            <i class="fa-solid fa-user text-primary" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-primary" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            @if ($hasAccounts ?? false)
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $pendingInvoices ?? 0 }}</h2>
                                <span>Unpaid Invoices</span>
                            </div>
                            <i class="fa-solid fa-file-invoice-dollar text-warning" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-warning" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            @endif
            @if ($hasSms ?? false)
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $smsLeft ?? 0 }}</h2>
                                <span>SMS Left In Package</span>
                            </div>
                            <i class="fa-solid fa-envelope-circle-check text-success" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-success" style="width:100%;height:4px;"></div>
                    </div>
                </div>
            </div>
            @endif
            @endif
        </div>

        <div class="row">
            @if ($hasCargo ?? false)
            <div class="col-xl-8 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Top 5 Customers by Loads</h4>
                    </div>
                    <div class="card-body" style="height:300px;">
                        <canvas id="topCustomersBarChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
            @if ($hasAccounts ?? false)
            <div class="col-xl-4 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Invoice Status</h4>
                    </div>
                    <div class="card-body" style="height:300px;">
                        <canvas id="invoicePieChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
            @if (($hasCargo ?? false) === false && ($hasSms ?? false))
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Your SMS allowance</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>{{ $smsLeft ?? 0 }}</strong> SMS left of
                            <strong>{{ $smsQuota ?? 0 }}</strong> assigned to this package.
                            When the allowance is finished sending stops until the owner adds more.
                        </p>
                        <div class="progress" style="height:10px;">
                            @php($used = max(($smsQuota ?? 0) - ($smsLeft ?? 0), 0))
                            @php($pct = ($smsQuota ?? 0) > 0 ? min(100, round($used / $smsQuota * 100)) : 0)
                            <div class="progress-bar {{ $pct > 90 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.message') }}" class="btn btn-primary btn-sm">Send a message</a>
                            <a href="{{ route('admin.message.index') }}" class="btn btn-outline-primary btn-sm">Message history</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
        <script>
            (function () {
                var invoiceEl = document.getElementById('invoicePieChart');
                if (invoiceEl) {
                var ctx = invoiceEl.getContext('2d');
                var invoicePieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Pending Invoices', 'Full Paid Invoices', 'Partial Paid Invoices'],
                        datasets: [{
                            data: [{{ $pendingInvoices }}, {{ $fullPaidInvoices }},
                                {{ $partialPaidInvoices }}
                            ],
                            backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 206, 86, 0.6)'
                            ],
                            borderColor: ['rgba(255, 99, 132, 1)', 'rgba(75, 192, 192, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
                }
            })();

            (function () {
                var topEl = document.getElementById('topCustomersBarChart');
                if (topEl) {
                var ctx = topEl.getContext('2d');
                var customerNames = @json($topCustomers->pluck('customer_firstname'));
                var loadCounts = @json($topCustomers->pluck('loadpackages_count'));

                var topCustomersBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: customerNames,
                        datasets: [{
                            label: 'Number of Loads',
                            data: loadCounts,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Loads'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Customers'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
                }
            })();

            (function () {
                var monthEl = document.getElementById('customersPerMonthLineChart');
                if (monthEl) {
                var ctx = monthEl.getContext('2d');
                var customersPerMonth = @json($customersPerMonth);
                var labels = customersPerMonth.map(function(item) {
                    return new Date(item.year, item.month - 1).toLocaleString('default', {
                        month: 'long',
                        year: 'numeric'
                    });
                });
                var data = customersPerMonth.map(function(item) {
                    return item.customer_count;
                });

                var customersPerMonthLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Customers',
                            data: data,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Customers'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
                }
            })();
        </script>
    @endpush






    @push('page_js')
        <!-- Apex Chart -->
        <script src="{{ asset('assets/vendor/chartist/js/chartist.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins-init/sparkline-init.js') }}"></script>
        <script src="{{ asset('assets/vendor/peity/jquery.peity.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins-init/piety-init.js') }}"></script>
        <script src="{{ asset('vendor/chart-js/chart.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins-init/widgets-script-init.js') }}"></script>
    @endpush
@endsection
