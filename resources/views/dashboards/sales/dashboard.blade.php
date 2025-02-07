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
                                <h2 class="fs-34 text-black font-w600">{{ $customerleads }}</h2>
                                <span>Total Leads</span>
                            </div>
                            <i class="fa fa-hospital-user text-primary" style="font-size:40px;"></i>
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
                                    <h2 class="fs-34 text-black font-w600">{{ $customercategories }}</h2>
                                    <span>Total Categories</span>
                                </div>
                                <i class="fa fa-house-medical text-primary" style="font-size:40px;"></i>
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
                                    <h2 class="fs-34 text-black font-w600">{{ $messentsent }} / {{ $messageLimit }}</h2>
                                    <span>Total Messages Sent / Assigned</span>
                                </div>
                                <i class="fa fa-user-doctor text-primary" style="font-size:40px;"></i>
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


            <div class="col-xl-3  col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="media-body mr-3">
                                <h2 class="fs-34 text-black font-w600">{{ $messagefailed }}</h2>
                                <span>Total Message Fail</span>
                            </div>
                            <i class="fa-solid fa-user-nurse text-primary" style="font-size:40px;"></i>
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
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Leads Collected by Month</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="leadsPerMonthChart" width="400" height="200"></canvas>
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
                var ctx = document.getElementById('leadsPerMonthChart').getContext('2d');
                var leadsPerMonth = @json($leadsPerMonth); // Ensure you're accessing the correct variable

                var labels = leadsPerMonth.map(item => new Date(item.year, item.month - 1).toLocaleString('default', {
                    month: 'long',
                    year: 'numeric'
                }));
                var data = leadsPerMonth.map(item => item.lead_count);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Leads',
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
                                    text: 'Number of Leads'
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
