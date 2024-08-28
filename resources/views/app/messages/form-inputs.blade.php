@php $editing = isset($message) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="sender_id"
            label="Sender Id"
            :value="old('sender_id', ($editing ? $message->sender_id : ''))"
            maxlength="255"
            placeholder="Sender Id"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="receiver_id"
            label="Receiver Id"
            :value="old('receiver_id', ($editing ? $message->receiver_id : ''))"
            maxlength="255"
            placeholder="Receiver Id"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea name="body" label="Body" maxlength="255" required
            >{{ old('body', ($editing ? $message->body : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="status" label="Status">
            @php $selected = old('status', ($editing ? $message->status : '0')) @endphp
            <option value="0" {{ $selected == '0' ? 'selected' : '' }} >0</option>
            <option value="1" {{ $selected == '1' ? 'selected' : '' }} >1</option>
            <option value="2" {{ $selected == '2' ? 'selected' : '' }} >2</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.checkbox
            name="opened"
            label="Opened"
            :checked="old('opened', ($editing ? $message->opened : 0))"
        ></x-inputs.checkbox>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="client_id" label="Client" required>
            @php $selected = old('client_id', ($editing ? $message->client_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Client</option>
            @foreach($clients as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>
</div>
