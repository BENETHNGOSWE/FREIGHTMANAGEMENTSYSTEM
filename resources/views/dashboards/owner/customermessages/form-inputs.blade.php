@php $editing = isset($customermessage) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="client_id" label="Client Name" required>
            @php $selected = old('client_id', ($editing ? $customermessage->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client</option>
            @foreach ($clients as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="messageamount"
            label="Message Amount"
            :value="old('messageamount', ($editing ? $customermessage->messageamount : ''))"
            maxlength="255"
            placeholder="Message Amount"
            required
        ></x-inputs.text>
    </x-inputs.group>
</div>
