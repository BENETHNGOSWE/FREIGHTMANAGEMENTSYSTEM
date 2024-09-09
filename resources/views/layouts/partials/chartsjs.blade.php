  @push('page_js')
  <script src="{{asset('assets/vendor/chart-js/chart.bundle.min.js')}}"></script>
  <!-- Chart piety plugin files -->
  <script src="{{asset('assets/vendor/peity/jquery.peity.min.js')}}"></script>
  <!-- Apex Chart -->
  <script src="{{asset('assets/vendor/apexchart/apexchart.js')}}"></script>
  <!-- Dashboard 1 -->
  <script src="{{asset('assets/js/dashboard/dashboard-1.js')}}"></script>

  @endpush

  @push('page_css')
  <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}" />
  @endpush