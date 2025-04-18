@extends('layouts._master')
@section('content')
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Welcome {{ auth()->user()->name }}  <span class="badge light badge-info">{{ auth()->user()->getRoleNames()[0] }}</span></h2>
                
                <p class="mb-0" id="date_time"></p>
            </div>
            <div class="date">
                <p class="mb-1">Show by date</p>
                <input name="datepicker" class="datepicker-default form-control" placeholder="choose date" id="datepicker"
                    style="background-color: #0061f2 !important; color: #fff !important;" />
            </div>

        </div>



        {{-- Dashboard card --}}
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{$total_customer}}</h2>
                                <span>Total Customers</span>
                            </div>
                            <i class="fa fa-user text-primary" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress  rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-secondary progress-animated" style="width: 100%; height:4px;"
                            role="progressbar">
                            <span class="sr-only">100% Complete</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3  col-sm-6">
                <a href="#">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{$total_categories}}</h2>
                                    <span>Total Categories</span>
                                </div>
                                <i class="fa fa-th-list text-primary" style="font-size:40px;"></i>
                            </div>
                        </div>
                        <div class="progress  rounded-0" style="height:4px;">
                            <div class="progress-bar rounded-0 bg-primary progress-animated" style="width:100%; height:4px;"
                                role="progressbar">
                                <span class="sr-only">100% Complete</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-sm-6">
                <a href="#">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <div class="media-body mr-3">
                                    <h2 class="fs-34 text-black font-w600">{{$messentsent}} / {{$messageLimit}}</h2>
                                    <span>Total Messages Sent / Assigned</span>
                                </div>
                                <i class="fa fa-comments text-primary" style="font-size:40px;"></i>
                            </div>
                        </div>
                        <div class="progress rounded-0" style="height:4px;">
                            <div class="progress-bar rounded-0 bg-secondary progress-animated" 
                                 style="width: {{ $messageLimit > 0 ? ($messentsent / $messageLimit) * 100 : 0 }}%; height:4px;" 
                                 role="progressbar">
                                <span class="sr-only">{{ $messageLimit > 0 ? round(($messentsent / $messageLimit) * 100) : 0 }}% Complete</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            
            <div class="col-xl-3  col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{$messagefailed}}</h2>
                                <span>Total Message Fail</span>
                            </div>
                            <i class="fa-solid fa-exclamation-circle text-primary" style="font-size:40px;"></i>
                        </div>
                    </div>
                    <div class="progress  rounded-0" style="height:4px;">
                        <div class="progress-bar rounded-0 bg-secondary progress-animated" style="width: 100%; height:4px;"
                            role="progressbar">
                            <span class="sr-only">1000% Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-xl-4 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Invoice Status</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="invoicePieChart" width="150" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Top 5 Customers by Loads</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="topCustomersBarChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Pending Invoices:', {{ $pendingInvoices }});
                console.log('Full Paid Invoices:', {{ $fullPaidInvoices }});
                console.log('Partial Paid Invoices:', {{ $partialPaidInvoices }});

                var ctx = document.getElementById('invoicePieChart').getContext('2d');
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
            });

            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('topCustomersBarChart').getContext('2d');
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
            });

            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('customersPerMonthLineChart').getContext('2d');
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
            });

          
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
