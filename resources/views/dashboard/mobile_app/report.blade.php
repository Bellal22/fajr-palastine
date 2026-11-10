<x-layout :title="__('distributors.batches.title')" :breadcrumbs="['dashboard.mobile_app.report']">
    @component('dashboard::components.box')
        @slot('title', __('distributors.batches.title'))
        
        <form action="{{ route('dashboard.mobile_app.report') }}" method="GET" class="row align-items-end">
            <div class="col-md-10 mb-2">
                <label class="small fw-bold">{{ __('distributors.batches.project_select') }}</label>
                <select name="project_id" class="form-control select2" onchange="this.form.submit()">
                    <option value="">-- {{ __('distributors.batches.project_select') }} --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-info btn-block"><i class="fas fa-sync"></i> {{ __('distributors.batches.update') }}</button>
            </div>
        </form>
    @endcomponent

    @if($selectedProjectId)
        @component('dashboard::components.table-box')
            @slot('title')
                <i class="fas fa-history mr-2"></i> {{ __('distributors.batches.table_title') }}
                <span class="badge badge-primary badge-pill ml-2">{{ $groupedNominations->count() }}</span>
            @endslot

            <thead>
                <tr class="bg-light">
                    <th>{{ __('distributors.batches.date') }}</th>
                    <th>{{ __('distributors.batches.worker') }}</th>
                    <th>{{ __('distributors.batches.warehouse') }}</th>
                    <th class="text-center">{{ __('distributors.batches.nominations') }}</th>
                    <th class="text-center">{{ __('distributors.batches.captured_qty') }}</th>
                    <th>{{ __('distributors.batches.field_notes') }}</th>
                    <th class="text-end" style="width: 100px">{{ __('distributors.batches.actions') }}</th>
                </tr>
                </thead>

                <tbody>
                @forelse($groupedNominations as $key => $batch)
                    @php
                        $first = $batch->first();
                        $nominationDate = \Carbon\Carbon::parse($first->created_at);
                        $receivedCount = $batch->where('status', 'مستلم')->count();
                        $capturedQty = $batch->where('status', 'مستلم')->sum('captured_qty');
                        $externalNotes = $batch->whereNotNull('external_notes')->pluck('external_notes')->first();
                        $batchId = 'batch_' . $nominationDate->format('Y_m_d_Hi') . '_' . $first->user_id . '_' . $first->sub_warehouse_id;
                    @endphp
                    <tr class="align-middle">
                        <td>
                            <span class="text-dark fw-bold">{{ $nominationDate->format('Y-m-d') }}</span><br>
                            <small class="text-muted">{{ $nominationDate->format('H:i') }} | {{ $nominationDate->locale('ar')->dayName }}</small>
                        </td>
                        <td><i class="fas fa-user-circle text-muted mr-1"></i> {{ $first->worker_name }}</td>
                        <td><span class="badge badge-light border">{{ $first->warehouse_name ?? 'عام' }}</span></td>
                        <td class="text-center">
                            <div class="mb-1">
                                <span class="badge badge-info badge-pill" title="إجمالي العدد في هذه الرفعة">{{ $batch->count() }} مرسل</span>
                            </div>
                            @if($receivedCount > 0)
                                <div>
                                    <span class="badge badge-success badge-pill" title="العدد الذي تم تسليمه فعلياً">{{ $receivedCount }} مستلم</span>
                                </div>
                                @php $percent = round(($receivedCount / $batch->count()) * 100); @endphp
                                <div class="progress mt-2" style="height: 5px;" title="نسبة الإنجاز: {{ $percent }}%">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            @else
                                <span class="text-muted small">بانتظار الرفع من الجوال</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($capturedQty !== null && $capturedQty !== '')
                                <span class="badge badge-warning fw-bold">{{ $capturedQty }}</span>
                            @else
                                <span class="text-muted">---</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-muted small" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $externalNotes }}">
                                {{ $externalNotes ?: 'لا يوجد' }}
                            </div>
                        </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                    type="button"
                                    id="dropdownMenuButton{{ $batchId }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenuButton{{ $batchId }}">
                                
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_{{ $batchId }}">
                                    <i class="fas fa-cog text-info"></i>
                                    <span class="mr-2">{{ __('distributors.batches.view_edit') }}</span>
                                </a>

                                <a class="dropdown-item" href="{{ route('dashboard.mobile_app.nominate', ['project_id' => $selectedProjectId, 'user_id' => $first->user_id]) }}">
                                    <i class="fas fa-users text-primary"></i>
                                    <span class="mr-2">{{ __('distributors.batches.view_names') }}</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <form action="{{ route('dashboard.mobile_app.report.delete_batch') }}" method="POST" onsubmit="return confirm('{{ __('distributors.batches.delete_confirm') }}')">
                                    @csrf
                                    <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                                    <input type="hidden" name="user_id" value="{{ $first->user_id }}">
                                    <input type="hidden" name="created_at" value="{{ $first->created_at }}">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-trash text-danger"></i>
                                        <span class="mr-2 text-danger">{{ __('distributors.batches.delete') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="modal_{{ $batchId }}" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content text-right">
                                    <form action="{{ route('dashboard.mobile_app.report.update_batch') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                                        <input type="hidden" name="user_id" value="{{ $first->user_id }}">
                                        <input type="hidden" name="created_at" value="{{ $first->created_at }}">
                                        
                                        <div class="modal-header d-flex align-items-center">
                                            <h5 class="modal-title font-weight-bold">
                                                <i class="fas fa-tasks text-primary ml-2"></i>
                                                {{ __('distributors.batches.modal_title', ['worker' => $first->worker_name]) }}
                                            </h5>
                                            <button type="button" class="close ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-right">
                                            <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
                                                <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                                                <div class="alert-text">
                                                    <span class="ml-4"><strong>{{ __('distributors.batches.date') }}:</strong> {{ $nominationDate->format('Y-m-d H:i') }}</span>
                                                    <span><strong>{{ __('distributors.batches.warehouse') }}:</strong> {{ $first->warehouse_name ?? 'عام' }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group mb-4" style="text-align: right !important; direction: rtl !important;">
                                                <label class="font-weight-bold mb-2 d-block" style="text-align: right !important; direction: rtl !important;">{{ __('distributors.batches.captured_qty') }}</label>
                                                <div class="input-group" style="direction: ltr !important;">
                                                    <input type="number" name="captured_qty" class="form-control text-right" value="{{ $capturedQty }}" placeholder="{{ __('distributors.batches.captured_qty') }}" style="direction: rtl !important; text-align: right !important;">
                                                    <div class="input-group-append"><span class="input-group-text"><i class="fas fa-box-open"></i></span></div>
                                                </div>
                                                <small class="form-text text-muted d-block" style="text-align: right !important; direction: rtl !important;">{{ __('distributors.batches.captured_qty_hint') }}</small>
                                            </div>

                                            <div class="form-group mb-0" style="text-align: right !important; direction: rtl !important;">
                                                <label class="font-weight-bold mb-2 d-block" style="text-align: right !important; direction: rtl !important;">{{ __('distributors.batches.field_notes') }}</label>
                                                <textarea name="external_notes" class="form-control text-right" rows="8" placeholder="{{ __('distributors.batches.field_notes') }}" style="direction: rtl !important; text-align: right !important;">{{ $externalNotes }}</textarea>
                                                <small class="form-text text-muted d-block" style="text-align: right !important; direction: rtl !important;">{{ __('distributors.batches.field_notes_hint') }}</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-end">
                                            <button type="submit" class="btn btn-primary px-5">{{ __('distributors.batches.save_changes') }}</button>
                                            <button type="button" class="btn btn-light-secondary font-weight-bold" data-dismiss="modal">{{ __('distributors.batches.close') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <span class="text-muted">{{ __('distributors.batches.empty_state') }}</span>
                    </td>
                </tr>
            @endforelse
            </tbody>
        @endcomponent
    @endif
</x-layout>
