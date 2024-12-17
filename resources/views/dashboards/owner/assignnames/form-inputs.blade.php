@php $editing = isset($sendername) @endphp

<div class="row">
    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="client_id" label="Client Name" required>
            @php $selected = old('client_id', ($editing ? $sendername->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the CLient</option>
            @foreach ($clients as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-4">
        <x-inputs.select name="sendername_id" label="Add Package" required>
            @php $selected = old('sendername_id', ($editing ? $sendername->sendername_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Sendername</option>
            @foreach ($sendernames as $value => $label)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>{{ $label }}
                </option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
