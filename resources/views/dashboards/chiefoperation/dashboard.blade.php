@extends('layouts._master')
@section('content')
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Welcome {{ auth()->user()->name }} <span
                        class="badge light badge-info">{{ auth()->user()->getRoleNames()[0] }}</span></h2>

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
                                <h2 class="fs-34 text-black font-w600">4</h2>
                                <span>Total Leads</span>
                            </div>
                            <i class="fas fa-lightbulb text-primary" style="font-size:40px;"></i>
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
                                    <h2 class="fs-34 text-black font-w600">4</h2>
                                    <span>Total Categories</span>
                                </div>
                                <i class="fas fa-th-list text-primary" style="font-size:40px;"></i>
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
                                    <h2 class="fs-34 text-black font-w600">4</h2>
                                    <span>Total Messages Sent / Assigned</span>
                                </div>
                                <i class="fa fa-comments text-primary" style="font-size:40px;"></i>
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


            <div class="col-xl-3  col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">9</h2>
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
            <!-- Total Pending Amount -->
            <div class="col-xl-4 col-xxl-6 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-primary text-primary">
                                <i class="fa fa-hourglass-half text-primary" style="font-size:40px;"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-1">Pending Invoices</p>
                                <h4 class="mb-0">{{ number_format($total_pending, 2) }} Tsh</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Total Partial Paid Amount -->
            <div class="col-xl-4 col-xxl-6 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-warning text-warning">
                                <i class="fa fa-hand-holding-dollar text-warning" style="font-size:40px;"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-1">Partial Paid Invoices</p>
                                <h4 class="mb-0">{{ number_format($total_partial_paid, 2) }} Tsh</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Total Fully Paid Amount -->
            <div class="col-xl-4 col-xxl-6 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-success text-success">
                                <i class="fa fa-check-circle text-success" style="font-size:40px;"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-1">Fully Paid Invoices</p>
                                <h4 class="mb-0">{{ number_format($total_full_paid, 2) }} Tsh</h4>
                            </div>
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
                                <h4 class="card-title">Customers Vs Leads</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="customersLeadsPieChart" width="150" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Invoice Status Trends by Month</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="invoiceChart" width="400" height="200"></canvas>
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
            var ctx = document.getElementById('invoiceChart').getContext('2d');

            // Chart data passed from the controller
            var chartData = @json($chartData);

            // Extract labels (months) and datasets
            var labels = Object.keys(chartData);
            var pendingData = labels.map(month => chartData[month]['Pending']);
            var partialPaidData = labels.map(month => chartData[month]['Partial Paid']);
            var fullPaidData = labels.map(month => chartData[month]['Full Paid']);

            // Create the chart
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pending',
                            data: pendingData,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)', // Red
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        },
                        {
                            label: 'Partial Paid',
                            data: partialPaidData,
                            backgroundColor: 'rgba(255, 206, 86, 0.6)', // Yellow
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        },
                        {
                            label: 'Full Paid',
                            data: fullPaidData,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)', // Green
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Invoices'
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

        document.addEventListener('DOMContentLoaded', function() {
            var ctxCustomersVsLeads = document.getElementById('customersLeadsPieChart').getContext('2d');
            new Chart(ctxCustomersVsLeads, {
                type: 'doughnut',
                data: {
                    labels: ['Customers', 'Leads'],
                    datasets: [{
                        data: [{{ $customerCount }}, {{ $leadCount }}],
                        backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(255, 159, 64, 0.6)'],
                        borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 159, 64, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
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
