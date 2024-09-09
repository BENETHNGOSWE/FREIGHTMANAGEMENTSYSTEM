
@push('page_js')
<script src="{{asset('assets/vendor/bootstrap-datetimepicker/js/moment.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>
<!-- pickdate -->
<script src="{{asset('assets/vendor/pickadate/picker.js')}}"></script>
<script src="{{asset('assets/vendor/pickadate/picker.date.js')}}"></script>

<!-- Pickdate -->
<script src="{{asset('assets/js/plugins-init/pickadate-init.js')}}"></script>
@endpush

@push('page_css')
<link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" />
<!-- Material color picker -->
<link href="{{ asset('assets/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet" />
<!-- Pick date -->
<link rel="stylesheet" href="{{ asset('assets/vendor/pickadate/themes/default.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/pickadate/themes/default.date.css') }}" />
@endpush