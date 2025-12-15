@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Expenses Cost</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Expenses Cost List</a></span></p>
            </div>
            <div class="d-flex align-items-center">
                {{-- MERGE BUTTON --}}
                <button id="merge-expenses-btn"
                    class="btn btn-warning me-3 shadow-lg disabled:opacity-50"
                    disabled
                    title="Select expenses to merge into one PDF">
                    <i class="fas fa-file-pdf me-2"></i> Merge Selected
                </button>
                <a href="{{ route('admin.create.officeexpense') }}" class="btn btn-primary me-3">+ Add Expenses Cost</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="progress-bar-container" style="display: none; width: 100%; background-color: #ddd; margin-bottom: 15px;">
                            <div id="progress-bar" style="width: 0%; height: 25px; background-color: #4caf50; text-align: center; color: white;">
                                0%
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.officeexpense.index_title')</h4>
                            {{-- Select All Checkbox --}}
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="select-all-expenses">
                                <label class="form-check-label" for="select-all-expenses">Select All</label>
                            </div>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th></th> {{-- Checkbox column --}}
                                    <th>s/n</th>
                                    <th class="text-left">@lang('crud.officeexpense.inputs.expenses_description')</th>
                                    <th class="text-left">@lang('crud.officeexpense.inputs.expenses_cost')</th>
                                    <th class="text-left">@lang('crud.officeexpense.inputs.status')</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($officeexpenses as $container)
                                    <tr>
                                        {{-- Checkbox Column --}}
                                        <td>
                                            <input type="checkbox" 
                                                value="{{ $container->id }}"
                                                class="form-check-input expense-checkbox" />
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper($container->expenses_description) ?? '-' }}</td>
                                        <td>{{ number_format((float) $container->expenses_cost, 2) }}</td>
                                        <td>{!! return_expenses_status_badge($container->status) !!}</td>
                                        <td class="text-center" style="width: 180px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                    <a href="{{ route('admin.edit.officeexpense', $container) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                    <a href="{{ route('admin.pdf.generate', $container->id) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </a>
                                                    <form class="delete_form" action="{{ route('admin.delete.officeexpense', $container) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger delete_button mx-2">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                              
                                               
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="7">
                                            @lang('crud.common.no_items_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        #progress-bar-container {
            width: 100%;
            background-color: #ddd;
            margin-bottom: 15px;
        }
        #progress-bar {
            width: 1%;
            height: 30px;
            background-color: #4caf50;
        }
        .expense-checkbox {
            cursor: pointer;
        }
        #merge-expenses-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <script>
        // Your existing delete button code
        const deleteButtons = document.querySelectorAll(".delete_button");

        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                swal({
                    title: "Are you sure to delete?",
                    text: "You will not be able to recover deleted data !!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it !!",
                    cancelButtonText: "No, cancel it !!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }).then((result) => {
                    if (result.value) {
                        const form = button.closest(".delete_form");
                        form.submit();
                    } else {
                        swal("Cancelled", "Your data is safe", "success");
                    }
                });
            });
        });

        $(document).ready(function() {
            // Your existing container loading code
            $('.load-container-btn').on('click', function() {
                var containerId = $(this).data('container-id');
                swal({
                    title: "Are you sure to load the container?",
                    text: "This will add loads to the container.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes, load it!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }).then((result) => {
                    if (result.value) {
                        showProgressBar(containerId);
                    } else {
                        swal("Cancelled", "The container was not loaded.", "info");
                    }
                });
            });

            $('#loadtypeTable').DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });

            // ----------------------------------------------------
            // NEW MERGE FUNCTIONALITY FOR EXPENSES
            // ----------------------------------------------------
            const mergeButton = document.getElementById('merge-expenses-btn');
            const checkboxes = document.querySelectorAll('.expense-checkbox');
            const selectAllCheckbox = document.getElementById('select-all-expenses');

            // Function to update merge button state
            function updateMergeButtonState() {
                const checkedCount = document.querySelectorAll('.expense-checkbox:checked').length;
                
                if (checkedCount > 0) {
                    mergeButton.disabled = false;
                    mergeButton.classList.remove('disabled:opacity-50');
                    mergeButton.innerHTML = `<i class="fas fa-file-pdf me-2"></i> Merge ${checkedCount} Selected`;
                } else {
                    mergeButton.disabled = true;
                    mergeButton.classList.add('disabled:opacity-50');
                    mergeButton.innerHTML = `<i class="fas fa-file-pdf me-2"></i> Merge Selected`;
                }

                // Update select all checkbox state
                const allChecked = checkedCount === checkboxes.length && checkboxes.length > 0;
                selectAllCheckbox.checked = allChecked;
            }

            // Listen for changes on all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateMergeButtonState);
            });

            // Handle select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateMergeButtonState();
            });

            // Initial check on load
            updateMergeButtonState();

            // Handle merge button click
            // Handle merge button click
mergeButton.addEventListener('click', function() {
    const selectedIds = [];
    const checkedBoxes = document.querySelectorAll('.expense-checkbox:checked');

    if (checkedBoxes.length === 0) {
        swal({
            title: "Selection Required",
            text: "Please select at least one expense to merge.",
            type: "warning",
            confirmButtonText: "OK"
        });
        return;
    }

    if (checkedBoxes.length === 1) {
        swal({
            title: "Multiple Selection Required",
            text: "Please select at least two expenses to merge. For single expense, use the download button.",
            type: "info",
            confirmButtonText: "OK"
        });
        return;
    }

    // Show confirmation before merging
    swal({
        title: "Merge Expenses?",
        text: `You are about to merge ${checkedBoxes.length} expense invoices into one PDF.`,
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Yes, merge them!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.value) {
            // Collect all selected IDs
            checkedBoxes.forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });

            // Build comma-separated IDs string
            const idsString = selectedIds.join(',');

            // Open merged PDF in new window/tab - FIXED ROUTE
            window.open(`/expense/pdf/${idsString}`, '_blank');
            
            // Clear selections after merge
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateMergeButtonState();
        }
    });
});
        });

        // Your existing progress bar functions
        function showProgressBar(containerId) {
            var progressBarContainer = document.getElementById('progress-bar-container');
            var progressBar = document.getElementById('progress-bar');
            var width = 1;
            progressBarContainer.style.display = 'block';

            var interval = setInterval(function() {
                if (width >= 100) {
                    clearInterval(interval);
                    // Make the AJAX request once the progress bar reaches 100%
                    $.ajax({
                        url: `/containers/${containerId}/load`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            hideProgressBar();
                            if (response.success) {
                                swal("Loaded!", "Loads successfully added to container.", "success").then(() => {
                                    location.reload();
                                });
                            } else {
                                swal("Error", "Could not find optimal load combination.", "error");
                            }
                        },
                        error: function() {
                            hideProgressBar();
                            swal("Error", "An error occurred while loading the container.", "error");
                        }
                    });
                } else {
                    width++;
                    progressBar.style.width = width + '%';
                    progressBar.innerHTML = width + '%';
                }
            }, 50); // This will take 5 seconds to reach 100%
        }

        function hideProgressBar() {
            var progressBarContainer = document.getElementById('progress-bar-container');
            progressBarContainer.style.display = 'none';
        }
    </script>
@endpush

@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')