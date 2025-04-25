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
                    <form action="{{ route('admin.sendmessage.lead') }}" method="POST"
                        {{ $canSendMessages ? '' : 'disabled' }}>
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

                        <div class="form-group d-flex align-items-center">
                            <div class="me-4">
                                <label for="send_to_all" class="mb-0">
                                    <input type="checkbox" id="send_to_all" name="send_to_all" value="1"> Send to All
                                    Customers
                                </label>
                            </div>
                            <div>
                                <label for="send_by_category" class="mb-0">
                                    <input type="checkbox" id="send_by_category" name="send_by_category" value="1">
                                    Send by Category
                                </label>
                            </div>
                        </div>

                        <div id="category_selection" class="form-group" style="display: none;">
                            <label for="selected_category">Select Category:</label>
                            <select id="selected_category" name="selected_category" class="form-control">
                                <option value="" disabled selected>Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->categoryname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="date_filter" class="form-group mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="start_date">From Date:</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date">To Date:</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" id="apply_date_filter" class="btn btn-sm btn-info">Apply Date Filter</button>
                                <button type="button" id="clear_date_filter" class="btn btn-sm btn-outline-secondary">Clear Date Filter</button>
                            </div>
                        </div>

                        <input type="hidden" id="selected_customers" name="selected_customers" value="">

                        <div class="table-responsive">
                            <table id="labtypeTable" class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Customer Name</th>
                                        <th>Customer Mobile No.</th>
                                        <th>Customer Categories</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customerleads as $customer)
                                        <tr class="customer-row"
                                            data-categories="{{ $customer->customercategories->pluck('id')->toJson() }}"
                                            data-date="{{ $customer->created_at->format('Y-m-d') }}">
                                            <td>
                                                <input type="checkbox" name="customerleads[]" class="customer-checkbox"
                                                    value="{{ $customer->phone }}">
                                            </td>
                                            <td>{{ strtoupper($customer->customer_firstname) }}
                                                {{ strtoupper($customer->customer_lastname) }}</td>
                                            <td>{{ strtoupper($customer->phone) }}</td>
                                            <td>
                                                @if ($customer->customercategories->isNotEmpty())
                                                    {{ strtoupper($customer->customercategories->pluck('categoryname')->implode(', ')) }}
                                                @else
                                                    No categories assigned
                                                @endif
                                            </td>
                                            <td>{{ strtoupper($customer->created_at->format('Y-m-d')) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center py-5" colspan="5">No items found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group mt-3">
                            <label for="message">Message: <span id="messageCount" class="badge bg-info ms-2">0 Messages</span></label>
                            <textarea id="message" name="message" class="form-control" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" {{ $canSendMessages ? '' : 'disabled' }}>Send
                            Message</button>
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
                    return text.replace(/ /g, ' ').replace(/\s+/g, ' ');
                }
            });
            editor.on('paste', function(ev) {
                const text = ev.data.dataValue
                    .replace(/<p>/g, '\n')
                    .replace(/<\/p>/g, '')
                    .replace(/<br>/g, '\n')
                    .replace(/ /g, ' ')
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
                .replace(/ /g, ' ')
                .replace(/\s+/g, ' ')
                .replace(/<p>\s*<\/p>/g, '\n')
                .replace(/<p>/g, '')
                .replace(/<\/p>/g, '\n')
                .replace(/<br\s*\/?>/g, '\n')
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

            if (isChecked) {
                document.getElementById('send_by_category').checked = false;
                document.getElementById('category_selection').style.display = 'none';
                document.getElementById('date_filter').style.display = 'none';
            } else {
                document.getElementById('date_filter').style.display = 'block';
            }

            checkboxes.forEach(checkbox => {
                checkbox.disabled = isChecked;
                if (isChecked) {
                    checkbox.checked = false;
                }
            });

            document.querySelectorAll('.customer-row').forEach(row => {
                row.style.display = '';
            });
            
            // Clear date filters when selecting 'Send to All'
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            
            selectedCustomers.clear();
            if (isChecked) {
                // Add all customer phones to selectedCustomers
                document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
                    selectedCustomers.add(checkbox.value);
                });
            }
            updateHiddenField();
        });

        // Handle the 'Send by Category' checkbox
        document.getElementById('send_by_category').addEventListener('change', function() {
            const isChecked = this.checked;
            const categorySelection = document.getElementById('category_selection');

            categorySelection.style.display = isChecked ? 'block' : 'none';
            document.getElementById('date_filter').style.display = 'block';

            if (isChecked) {
                document.getElementById('send_to_all').checked = false;
            }

            const checkboxes = document.querySelectorAll('input[name="customerleads[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.disabled = isChecked;
                if (isChecked) {
                    checkbox.checked = false;
                }
            });

            if (!isChecked) {
                document.getElementById('selected_category').value = '';
                document.querySelectorAll('.customer-row').forEach(row => {
                    row.style.display = '';
                });
            } else {
                document.querySelectorAll('.customer-row').forEach(row => {
                    row.style.display = 'none';
                });
            }

            selectedCustomers.clear();
            updateHiddenField();
        });

        // Handle category selection change
        document.getElementById('selected_category').addEventListener('change', function() {
            const selectedCategory = this.value;
            const selectedCategoryId = parseInt(selectedCategory);
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Reset all checkboxes and selections
            selectedCustomers.clear();

            // Show all customers who have the selected category
            document.querySelectorAll('.customer-row').forEach(row => {
                const categoryIds = JSON.parse(row.getAttribute('data-categories') || '[]');
                const checkbox = row.querySelector('.customer-checkbox');
                const dateCell = row.getAttribute('data-date');
                const rowDate = new Date(dateCell);
                
                // Check date filter if active
                let showByDate = true;
                if (startDate || endDate) {
                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59);
                        showByDate = rowDate >= start && rowDate <= end;
                    } else if (startDate) {
                        showByDate = rowDate >= new Date(startDate);
                    } else if (endDate) {
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59);
                        showByDate = rowDate <= end;
                    }
                }

                // Check if the selected category is one of the customer's categories AND date filter passes
                if (categoryIds.includes(selectedCategoryId) && showByDate) {
                    // Show and select this customer
                    row.style.display = '';
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.disabled = false;
                        selectedCustomers.add(checkbox.value);
                    }
                } else {
                    // Hide this customer
                    row.style.display = 'none';
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            });

            updateHiddenField();
        });

        // Apply date filter button
        document.getElementById('apply_date_filter').addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const selectedCategory = document.getElementById('selected_category').value;
            const selectedCategoryId = selectedCategory ? parseInt(selectedCategory) : null;
            const isSendByCategory = document.getElementById('send_by_category').checked;
            
            // Reset selections when applying a new filter
            selectedCustomers.clear();
            
            // Filter rows by date (and respect category filter if active)
            document.querySelectorAll('.customer-row').forEach(row => {
                const checkbox = row.querySelector('.customer-checkbox');
                const dateCell = row.getAttribute('data-date');
                const rowDate = new Date(dateCell);
                const categoryIds = JSON.parse(row.getAttribute('data-categories') || '[]');
                
                // Check if date is within range
                let showByDate = true;
                if (startDate || endDate) {
                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59); // Include the entire end day
                        
                        showByDate = rowDate >= start && rowDate <= end;
                    } else if (startDate) {
                        // Only check start date
                        showByDate = rowDate >= new Date(startDate);
                    } else if (endDate) {
                        // Only check end date
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59);
                        showByDate = rowDate <= end;
                    }
                }
                
                // Check category filter if active
                let showByCategory = true;
                if (isSendByCategory && selectedCategoryId) {
                    showByCategory = categoryIds.includes(selectedCategoryId);
                }
                
                // Determine if row should be shown based on all filters
                if (showByDate && showByCategory) {
                    // Show and select this customer
                    row.style.display = '';
                    if (checkbox) {
                        checkbox.checked = true;
                        selectedCustomers.add(checkbox.value);
                    }
                } else {
                    // Hide this customer
                    row.style.display = 'none';
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            });
            
            updateHiddenField();
        });

        // Clear date filter button
        document.getElementById('clear_date_filter').addEventListener('click', function() {
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            
            // Reset date filtering, but maintain category filtering if active
            const isSendByCategory = document.getElementById('send_by_category').checked;
            const selectedCategory = document.getElementById('selected_category').value;
            
            if (isSendByCategory && selectedCategory) {
                // If category is selected, reapply only category filter
                document.getElementById('selected_category').dispatchEvent(new Event('change'));
            } else {
                // If no category filter active, show all rows
                document.querySelectorAll('.customer-row').forEach(row => {
                    row.style.display = '';
                    const checkbox = row.querySelector('.customer-checkbox');
                    if (checkbox) {
                        checkbox.checked = true;
                        selectedCustomers.add(checkbox.value);
                    }
                });
                updateHiddenField();
            }
        });


        // Function to update message count
            function updateMessageCount(content) {
                // Remove HTML tags and extra whitespace for accurate character count
                const cleanContent = content
                    .replace(/<[^>]*>/g, '') // Remove HTML tags
                    .replace(/\s+/g, ' ')    // Replace multiple spaces with single space
                    .trim();
                
                const charCount = cleanContent.length;
                const messageCount = Math.ceil(charCount / 15); 
                document.getElementById('messageCount').textContent = `${messageCount} Message${messageCount !== 1 ? 's' : ''}`;
            }

            // Update message count when CKEditor content changes
            CKEDITOR.instances.message.on('change', function() {
                const content = CKEDITOR.instances.message.getData();
                updateMessageCount(content);
            });

            // Update message count when pasting content
            CKEDITOR.instances.message.on('paste', function(ev) {
                setTimeout(() => {
                    const content = CKEDITOR.instances.message.getData();
                    updateMessageCount(content);
                }, 0);
            });

            // Initial message count update
            document.addEventListener('DOMContentLoaded', function() {
                updateMessageCount(CKEDITOR.instances.message.getData());
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
                lengthMenu: [
                    [10, 20, 50, -1],
                    [10, 20, 50, "All"]
                ],
            });
        })(jQuery);
    </script>
@endpush

@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')