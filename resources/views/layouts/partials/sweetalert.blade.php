@push('page_css')
{{-- sweetalert  --}}
<link href="{{asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet" />

@endpush
@push('page_js')
<!-- SweetAlert2 -->
<script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endpush

@if(session()->has('success'))
<script>
   swal(
    'Success!',
    {{ session('success') }}
   );
</script>
@endif

@if(session()->has('error'))
<script>
  swal(
   'error!',
   {{ session('error') }}
  );
</script>
@endif

@if(session()->has('warning'))
<script>
  swal(
   'warning!',
   {{ session('warning') }}
  );
</script>

@endif

@if(session()->has('info'))
<script>
  swal(
   'info!',
   {{ session('info') }}
  );
</script>

@endif