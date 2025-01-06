@php $editing = isset($activename) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <label for="sendername_id">Select Sender Name</label>
        <select class="form-control" id="sendername_id" name="sendername_id" required>
            <option value="" disabled selected>Select Sender Name</option>
            @foreach($senderNames as $sendername)
                <option value="{{ $sendername->id }}" 
                    {{ (old('sendername_id', $editing ? $activename->sendername_id : null)) == $sendername->id ? 'selected' : '' }}>
                    {{ $sendername->name }}
                </option>
            @endforeach
        </select>
    </x-inputs.group>
</div>
