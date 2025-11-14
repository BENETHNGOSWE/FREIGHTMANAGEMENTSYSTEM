{{-- 
    This file is intended to be loaded via AJAX into the modal body 
    It expects $allloads, $container_id, and $loadedIdsForContainer to be passed from the controller.
--}}
<div id="modal-load-content">
    
    {{-- Progress Bar Container (Hidden by default, used only during POST submission) --}}
    <div id="modal-progress-bar-container" style="display:none; width:100%; background-color:#ddd; margin-bottom:15px;">
        <div id="modal-progress-bar" style="width:0%; height:25px; background-color:#4caf50; text-align:center; color:white;">
            0%
        </div>
    </div>

    <div class="table-responsive">
        <table id="loadTable" class="table table-borderless table-hover">
            <thead>
                <tr>
                    <th style="width:50px;">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>Customer Name</th>
                    <th>Load No</th>
                    <th>Load Name</th>
                    <th>Weight</th>
                    <th>Volume</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allloads as $loadpackage)
                    <tr>
                        <td>
                            <input 
                                type="checkbox" 
                                class="package-checkbox" 
                                value="{{ $loadpackage->id }}"
                                {{-- Check if package is already loaded --}}
                                {{ in_array($loadpackage->id, $loadedIdsForContainer ?? []) ? 'checked' : '' }}>
                        </td>
                        <td>{{ strtoupper($loadpackage->customer->customer_firstname . ' ' . $loadpackage->customer->customer_lastname) ?? '-' }}</td>
                        <td style="color:green; font-weight:700;">{{ strtoupper($loadpackage->load_no) ?? '-' }}</td>
                        <td>{{ strtoupper($loadpackage->load_name) ?? '-' }}</td>
                        <td>{{ strtoupper($loadpackage->load_weight_value . ' ' . $loadpackage->unittype->unittype_name) ?? '-' }}</td>
                        <td style="color:rgb(255,117,19); font-weight:700;">{{ strtoupper($loadpackage->load_volume) ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">No packages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Hidden input to pass container ID back to the main JS when submitting --}}
    <input type="hidden" id="modal_container_id" value="{{ $container_id }}">
</div>
