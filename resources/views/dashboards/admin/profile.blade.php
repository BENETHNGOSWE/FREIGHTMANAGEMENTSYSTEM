@extends('layouts._master')
@section('content')
<!-- row -->
    <div class="container-fluid">
    <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
      <div class="me-auto">
        <h2 class="text-black font-w600">{{ $clientadmin->firstname . " " . $clientadmin->middlename . " " . $clientadmin->lastname }}</h2>
        <p class="mb-0">Company Manager</p>
      </div>

      <div>
        <a href="{{ route('admin.edit.profile', $clientadmin) }}" class="btn btn-outline-primary">
            Update Profile
        </a>
    </div>
    
     
        
    </div>
    

    <div class="row">
        <div class="col-md-12">
             <div class="row">
                <div class="card col-md-8">
                    <div class="card-body">
                       <div class="row">
                        <div class="col-md-4">
                            <img class="rounded img-fluid" style="width: 90%" src="{{asset('assets/images/users/5.png')}}" alt="">

                        </div>
        
                        <div class="col-md-8 justify-between">
                           <div class="">
                                <h2 class="pt-2 text-black">Dr. {{ $clientadmin->firstname . " " . $clientadmin->middlename . " " . $clientadmin->lastname }}</h2>
                                <h4 class="pt-2 text-black">Company Manager</h4>
                           </div>
    
                            <div class="pt-5">
                                {{-- <p>{{ $clientadmin->staff->bio }}</p> --}}
                            </div>
                        </div>
                       </div>
    
    
    
                       {{-- tab ui --}}
                       <div class="row">
                        <div class="col-xl-12">
                            <div class="card border mt-4">
                             
                              <div class="card-body">
                                <!-- Nav tabs -->
                                <div class="default-tab ">
                                  <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                      <a
                                        class="nav-link active"
                                        data-bs-toggle="tab"
                                        href="#home"
                                        > Profile</a
                                      >
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" data-bs-toggle="tab" href="#profile"
                                        > Appointment</a
                                      >
                                    </li>
                                    {{-- <li class="nav-item">
                                      <a class="nav-link" data-bs-toggle="tab" href="#contact"
                                        >Patients</a
                                      >
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" data-bs-toggle="tab" href="#message"
                                        >Notification</a
                                      >
                                    </li>
    
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#attachments"
                                          >Attachments</a
                                        >
                                    </li>
    
                                      <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#Schedule"
                                          > Schedule</a
                                        >
                                      </li> --}}
                                  </ul>
                                  <div class="tab-content">
                                    <div
                                      class="tab-pane fade show active"
                                      id="home"
                                      role="tabpanel"
                                    >
                                    
                                    <div class="card">
                                      <div class="card-body">
                                        <div class="row">
                                          <div class="col-sm-6">
                                            <p class="mb-0">Full Name</p>
                                          </div>
                                          <div class="col-sm-6">
                                            <p class="text-muted mb-0">{{ $clientadmin->firstname . " " . $clientadmin->middlename . " " . $clientadmin->lastname }}</p>
                                          </div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                          <div class="col-sm-6">
                                            <p class="mb-0">Email</p>
                                          </div>
                                          <div class="col-sm-6">
                                            <p class="text-muted mb-0">{{$clientadmin->email}}</p>
                                          </div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                          <div class="col-sm-6">
                                            <p class="mb-0">phonenumber</p>
                                          </div>
                                          <div class="col-sm-6">
                                            <p class="text-muted mb-0">{{$clientadmin->phone}}</p>
                                          </div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                          <div class="col-sm-6">
                                            <p class="mb-0">Gender</p>
                                          </div>
                                          <div class="col-sm-6">
                                            <p class="text-muted mb-0">{{$clientadmin->client_gender}}</p>
                                          </div>
                                        </div>
                                        <hr>

                                      </div>
                                    </div>

                                           
                                    </div>
                                    <div class="tab-pane fade" id="profile">
                                      <div class="pt-4">
                                        <div class="card">
                                          <div class="card-body">
                                              <div class="table-responsive">
                                                  <div style="display: flex; justify-content: space-between;">
                                                  </div>
                                                  <table id="stafftypeTable" class="table table-borderless table-hover">
                                                      <thead>
                                                          <tr>
                                                              <th>
                                                                  s/n
                                                              </th>
                                                              <th class="text-left">
                                                                  Patient No 
                                                              </th> 
                                                              <th class="text-left">
                                                                  Patient Name
                                                              </th>
                                                              <th class="text-left">
                                                                  Patient phonenumber
                                                              </th>
                          
                                                              <th class="text-left">
                                                                  Patient Email
                                                              </th>
                          
                                                              <th class="text-left">
                                                                  Patient Status
                                                              </th>
                          
                                                              <th class="text-center">
                                                                  @lang('crud.common.actions')
                                                              </th>
                                                          </tr>
                                                      </thead>
                                                      
                                                  </table>
                                              </div>
                                          </div>
                                      </div>
                                      </div>
                                    </div>
                                    <div class="tab-pane fade" id="contact">
                                    
                                    </div>
                                    <div class="tab-pane fade" id="message">
                                      
                                    </div>
    
                                    <div class="tab-pane fade" id="attachments">
                                      {{-- @php
                                      $attachments = \App\Models\Attachments::where('user_id', Auth::user()->id)->get(); 

                                      @endphp --}}

                                      <div class="pt-4">
                                        <div class="card">
                                          <div class="card-body">
                                              <div class="table-responsive">
                                                  <div style="display: flex; justify-content: space-between;">
                                                  </div>
                                                  <table id="stafftypeTable" class="table table-borderless table-hover">
                                                      <thead>
                                                          <tr>
                                                              <th>
                                                                  s/n
                                                              </th>
                                                              <th class="text-left">
                                                                  Title
                                                              </th> 
                                                              <th class="text-left">
                                                                  Description
                                                              </th>
                                                              <th class="text-left">
                                                                  File
                                                              </th>
                                                          </tr>
                                                      </thead>
                                                      {{-- <tbody>
                                                          @forelse($attachments as $attachment)
                                                              <tr>
                                                                  <td>
                                                                      {{ $loop->iteration }}
                                                                  </td>
                                                                  <td>{{ $attachment->title }}</td>
                                                                  <td>{{ $attachment->description }}</td>
                                                                  <td>
                                                                    <a class="btn btn-success btn-sm" href="{{ asset('storage/uploads/'.$attachment->file) }}" target="_blank">View File</a>

                                                                  </td>
                                                                  </tr>
                                                          @empty
                                                              <tr>
                                                                  <td class="text-center py-5" colspan="5">
                                                                      @lang('crud.common.no_items_found')
                                                                  </td>
                                                              </tr>
                                                          @endforelse
                                                      </tbody> --}}
                                                  </table>
                                              </div>
                                          </div>
                                        </div>
                                      </div>
                                                          
                                        
                                      </div>
    
                                      <div class="tab-pane fade" id="Schedule">
                                        
                                      </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                       </div>
                    </div>
                 </div>
    
                 <div class="col-md-4">
                    <div class="card appointment-schedule">
                        <div class="card-header pb-0 border-0">
                          <h3 class="fs-20 text-black mb-0">
                            Appointment Schedule
                          </h3>
                          <div class="dropdown ms-auto c-pointer">
                            <div
                              class="btn-link p-2 bg-light"
                              data-bs-toggle="dropdown"
                            >
                              <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z"
                                  stroke="#2E2E2E"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                />
                                <path
                                  d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z"
                                  stroke="#2E2E2E"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                />
                                <path
                                  d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z"
                                  stroke="#2E2E2E"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                />
                              </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                              <a
                                class="dropdown-item text-black"
                                href="javascript:;"
                                >Info</a
                              >
                              <a
                                class="dropdown-item text-black"
                                href="javascript:;"
                                >Details</a
                              >
                            </div>
                          </div>
                        </div>



                        <div class="card-body">
                          <div class="row">
                            <div class="col-xl-12 col-xxl-12 col-md-12">
                              <div class="appointment-calender">
                                <input
                                  type="text"
                                  class="form-control d-none"
                                  id="datetimepicker1"
                                />
                              </div>
                            </div>
                            <div
                              class="col-xl-12 col-xxl-12 col-md-12 height415 dz-scroll border mt-3"
                              id="appointment-schedule"
                            >
                              <div
                                class="d-flex pb-3 border-bottom mb-3 align-items-end"
                              >
                                <div class="me-auto">
                                  <p class="text-black font-w600 mb-2">
                                    Wednesday, June 3th
                                  </p>
                                  <ul>
                                    <li>
                                      <i class="las la-clock"></i>09:00 - 10:30 AM
                                    </li>
                                    <li><i class="las la-user"></i>Dr. Samantha</li>
                                  </ul>
                                </div>
                                <a
                                  href="javascript:void(0)"
                                  class="text-success me-3 mb-2"
                                >
                                  <i class="las la-check-circle scale5"></i>
                                </a>
                                <a
                                  href="javascript:void(0)"
                                  class="text-danger mb-2"
                                >
                                  <i class="las la-times-circle scale5"></i>
                                </a>
                              </div>
                              <div
                                class="d-flex pb-3 border-bottom mb-3 align-items-end"
                              >
                                <div class="me-auto">
                                  <p class="text-black font-w600 mb-2">
                                    Tuesday, June 16th
                                  </p>
                                  <ul>
                                    <li>
                                      <i class="las la-clock"></i>09:00 - 10:30 AM
                                    </li>
                                    <li><i class="las la-user"></i>Dr. Samantha</li>
                                  </ul>
                                </div>
                                <a
                                  href="javascript:void(0)"
                                  class="text-success me-3 mb-2"
                                >
                                  <i class="las la-check-circle scale5"></i>
                                </a>
                                <a
                                  href="javascript:void(0)"
                                  class="text-danger mb-2"
                                >
                                  <i class="las la-times-circle scale5"></i>
                                </a>
                              </div>
                              <div
                                class="d-flex pb-3 border-bottom mb-3 align-items-end"
                              >
                                <div class="me-auto">
                                  <p class="text-black font-w600 mb-2">
                                    Saturday, June 27th
                                  </p>
                                  <ul>
                                    <li>
                                      <i class="las la-clock"></i>09:00 - 10:30 AM
                                    </li>
                                    <li><i class="las la-user"></i>Dr. Samantha</li>
                                  </ul>
                                </div>
                                <a
                                  href="javascript:void(0)"
                                  class="text-success me-3 mb-2"
                                >
                                  <i class="las la-check-circle scale5"></i>
                                </a>
                                <a
                                  href="javascript:void(0)"
                                  class="text-danger mb-2"
                                >
                                  <i class="las la-times-circle scale5"></i>
                                </a>
                              </div>
                              <div
                                class="d-flex pb-3 border-bottom mb-3 align-items-end"
                              >
                                <div class="me-auto">
                                  <p class="text-black font-w600 mb-2">
                                    Wednesday, June 3th
                                  </p>
                                  <ul>
                                    <li>
                                      <i class="las la-clock"></i>09:00 - 10:30 AM
                                    </li>
                                    <li><i class="las la-user"></i>Dr. Samantha</li>
                                  </ul>
                                </div>
                                <a
                                  href="javascript:void(0)"
                                  class="text-success me-3 mb-2"
                                >
                                  <i class="las la-check-circle scale5"></i>
                                </a>
                                <a
                                  href="javascript:void(0)"
                                  class="text-danger mb-2"
                                >
                                  <i class="las la-times-circle scale5"></i>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                 </div>
             </div>
        </div>
    </div>


  </div>




  <script src="{{'assets/vendor/global/global.min.js'}}"></script>
  <script src="{{'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js'}}"></script>
  <script src="{{'assets/js/custom.min.js'}}"></script>
  <script src="{{'assets/js/deznav-init.js'}}"></script>
  <script src="{{'assets/vendor/bootstrap-datetimepicker/js/moment.js'}}"></script>
  <script src="{{'assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'}}"></script>

  @push('scripts')
    <script>
        (function($) {
            var table = $("#stafftypeTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        })(jQuery);
    </script>


@endpush
@include('layouts.partials.datatable')
  <script>
    $(function () {
      $("#datetimepicker1").datetimepicker({
        inline: true,
      });
    });
  </script>
  @endsection