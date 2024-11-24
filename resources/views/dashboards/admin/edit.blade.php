@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Edit Invoice Status</h2>
        </div>

        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary"
                        href="#">Edit Client Admin</a></span></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <x-form
                method="PUT"
                action="{{ route('admin.update.profile', $clientadmin) }}"
                class="mt-4"
            >
           

            <!-- Additional Fields for Client Admin -->
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
                <a href="#" class="btn btn-light">
                    <i class="fa-solid fa-backload"></i> @lang('crud.common.back')
                </a>

                <button type="submit" class="btn btn-primary float-right">
                    <i class="icon ion-md-save"></i> @lang('crud.common.update')
                </button>
            </div>

            </x-form>
        </div>
    </div>
</div>
@endsection
