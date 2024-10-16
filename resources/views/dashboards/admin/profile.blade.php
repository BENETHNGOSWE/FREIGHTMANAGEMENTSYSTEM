@extends('layouts._master')
@section('content')
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head d-flex align-items-center mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">
                    {{ $clientadmin->firstname . ' ' . $clientadmin->middlename . ' ' . $clientadmin->lastname }}
                </h2>
                <p class="mb-0">Company Manager</p>
            </div>

            <!-- Button to trigger modal -->
            <div>
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#updateProfileModal">
                    Update Profile
                </button>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="card col-md-12">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img class="rounded img-fluid" style="width: 90%" src="{{ asset('storage/uploads/' . $clientadmin->client->client_logo) }}" alt="Client Logo">
                                </div>

                                <div class="col-md-8">
                                    <h2 class="pt-2 text-black">{{ $clientadmin->firstname . ' ' . $clientadmin->middlename . ' ' . $clientadmin->lastname }}</h2>
                                    <h4 class="pt-2 text-black">Company Manager</h4>
                                </div>
                            </div>

                            {{-- Tab UI --}}
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border mt-4">
                                        <div class="card-body">
                                            <!-- Nav tabs -->
                                            <div class="default-tab">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-bs-toggle="tab" href="#home">Profile</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-bs-toggle="tab" href="#appointment">Appointment</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                                                        <!-- Profile Details -->
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <p class="mb-0">Full Name</p>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <p class="text-muted mb-0">{{ $clientadmin->firstname . ' ' . $clientadmin->middlename . ' ' . $clientadmin->lastname }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <p class="mb-0">Email</p>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <p class="text-muted mb-0">{{ $clientadmin->email }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <p class="mb-0">Phone Number</p>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <p class="text-muted mb-0">{{ $clientadmin->phone }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <p class="mb-0">Gender</p>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <p class="text-muted mb-0">{{ $clientadmin->client_gender }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="appointment" role="tabpanel">
                                                        <!-- Appointment Details (Placeholder) -->
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
            </div>
        </div>

        <!-- Modal for Update Profile Form -->
        <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="max-width: 800px; width: 100%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                        <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                      
                    </div>
                    <div class="modal-body">
                        <!-- Update Profile Form -->
                        <form method="POST" action="{{ route('admin.update.profile', $clientadmin) }}" class="mt-4">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- First Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname">First Name</label>
                                        <input type="text" name="firstname" value="{{ old('firstname', $clientadmin->firstname ?? '') }}" class="form-control" placeholder="First Name">
                                        @error('firstname') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Middle Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" name="middlename" value="{{ old('middlename', $clientadmin->middlename ?? '') }}" class="form-control" placeholder="Middle Name">
                                        @error('middlename') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" name="lastname" value="{{ old('lastname', $clientadmin->lastname ?? '') }}" class="form-control" placeholder="Last Name">
                                        @error('lastname') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Username -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" name="username" value="{{ old('username', $clientadmin->username ?? '') }}" class="form-control" placeholder="Username">
                                        @error('username') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $clientadmin->email ?? '') }}" class="form-control" placeholder="Email">
                                        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', $clientadmin->phone ?? '') }}" class="form-control" placeholder="Phone">
                                        @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Password">
                                        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="mt-4">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="{{ 'assets/vendor/global/global.min.js' }}"></script>
    <script src="{{ 'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js' }}"></script>
    <script src="{{ 'assets/js/custom.min.js' }}"></script>
    <script src="{{ 'assets/js/deznav-init.js' }}"></script>
    <script src="{{ 'assets/vendor/bootstrap-datetimepicker/js/moment.js' }}"></script>
    <script src="{{ 'assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js' }}"></script>

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
        $(function() {
            $("#datetimepicker1").datetimepicker({
                inline: true,
            });
        });
    </script>
@endsection
