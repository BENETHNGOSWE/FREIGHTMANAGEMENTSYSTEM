@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Send Message</h2>
            </div>
            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('admin.create.loadpackage') }}">Send
                            Message</a></span></p>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.sendmessage.lead') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="sender_id">Sender ID:</label>
                            <select id="sender_id" name="sender_id" class="form-control" required>
                                <option value="" disabled selected>Select Sender ID</option>
                                @foreach ($sendernames as $sendername)
                                    <option value="{{ $sendername->name }}">{{ $sendername->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="send_to_all">
                                <input type="checkbox" id="send_to_all" name="send_to_all" value="1"> Send to All
                                Customers
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
                                    @forelse($customerleads as $customer)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="customerleads[]"
                                                    value="{{ $customer->phone }}">
                                            </td>
                                            <td>{{ strtoupper($customer->customer_firstname) }}
                                                {{ strtoupper($customer->customer_lastname) }}</td>
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
        // Initialize CKEditor
        CKEDITOR.replace('message', {
            height: 300,
            removePlugins: 'elementspath',
            resize_enabled: false,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_P,
            // Configure entities handling
            entities: false,
            entities_latin: false,
            entities_greek: false,
            entities_processNumerical: false,
            // Configure how spaces are handled
            basicEntities: true,
            forceSimpleAmpersand: true
        });

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            // Get the editor instance
            const editor = CKEDITOR.instances.message;

            // Get the editor content
            let content = editor.getData();

            // Clean up the content
            content = content
                // Replace &nbsp; with regular spaces
                .replace(/&nbsp;/g, ' ')
                // Replace multiple consecutive spaces with a single space
                .replace(/\s+/g, ' ')
                // Replace <p> tags with newlines
                .replace(/<p>/g, '\n')
                .replace(/<\/p>/g, '')
                // Remove any remaining HTML tags
                .replace(/<[^>]*>/g, '')
                // Trim extra whitespace
                .trim();

            // Update the editor content with cleaned text
            editor.setData(content);
        });

        // Enable or disable checkboxes based on 'Send to All' checkbox
        document.getElementById('send_to_all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="customerleads[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.disabled = this.checked;
            });
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
