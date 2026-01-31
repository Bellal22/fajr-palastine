<x-layout :title="trans('need_requests.actions.project_settings')" :breadcrumbs="['dashboard.need_requests.index']">
    @component('dashboard::components.table-box')
        @slot('title', trans('need_requests.actions.project_settings'))

        <thead>
        <tr>
            <th>@lang('need_requests.attributes.project_id')</th>
            <th>الحالة في طلب الاحتياج</th>
            <th style="width: 160px">التحكم</th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <tr>
                <td>{{ $project->name }}</td>
                <td>
                    @php
                        $isEnabled = !$project->needRequestProject || $project->needRequestProject->is_enabled;
                    @endphp
                    <span id="status-{{ $project->id }}" class="badge badge-{{ $isEnabled ? 'success' : 'danger' }}">
                        {{ $isEnabled ? 'متاح' : 'مخفي' }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-{{ $isEnabled ? 'danger' : 'success' }} toggle-project" 
                            data-id="{{ $project->id }}"
                            data-url="{{ route('dashboard.need_requests.settings.projects.toggle', $project->id) }}">
                        {{ $isEnabled ? 'إخفاء' : 'إتاحة' }}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    @endcomponent

    @push('scripts')
    <script>
        $(document).on('click', '.toggle-project', function() {
            const btn = $(this);
            const id = btn.data('id');
            const url = btn.data('url');

            $.post(url, {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if(response.success) {
                    const statusBadge = $('#status-' + id);
                    if(response.is_enabled) {
                        statusBadge.removeClass('badge-danger').addClass('badge-success').text('متاح');
                        btn.removeClass('btn-success').addClass('btn-danger').text('إخفاء');
                    } else {
                        statusBadge.removeClass('badge-success').addClass('badge-danger').text('مخفي');
                        btn.removeClass('btn-danger').addClass('btn-success').text('إتاحة');
                    }
                }
            });
        });
    </script>
    @endpush
</x-layout>
