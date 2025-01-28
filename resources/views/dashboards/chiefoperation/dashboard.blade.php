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





        <!-- <div class="row">
            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-primary text-primary">
                                <i class="fa fa-dollar text-primary" style="font-size:40px;"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-1">Montly Income</p>
                                <h4 class="mb-0">2,000,000 Tsh</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-warning text-warning">
                                <i class="fa fa-hand-holding-dollar text-secondary" style="font-size:40px;"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-1">Daily Income</p>
                                <h4 class="mb-0">140,000 Tsh</h4>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media ai-icon">
                            <span class="me-3 bgl-danger text-danger">
                                <i class="fa fa-filter-circle-dollar text-danger" style="font-size:40px;"></i>
                            </span>
                            <div class="media-body">
                                <p class="mb-1">Monthly Expences</p>
                                <h4 class="mb-0">40,000 Tsh</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <a href="#">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-success text-success">
                                    <i class="fa fa-users text-success" style="font-size:40px;"></i>

                                </span>
                                <div class="media-body">
                                    <p class="mb-1">Total Staffs</p>
                                    <h4 class="mb-0">4</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div> -->

        {{-- //assets,buildings,wards,etc  --}}
        <!-- <div class="row">
            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <a href="#">
                    <div class="widget-stat card bg-secondary">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-primary text-primary">
                                    <i class="fa fa-folder-tree text-primary" style="font-size:40px;"></i>
                                </span>
                                <div class="media-body">
                                    <p class="mb-1 text-light">Total Assets</p>
                                    <h4 class="mb-0">4</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <a href="#">
                    <div class="widget-stat card bg-primary">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-warning text-warning">
                                    <i class="fa fa-city text-light" style="font-size:40px;"></i>
                                </span>
                                <div class="media-body text-light">
                                    <p class="mb-1 ">buildings/Facilities</p>
                                    <h4 class="mb-0 text-light">3</h4>

                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <a href="#">
                    <div class="widget-stat card bg-success">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-danger text-danger">
                                    <i class="fa fa-house-chimney-medical text-light" style="font-size:40px;"></i>
                                </span>
                                <div class="media-body">
                                    <p class="mb-1 text-light">Total Wards</p>
                                    <h4 class="mb-0">3</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>



            <div class="col-xl-3 col-xxl-6 col-lg-6 col-sm-6">
                <a href=" # ">
                    <div class="widget-stat card bg-info">
                        <div class="card-body p-4 ">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-success text-indigo">
                                    <i class="fa fa-flask text-indigo" style="font-size:40px;"></i>

                                </span>
                                <div class="media-body">
                                    <p class="mb-1 text-light">Total Laboratories</p>
                                    <h4 class="mb-0 text-light">3</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div> -->
    </div>




    {{-- Today Apppointment --}}
    <!-- <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-header border-0 pb-0">
                        <h3 class="card-title text-primary">Revenue vs Expences</h3>
                        <h5 class="text-primary mb-0">
                            <i class="fa fa-caret-up"></i> 17.24%
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="ico-sparkline">
                            <div id="sparkline12"><canvas width="100" height="100"
                                    style="display: inline-block; width: 100px; height: 100px; vertical-align: top;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
 -->




    @push('scripts')
        <script>
            var sparkLine12 = function() {
                if (jQuery("#sparkline12").length > 0) {
                    //Pie
                    $("#sparkline12").sparkline([20, 80], {
                        type: "pie",
                        height: "100",
                        resize: !0,
                        sliceColors: [
                            "rgba(238, 50, 50, 1)",
                            "rgba(43, 193, 85, 1)",
                        ],
                    });
                }
            };
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
