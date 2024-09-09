@push('page_js')
<script src="{{ asset('assets/vendor/jquery-validation/jquery.validate.min.js')}}"></script>
@endpush

@push('scripts')
<script>
    (function () {
      "use strict";
      var form = document.getElementById("jason_form");
  
      form.addEventListener("submit", function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
  
        form.classList.add("was-validated");
      });
    })();
  </script>
@endpush  