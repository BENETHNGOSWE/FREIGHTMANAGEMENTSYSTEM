@php $editing = isset($subscription) @endphp

<div class="row">
    <div class="row">
        <div class="col-sm-6">
            <form method="POST" action="{{ route('store.subscriptions') }}" class="mt-4">
                @csrf

            <x-inputs.group class="col-sm-12">
                <x-inputs.select name="client_id" class="select2" label="Client name" >
                    @php $selected = old('client_id', ($editing ? $subscription->client_id : '')) @endphp
                    <option disabled {{ empty($selected) ? 'selected' : '' }}>--Choose Client--</option>
                    @foreach ($clients as $value => $label)
                        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </x-inputs.select>
                
            </x-inputs.group>
            <x-inputs.group class="col-sm-12">
                <x-inputs.select name="package_id" class="package" label="Package Type">
                    @php $selected = old('package_id', ($editing ? $subscription->package_id : '')) @endphp
                    <option disabled {{ empty($selected) ? 'selected' : '' }}>--Choose Package Type--</option>
                    @foreach ($kodapackages as $value => $label)
                        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </x-inputs.select>
            </x-inputs.group>  

            <x-inputs.group class="col-sm-12">
                <x-inputs.select name="status" label="Payment Status">
                    @php $selectedStatus = old('status', ($editing ? $subscription->status : '')) @endphp
                    <option disabled {{ empty($selectedStatus) ? 'selected' : '' }}>--Choose Payment Status--</option>
                    <option value="0" {{ $selectedStatus == '0' ? 'selected' : '' }}>Paid</option>
                    <option value="1" {{ $selectedStatus == '1' ? 'selected' : '' }}>Partial</option>
                    <option value="2" {{ $selectedStatus == '2' ? 'selected' : '' }}>Not Paid</option>
                </x-inputs.select>
            </x-inputs.group>
            

            <x-inputs.group class="col-sm-12">
                <x-inputs.number
                    name="duration_in_month"
                    label="Duration in Months"
                    value="{{ old('duration_in_month', ($editing ? $subscription->duration_in_month : '')) }}"
                    max="48"
                    placeholder="Duration"
                ></x-inputs.number>
            </x-inputs.group>
        
            <x-inputs.group class="col-sm-12">
                <x-inputs.date
                    name="start_date"
                    label="Start Date"
                    :value="old('start_date', ($editing ? $subscription->start_date : ''))"      
                    placeholder="Start Date"
                ></x-inputs.number>
            </x-inputs.group>

            <input type="submit" value="submit" class="btn btn-primary">
        </form>

            
              
        </div>
    
    </div>
    
</div>
@push("scripts")

@endpush

@include('layouts.partials.select2')