@extends('layouts._master')

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Container</h2>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="appointment-add.html">Container
                            List</a></span>
                </p>
            </div>
            <div>
                <a href="{{ route('admin.create.container') }}" class="btn btn-primary me-3">+ Add Container</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div style="display: flex; justify-content: space-between;">
                            <h4 class="card-title">@lang('crud.containers.index_title')</h4>
                        </div>
                        <table id="loadtypeTable" class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>s/n</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_number')</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_size')</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_volume')</th>
                                    <th class="text-left">@lang('crud.containers.inputs.container_status')</th>
                                    <th class="text-center">@lang('crud.common.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($containers as $container)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strtoupper($container->container_number) ?? '-' }}</td>
                                        <td>{{ strtoupper($container->container_size) ?? '-' }}</td>
                                        <td>{{ strtoupper($container->container_volume) ?? '-' }}</td>
                                        <td>{!! return_status_badge($container->container_status) !!}</td>
                                        <td class="text-center" style="width: 180px;">
                                            <div role="group" aria-label="Row Actions" class="btn-group">
                                                @can('update', $container)
                                                    <a href="{{ route('admin.edit.container', $container) }}">
                                                        <button type="button" class="btn btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </a>
                                                @endcan 
                                                @can('view', $container)
                                                    <a href="{{ route('admin.show.container', $container) }}">
                                                        <button type="button" class="btn btn-outline-primary mx-2">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </a>
                                                @endcan 
                                                @can('delete', $container)
                                                    <form class="delete_form" action="{{ route('admin.delete.container', $container) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger delete_button mx-2">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                <button type="button" class="btn btn-primary load-container-btn" data-container-id="{{ $container->id }}">
                                                    Load Container
                                                </button>
                                                <a href="{{ route('admin.loadlistpdf.container', $container)}}" class="btn btn-secondary mx-2">
                                                    <i class="fas fa-print"></i> Print
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="6">
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
        $(document).ready(function() {
            $('.load-container-btn').on('click', function() {
                var containerId = $(this).data('container-id');
                $.ajax({
                    url: `/containers/${containerId}/load`, 
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            alert('Loads successfully added to container');
                            location.reload();
                        } else {
                            alert('Could not find optimal load combination');
                        }
                    },
                    error: function() {
                        alert('An error occurred while loading the container');
                    }
                });
            });

            $('#loadtypeTable').DataTable({
                searching: true,
                paging: true,
                select: false,
                lengthChange: false,
            });
        });
    </script>
@endpush

@include('layouts.partials.datatable')
@include('layouts.partials.sweetalert')
