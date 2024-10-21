

@extends('layouts._master')

@section('content')
<div class="container-fluid">
    <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
        <div class="me-auto">
            <h2 class="text-black font-w600">Send Message</h2>
        </div>
        <div>
           <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.create.loadpackage') }}">Send Message</a></span></p>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.sendmessage') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="send_to_all">
                            <input type="checkbox" id="send_to_all" name="send_to_all" value="1"> Send to All Customers
                        </label>
                    </div>
                    <div class="table-responsive">
                        <table id="labtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th class="text-left" style="white-space: normal; word-wrap: break-word;">
                                       Customer Name
                                    </th>
                                    <th class="text-left" style="white-space: normal; word-wrap: break-word;">
                                        Customer Mobile No.
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="customers[]" value="{{ $customer->phone }}">
                                        </td>
                                        <td>{{ strtoupper($customer->customer_firstname) }} {{ strtoupper($customer->customer_lastname) }}</td>
                                        <td>{{ strtoupper($customer->phone) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="10">
                                            @lang('crud.common.no_items_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Message Input -->
                    <div class="form-group mt-3">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" class="form-control" required></textarea>
                    </div>
    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
    
</div>

<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

<script>
    // Initialize CKEditor on the 'message' textarea
    CKEDITOR.replace('message', {
        height: 300,
        removePlugins: 'elementspath',  // Removes the bottom "Element path"
        resize_enabled: false  // Disables resizing
    });
</script>

<script>
    // Disable customer checkboxes when "Send to All" is checked
    document.getElementById('send_to_all').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('input[name="customers[]"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.disabled = this.checked;
        }.bind(this));
    });

    // Store selected customer names and their phone numbers
    document.querySelector('form').addEventListener('submit', function(event) {
        var selectedCustomers = [];
        var checkboxes = document.querySelectorAll('input[name="customers[]"]:checked');
        checkboxes.forEach(function(checkbox) {
            var customerName = checkbox.nextSibling.textContent.trim();
            selectedCustomers.push(customerName);
        });

        // Set the hidden input value with customer names
        document.getElementById('selected_customers').value = selectedCustomers.join(', ');

        // You can also use this for logging or checking the selected customers
        console.log(selectedCustomers);
    });
</script>
@endsection
@push('scripts')
    <script>
        (function($) {
            var table = $("#labtypeTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        })(jQuery);
    </script>

   
@endpush
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')