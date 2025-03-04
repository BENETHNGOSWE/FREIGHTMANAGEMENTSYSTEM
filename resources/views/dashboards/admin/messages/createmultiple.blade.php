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
                    <form action="{{ route('admin.sendmessage.multiple') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="sender_id">Select Sender Name:</label>
                            <select id="sender_id" name="sender_id" class="form-control" required>
                                <option value="" disabled selected>Select Sender Name</option>
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
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="customers[]" value="{{ $customer->phone }}">
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
                        <button type="submit" class="btn btn-primary" {{ $canSendMessages ? '' : 'disabled' }}>Send Message</button>
                    </form>
                    @if (!$canSendMessages)
                        <div class="alert alert-warning mt-3">
                            You have reached your message limit. Please contact the administrator to reset your limit or
                            assign more messages.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

    <script>
               CKEDITOR.replace('message', {
            height: 300,
            removePlugins: 'elementspath',
            resize_enabled: false,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_P,
            fillEmptyBlocks: false,
            tabSpaces: 0,
            basicEntities: false,
            entities: false,
            entities_latin: false,
            entities_greek: false,
            entities_processNumerical: false,
            forceSimpleAmpersand: true,
            contentsCss: ['body { font-family: sans-serif; }'],
            autoParagraph: false,
            allowedContent: true
        });

        CKEDITOR.instances.message.on('instanceReady', function(ev) {
            const editor = ev.editor;

            editor.dataProcessor.htmlFilter.addRules({
                text: function(text) {
                    return text
                        .replace(/&nbsp;/g, ' ')
                        .replace(/\s+/g, ' ');
                }
            });

            editor.on('paste', function(ev) {
                const text = ev.data.dataValue
                    .replace(/<p>/g, '\n')
                    .replace(/<\/p>/g, '')
                    .replace(/<br>/g, '\n')
                    .replace(/&nbsp;/g, ' ')
                    .replace(/<[^>]*>/g, '')
                    .trim();
                ev.data.dataValue = text;
            });
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const editor = CKEDITOR.instances.message;
            let content = editor.getData();

            content = processMessageContent(content);
            editor.setData(content);
        });

        function processMessageContent(content) {
            return content
                .replace(/&nbsp;/g, ' ')
                .replace(/\s+/g, ' ')
                .replace(/<p>\s*<\/p>/g, '\n')
                .replace(/<p>/g, '')
                .replace(/<\/p>/g, '\n')
                .replace(/<br\s*\/?/g, '\n')
                .replace(/<[^>]*>/g, '')
                .replace(/\n\s*\n/g, '\n')
                .trim();
        }

        const selectedCustomers = new Set();

        // Handle checkbox selection
        document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedCustomers.add(this.value);
                } else {
                    selectedCustomers.delete(this.value);
                }
                updateHiddenField();
            });
        });

        // Update hidden field with selected customers
        function updateHiddenField() {
            document.getElementById('selected_customers').value = Array.from(selectedCustomers).join(',');
        }

        // Restore selections when the page is reloaded
        document.addEventListener('DOMContentLoaded', function() {
            const hiddenField = document.getElementById('selected_customers');
            if (hiddenField.value) {
                hiddenField.value.split(',').forEach(phone => {
                    selectedCustomers.add(phone);
                    const checkbox = document.querySelector(`.customer-checkbox[value="${phone}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        });

        // Handle the 'Send to All' checkbox
        document.getElementById('send_to_all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="customerleads[]"]');
            const isChecked = this.checked;

            checkboxes.forEach(checkbox => {
                checkbox.disabled = isChecked;
                if (isChecked) {
                    checkbox.checked = false;
                }
            });

            // Show all rows when "Send to All" is selected
            document.querySelectorAll('.customer-row').forEach(row => {
                row.style.display = '';
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
                lengthChange: true,
                lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "All"]],
            });
        })(jQuery);
    </script>
@endpush

@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
