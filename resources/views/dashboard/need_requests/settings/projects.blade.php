<x-layout :title="trans('need_requests.actions.project_settings')" :breadcrumbs="['dashboard.need_requests.index']">
    @component('dashboard::components.table-box')
        @slot('title', trans('need_requests.actions.project_settings'))

        <thead>
        <tr>
            <th>@lang('need_requests.attributes.project_id')</th>
            <th>@lang('الموعد النهائي (Deadline)')</th>
            <th>الحالة في طلب الاحتياج</th>
            <th style="width: 250px">التحكم</th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            @php
                $latestActivation = $project->latestNeedRequestProject;
                $isEnabled = $latestActivation && $latestActivation->is_enabled;
                $isExpired = $latestActivation && $latestActivation->deadline && $latestActivation->deadline->isPast();
            @endphp
            <tr>
                <td>{{ $project->name }}</td>
                <td>
                    @if($latestActivation && $latestActivation->deadline)
                        <span class="{{ $isExpired ? 'text-danger font-weight-bold' : '' }}">
                            {{ $latestActivation->deadline->format('Y-m-d H:i') }}
                            @if($isExpired) <small>(منتهي)</small> @endif
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <span id="status-{{ $project->id }}" class="badge badge-{{ ($isEnabled && !$isExpired) ? 'success' : ($isEnabled ? 'warning' : 'danger') }}">
                        @if(!$isEnabled) مخفي @elseif($isExpired) منتهي @else متاح @endif
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-{{ $isEnabled ? 'danger' : 'success' }} toggle-project" 
                                data-id="{{ $project->id }}"
                                data-url="{{ route('dashboard.need_requests.settings.projects.toggle', $project->id) }}">
                            {{ $isEnabled ? 'إخفاء' : 'إتاحة' }}
                        </button>
                        <a href="{{ route('dashboard.need_requests.bulk_create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> تعديل / تمديد
                        </a>
                    </div>
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
