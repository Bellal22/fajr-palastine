<x-layout :title="$need_request->project->name ?? trans('need_requests.singular')" :breadcrumbs="['dashboard.need_requests.show', $need_request]">
    <div class="row">
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
