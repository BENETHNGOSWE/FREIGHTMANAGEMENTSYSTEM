<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('layouts.partials._meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{$page_title ?? 'Freight Management System'}} </title>
    @include('layouts.partials._global_css')
    @stack('page_css')
</head>

<body>
    @include('layouts.partials._preloader')

    <div id="main-wrapper">
        @include('layouts.partials._nav_header')
        @include('layouts.includes.chatbox')

        @include('layouts.header')

        @include('layouts.sidebar')

        <div class="content-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                     @include('layouts.partials.alerts')
                    </div>
                </div>
               
            </div>
            
            
            @yield('content')
        </div>

        @include('layouts.footer')

    </div>
    @stack('modals')
    @include('layouts.partials._global_js')
    @stack('page_js')
    @include('layouts.partials.sweetalert')
    @stack('scripts')
</body>

</html>
