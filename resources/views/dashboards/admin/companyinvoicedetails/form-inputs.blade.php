@php $editing = isset($companyinvoicedetail) @endphp

<div class="row">
    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="company_name"
            label="Company Name"
            :value="old('company_name', ($editing ? $companyinvoicedetail->company_name : ''))"
            maxlength="255"
            placeholder="Company Name"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="office_phone"
            label="Office Phone"
            :value="old('office_phone', ($editing ? $companyinvoicedetail->office_phone : ''))"
            maxlength="255"
            placeholder="Office Phone"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="tin_number"
            label="TIN Number"
            :value="old('tin_number', ($editing ? $companyinvoicedetail->tin_number : ''))"
            maxlength="255"
            placeholder="TIN Number"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-6">
        <x-inputs.text
            name="tagline"
            label="Company Tagline"
            :value="old('tagline', ($editing ? $companyinvoicedetail->tagline : ''))"
            maxlength="255"
            placeholder="Company Tagline"
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.file
            name="logo"
            label="Company Logo"
            maxlength="255"
            placeholder="Company Logo"
        ></x-inputs.file>
        @if($editing && $companyinvoicedetail->logo)
            <div class="mt-2">
                <img src="{{ asset('storage/company_logos/' . $companyinvoicedetail->logo) }}" 
                     alt="Current logo" 
                     class="img-thumbnail" 
                     style="max-height: 100px">
            </div>
        @endif
    </x-inputs.group>
</div>