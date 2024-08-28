@php $editing = isset($branch) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="client_id" label="Client" required>
            @php $selected = old('client_id', ($editing ? $branch->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client</option>
            @foreach($clients as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="branch_name"
            label="Branch Name"
            :value="old('branch_name', ($editing ? $branch->branch_name : ''))"
            maxlength="255"
            placeholder="Branch Name"
            required
        ></x-inputs.text>
    </x-inputs.group>
</div>
