{{ BsForm::resource('need_requests')->get(url()->current()) }}

@component('dashboard::components.box')
    @slot('title')
        <i class="fas fa-filter"></i> @lang('need_requests.filter')
    @endslot

    <div class="row">
        {{-- ============== البحث الأساسي (المشروع) ============== --}}
        <div class="col-12 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-project-diagram"></i> @lang('need_requests.attributes.project_id')
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-weight-bold small">
                                <i class="fas fa-project-diagram"></i> @lang('need_requests.attributes.project_id')
                            </label>
                            @php
                                $projects = \App\Models\Project::query()
                                    ->whereHas('needRequestProject')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                                $projects = ['' => trans('need_requests.placeholders.select_project')] + $projects;
                            @endphp
                            
                            {{ BsForm::select('project_id', $projects)
                                ->value(request('project_id'))
                                ->label(false)
                                ->attribute('class', 'form-control select2') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============== فلاتر إضافية (المشرف والحالة) ============== --}}
        <div class="col-md-8 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-tasks"></i> خيارات التصنيف
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- المشرف (للمسؤولين فقط) --}}
                        @if(auth()->user()->isAdmin())
                            <div class="col-md-6 mb-2">
                                <label class="font-weight-bold small">
                                    <i class="fas fa-user-tie"></i> @lang('need_requests.attributes.supervisor_id')
                                </label>
                                @php
                                    $supervisors = \App\Models\Supervisor::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->toArray();
                                    $supervisors = ['' => '--- الكل ---'] + $supervisors;
                                @endphp
                                {{ BsForm::select('supervisor_id', $supervisors)
                                    ->value(request('supervisor_id'))
                                    ->label(false) }}
                            </div>
                        @endif

                        {{-- الحالة --}}
                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold small">
                                <i class="fas fa-info-circle"></i> @lang('need_requests.attributes.status')
                            </label>
                            @php
                                $statuses = [
                                    '' => '--- الكل ---',
                                    'pending' => trans('need_requests.statuses.pending'),
                                    'approved' => trans('need_requests.statuses.approved'),
                                    'rejected' => trans('need_requests.statuses.rejected'),
                                ];
                            @endphp
                            {{ BsForm::select('status', $statuses)
                                ->value(request('status'))
                                ->label(false) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============== إعدادات العرض ============== --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cog"></i> إعدادات العرض
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label class="font-weight-bold small">
                                <i class="fas fa-list-ol"></i> @lang('need_requests.perPage')
                            </label>
                            {{ BsForm::number('perPage')
                                ->value(request('perPage', 15))
                                ->min(1)
                                ->label(false) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('footer')
        <div class="d-flex justify-content-start">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5">
                <i class="fas fa-fw fa-search mr-1"></i>
                @lang('need_requests.actions.filter')
            </button>
            <a href="{{ route('dashboard.need_requests.index') }}" class="btn btn-secondary btn-lg ml-2 shadow-sm px-4">
                <i class="fas fa-eraser mr-1"></i>
                @lang('need_requests.actions.empty_filters')
            </a>
        </div>
    @endslot
@endcomponent

{{ BsForm::close() }}
