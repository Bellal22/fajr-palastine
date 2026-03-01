<x-layout :title="trans('need_requests.actions.my_requests')" :breadcrumbs="['dashboard.need_requests.index']">
    @push('styles')
    <style>
        .badge-light-primary {
            background-color: rgba(115, 103, 240, 0.12) !important;
            color: #7367f0 !important;
        }
        .timer-text {
            font-family: 'monospace', sans-serif;
            font-weight: 600;
        }
    </style>
    @endpush
    @component('dashboard::components.table-box')
        @slot('title')
            @lang('need_requests.actions.my_requests') ({{ $need_requests->total() }})
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex justify-content-end">
                    @include('dashboard.need_requests.partials.actions.create')
                </div>
            </th>
        </tr>
        <tr>
            <th>@lang('need_requests.attributes.project_id')</th>
            <th>@lang('need_requests.attributes.status')</th>
            <th>المدة المتبقية</th>
            <th>@lang('need_requests.count')</th>
            <th>@lang('need_requests.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($need_requests as $need_request)
            <tr>
                <td>
                    <a href="{{ route('dashboard.need_requests.show', $need_request) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $need_request->project->name ?? '---' }}
                    </a>
                </td>
                <td>
                    @php
                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                        $color = $statusColors[$need_request->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }}">
                        {{ trans('need_requests.statuses.'.$need_request->status) }}
                    </span>
                </td>
                <td>
                    @if($need_request->isPending() && optional($need_request->project->needRequestProject)->deadline)
                        @php
                            $deadline = $need_request->project->needRequestProject->deadline;
                        @endphp
                        @if($deadline->isPast())
                            <span class="text-danger font-weight-bold">منتهي</span>
                        @else
                            <div class="row-countdown" data-deadline="{{ $deadline->toIso8601String() }}">
                                <span class="badge badge-pill badge-primary timer-text">...</span>
                            </div>
                        @endif
                    @else
                        ---
                    @endif
                </td>
                <td>{{ $need_request->items_count }}</td>
                <td>{{ $need_request->created_at->format('Y-m-d') }}</td>

                <td style="width: 160px">
                    @include('dashboard.need_requests.partials.actions.show')
                    @if($need_request->isPending())
                        @include('dashboard.need_requests.partials.actions.edit')
                        @include('dashboard.need_requests.partials.actions.delete')
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('need_requests.empty')</td>
            </tr>
        @endforelse

        @if($need_requests->hasPages())
            @slot('footer')
                {{ $need_requests->links() }}
            @endslot
        @endif
    @endcomponent
    @push('scripts')
    <script>
        $(document).ready(function() {
            function updateRowTimers() {
                $('.row-countdown').each(function() {
                    const deadline = new Date($(this).data('deadline')).getTime();
                    const now = new Date().getTime();
                    const diff = deadline - now;

                    if (diff <= 0) {
                        $(this).html('<span class="text-danger font-weight-bold">منتهي</span>');
                        return;
                    }

                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    let text = "";
                    if (d > 0) text += d + "ي ";
                    text += String(h).padStart(2, '0') + ":" + String(m).padStart(2, '0') + ":" + String(s).padStart(2, '0');
                    
                    $(this).find('.timer-text').text(text);
                });
            }

            setInterval(updateRowTimers, 1000);
            updateRowTimers();
        });
    </script>
    @endpush
    @include('dashboard.need_requests.partials.skipped_modal')
</x-layout>
