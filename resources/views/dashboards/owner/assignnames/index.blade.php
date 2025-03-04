@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Sendername</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Sendername
                            List</a></span>
                </p>
            </div>
            @if (auth()->user()->hasRole('owner'))
                <div>
                    <a href="{{ route('owner.create.assignname') }}" class="btn btn-primary me-3">+ Add Sendername</a>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.sendernames.index_title')</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        s/n
                                    </th>
                                    <th class="text-left">
                                        Client Name
                                    </th>
                                    <th class="text-left">
                                        SenderName
                                    </th>
                                    <th class="text-center">
                                        @lang('crud.common.actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>{{ strtoupper($client->client_name) ?? '-' }}</td>
                                        <td>
                                            @if ($client->sendernames->isNotEmpty())
                                                <ul>
                                                    @foreach ($client->sendernames as $sendername)
                                                        <li>{{ $sendername->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <em>No sender names assigned</em>
                                            @endif
                                        </td>
                                        {{-- <td class="text-center" style="width: 134px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @can('update', $sendername)
                                                    <a href="{{ route('owner.edit.sendername', $sendername) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                    @endcan @can('view', $sendername)
                                                    <a href="{{ route('owner.show.sendername', $sendername) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </a>
                                                    @endcan @can('delete', $sendername)
                                                    <form class="delete_form"
                                                        action="{{ route('owner.delete.sendername', $sendername) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger delete_button">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="5">
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
    <script>
        (function($) {
            var table = $("#loadtypeTable").DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        })(jQuery);
    </script>

    <script>
        const deleteButtons = document.querySelectorAll(".delete_button");

        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                swal({
                    title: "Are you sure to delete ?",
                    text: "You will not be able to recover deleted data !!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it !!",
                    cancelButtonText: "No, cancel it !!",
                    closeOnConfirm: !1,
                    closeOnCancel: !1
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
    </script>
@endpush
@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
