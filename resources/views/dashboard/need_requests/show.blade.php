<x-layout :title="$need_request->project->name ?? trans('need_requests.singular')" :breadcrumbs="['dashboard.need_requests.show', $need_request]">
    <div class="row">
        @php
            $deadline = optional($need_request->project->needRequestProject)->deadline;
        @endphp
        @if($need_request->isPending() && $deadline)
        <div class="col-md-12">
            @push('styles')
            <style>
                .show-countdown {
                    background: #fff;
                    border: 1px solid #7367f0;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    box-shadow: 0 4px 6px rgba(115, 103, 240, 0.05);
                }
                .show-countdown-text {
                    font-weight: 600;
                    color: #5e5873;
                }
                .show-countdown-display {
                    display: flex;
                    gap: 15px;
                }
                .show-countdown-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    min-width: 45px;
                }
                .show-countdown-val {
                    font-size: 1.4rem;
                    font-weight: 700;
                    color: #7367f0;
                    line-height: 1;
                }
                .show-countdown-lab {
                    font-size: 0.7rem;
                    color: #b9b9c3;
                    margin-top: 2px;
                }
            </style>
            @endpush
            
            <div class="show-countdown" id="show-view-countdown">
                <div class="show-countdown-text">
                    <i class="fas fa-hourglass-start text-primary mr-1"></i>
                    متبقي على إغلاق الترشيح:
                </div>
                <div class="show-countdown-display">
                    <div class="show-countdown-item">
                        <span id="show-days" class="show-countdown-val">00</span>
                        <span class="show-countdown-lab">يوم</span>
                    </div>
                    <div class="show-countdown-item">
                        <span id="show-hours" class="show-countdown-val">00</span>
                        <span class="show-countdown-lab">ساعة</span>
                    </div>
                    <div class="show-countdown-item">
                        <span id="show-minutes" class="show-countdown-val">00</span>
                        <span class="show-countdown-lab">دقيقة</span>
                    </div>
                    <div class="show-countdown-item">
                        <span id="show-seconds" class="show-countdown-val">00</span>
                        <span class="show-countdown-lab">ثانية</span>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
                $(document).ready(function() {
                    const deadlineDate = new Date("{{ $deadline->toIso8601String() }}").getTime();
                    
                    const interval = setInterval(function() {
                        const now = new Date().getTime();
                        const diff = deadlineDate - now;

                        if (diff <= 0) {
                            clearInterval(interval);
                            $('#show-view-countdown').fadeOut();
                            return;
                        }

                        const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                        const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const s = Math.floor((diff % (1000 * 60)) / 1000);

                        $('#show-days').text(String(d).padStart(2, '0'));
                        $('#show-hours').text(String(h).padStart(2, '0'));
                        $('#show-minutes').text(String(m).padStart(2, '0'));
                        $('#show-seconds').text(String(s).padStart(2, '0'));
                    }, 1000);
                });
            </script>
            @endpush
        </div>
        @endif
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('title', trans('need_requests.actions.show'))
        <div class="card-header-actions">
            @can('export', $need_request)
                <a href="{{ route('dashboard.need_requests.export', $need_request) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> @lang('people.actions.export')
                </a>
            @endcan
        </div>

                <table class="table table-striped">
                    <tr>
                        <th style="width: 200px">@lang('need_requests.attributes.project_id')</th>
                        <td>{{ $need_request->project->name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('need_requests.attributes.supervisor_id')</th>
                        <td>{{ $need_request->supervisor->name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('need_requests.attributes.status')</th>
                        <td>
                            <span class="badge badge-{{ $need_request->isApproved() ? 'success' : ($need_request->isRejected() ? 'danger' : 'warning') }}">
                                {{ trans('need_requests.statuses.'.$need_request->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('need_requests.attributes.created_at')</th>
                        <td>{{ $need_request->created_at->toDateTimeString() }}</td>
                    </tr>
                    @if($need_request->reviewed_at)
                    <tr>
                        <th>@lang('need_requests.attributes.reviewed_at')</th>
                        <td>{{ $need_request->reviewed_at->toDateTimeString() }}</td>
                    </tr>
                    <tr>
                        <th>@lang('need_requests.attributes.reviewed_by')</th>
                        <td>{{ $need_request->reviewedBy->name ?? '---' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>@lang('need_requests.attributes.notes')</th>
                        <td>{{ $need_request->notes ?? '---' }}</td>
                    </tr>
                </table>

                @slot('footer')
                    @include('dashboard.need_requests.partials.actions.edit')
                    @include('dashboard.need_requests.partials.actions.delete')
                @endslot
            @endcomponent

            @can('review', $need_request)
            @if($need_request->isPending())
            @component('dashboard::components.box')
                @slot('title', trans('need_requests.actions.review'))
                
                {{ BsForm::post(route('dashboard.need_requests.review', $need_request)) }}
                {{ BsForm::textarea('notes')->label(trans('need_requests.attributes.notes')) }}
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="submit" name="status" value="approved" class="btn btn-success">
                        <i class="fas fa-check"></i> @lang('need_requests.actions.approve')
                    </button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger">
                        <i class="fas fa-times"></i> @lang('need_requests.actions.reject')
                    </button>
                </div>
                {{ BsForm::close() }}
            @endcomponent
            @endif
            @endcan

            @if($need_request->isApproved())
            @can('nominate', $need_request)
            @component('dashboard::components.box')
                @slot('title', trans('need_requests.actions.nominate'))
                <p>سيتم ترشيح الأشخاص في هذا الطلب وإضافتهم كمستفيدين في مشروع: <b>{{ $need_request->project->name }}</b></p>
                
                <div class="row">
                    <div class="col-md-6">
                        {{ BsForm::post(route('dashboard.need_requests.nominate', $need_request)) }}
                        <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('هل أنت متأكد من ترشيح الجميع؟')">
                            <i class="fas fa-users"></i> ترشيح الكل
                        </button>
                        {{ BsForm::close() }}
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-primary btn-block" data-toggle="modal" data-target="#partial-nomination-modal">
                            <i class="fas fa-user-plus"></i> ترشيح مجموعة
                        </button>
                    </div>
                </div>
            @endcomponent

            {{-- Partial Nomination Modal --}}
            <div class="modal fade" id="partial-nomination-modal" tabindex="-1" role="dialog" aria-labelledby="partialNominationModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="partialNominationModalLabel">ترشيح مجموعة محددة</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        {{ BsForm::post(route('dashboard.need_requests.nominate', $need_request)) }}
                        <div class="modal-body">
                            <div class="alert alert-info">
                                أدخل أرقام الهويات (كل رقم في سطر) لترشيحهم فقط من ضمن الأشخاص المقبولين في هذا الطلب.
                            </div>
                            {{ BsForm::textarea('id_nums')->label('أرقام الهويات')->placeholder("900XXXXXX\n910XXXXXX")->rows(10) }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ وترشيح</button>
                        </div>
                        {{ BsForm::close() }}
                    </div>
                </div>
            </div>
            @endcan
            @endif
        </div>

        <div class="col-md-6">
            @component('dashboard::components.table-box')
                @slot('title', trans('need_requests.plural') . " (" . $need_request->items->count() . ")")

                <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الهوية</th>
                    <th>الاسم</th>
                    <th>الحالة</th>
                    <th>التحكم</th>
                </tr>
                </thead>
                <tbody>
                @forelse($need_request->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->person->id_num ?? '---' }}</td>
                    <td>{{ $item->person->name ?? '---' }}</td>
                    <td>
                        <span class="badge badge-{{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ trans('need_requests.statuses.'.$item->status) }}
                        </span>
                    </td>
                    <td>
                        @if($need_request->isApproved())
                            @can('nominate', $need_request)
                                {{ BsForm::post(route('dashboard.need_requests.items.nominate', [$need_request, $item])) }}
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="@lang('need_requests.actions.nominate')">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                {{ BsForm::close() }}
                            @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">لا توجد عناصر مضافة</td>
                </tr>
                @endforelse
                </tbody>
            @endcomponent
        </div>
    </div>
    @include('dashboard.need_requests.partials.skipped_modal')
</x-layout>
