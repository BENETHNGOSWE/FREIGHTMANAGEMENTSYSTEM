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
                                <h2 class="fs-34 text-black font-w600">{{ $loadcreate }}</h2>
                                <span>Total Load in Stock</span>
                            </div>
                            <i class="fas fa-truck text-primary" style="font-size:40px;"></i>
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
                                    <h2 class="fs-34 text-black font-w600">{{ $invoiceCount }}</h2>
                                    <span>Total Invoice</span>
                                </div>
                                <i class="fas fa-file-invoice text-primary" style="font-size:40px;"></i>
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
                <a href="#">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center">
                                <div class="media-body mr-3">
                                    <h2 class="fs-34 text-black font-w600">{{ $customerCount }}</h2>
                                    <span>Total Customers</span>
                                </div>
                                <i class="fa fa-user text-primary" style="font-size:40px;"></i>
                            </div>
                        </div>
                        <div class="progress  rounded-0" style="height:4px;">
                            <div class="progress-bar rounded-0 bg-secondary progress-animated"
                                style="width: 100%; height:4px;" role="progressbar">
                                <span class="sr-only">30% Complete</span>
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
                                    <h2 class="fs-34 text-black font-w600">{{ $messentsent }} / {{ $messageLimit }}</h2>
                                    <span>Total Messages Sent / Assigned</span>
                                </div>
                                <i class="fa fa-comments text-primary" style="font-size:40px;"></i>
                            </div>
                        </div>
                        <div class="progress rounded-0" style="height:4px;">
                            <div class="progress-bar rounded-0 bg-secondary progress-animated"
                                style="width: {{ $messageLimit > 0 ? ($messentsent / $messageLimit) * 100 : 0 }}%; height:4px;"
                                role="progressbar">
                                <span
                                    class="sr-only">{{ $messageLimit > 0 ? round(($messentsent / $messageLimit) * 100) : 0 }}%
                                    Complete</span>
                            </div>
                        </div>
                    </div>
                </a>
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
                                <h4 class="card-title">Loads Trends by Month</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="loadsPerMonthChart" width="400" height="200"></canvas>
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
                var ctx = document.getElementById('loadsPerMonthChart').getContext('2d');
                var loadsPerMonth = @json($loadsPerMonth);

                var labels = loadsPerMonth.map(item => new Date(item.year, item.month - 1).toLocaleString('default', {
                    month: 'long',
                    year: 'numeric'
                }));
                var data = loadsPerMonth.map(item => item.load_count);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Loads',
                            data: data,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Number of Loads'
                                }
                            },
                            x: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Month'
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
