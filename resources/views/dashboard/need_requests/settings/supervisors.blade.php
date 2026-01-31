<x-layout :title="trans('need_requests.actions.supervisor_settings')" :breadcrumbs="['dashboard.need_requests.index']">
    @component('dashboard::components.table-box')
        @slot('title', trans('need_requests.actions.supervisor_settings'))

        <thead>
        <tr>
            <th>@lang('need_requests.attributes.supervisor_id')</th>
            <th>البريد الإلكتروني</th>
            <th>الحالة</th>
            <th style="width: 160px">التحكم</th>
        </tr>
        </thead>
        <tbody>
        @foreach($supervisors as $supervisor)
            <tr>
                <td>{{ $supervisor->name }}</td>
                <td>{{ $supervisor->email }}</td>
                <td>
                    <span id="status-{{ $supervisor->id }}" class="badge badge-{{ $supervisor->needRequestSetting && $supervisor->needRequestSetting->is_enabled ? 'success' : 'danger' }}">
                        {{ $supervisor->needRequestSetting && $supervisor->needRequestSetting->is_enabled ? 'مفعل' : 'معطل' }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-{{ $supervisor->needRequestSetting && $supervisor->needRequestSetting->is_enabled ? 'danger' : 'success' }} toggle-supervisor" 
                            data-id="{{ $supervisor->id }}"
                            data-url="{{ route('dashboard.need_requests.settings.toggle', $supervisor->id) }}">
                        {{ $supervisor->needRequestSetting && $supervisor->needRequestSetting->is_enabled ? 'تعطيل' : 'تفعيل' }}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    @endcomponent

    @push('scripts')
    <script>
        $(document).on('click', '.toggle-supervisor', function() {
            const btn = $(this);
            const id = btn.data('id');
            const url = btn.data('url');

            $.post(url, {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if(response.success) {
                    const statusBadge = $('#status-' + id);
                    if(response.is_enabled) {
                        statusBadge.removeClass('badge-danger').addClass('badge-success').text('مفعل');
                        btn.removeClass('btn-success').addClass('btn-danger').text('تعطيل');
                    } else {
                        statusBadge.removeClass('badge-success').addClass('badge-danger').text('معطل');
                        btn.removeClass('btn-danger').addClass('btn-success').text('تفعيل');
                    }
                }
            });
        });
    </script>
    @endpush
</x-layout>
