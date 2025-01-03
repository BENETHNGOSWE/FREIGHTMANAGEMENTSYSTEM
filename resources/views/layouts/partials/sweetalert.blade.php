@push('page_css')
    {{-- sweetalert  --}}
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush
@push('page_js')
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endpush

@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session()->has('success'))
                Swal.fire('Success!', '{{ session('success') }}', 'success');
            @endif
            @if (session()->has('error'))
                Swal.fire('Error!', '{{ session('error') }}', 'error');
            @endif
            @if (session()->has('warning'))
                Swal.fire('Warning!', '{{ session('warning') }}', 'warning');
            @endif
            @if (session()->has('info'))
                Swal.fire('Info!', '{{ session('info') }}', 'info');
            @endif
        });
    </script>
@endif
