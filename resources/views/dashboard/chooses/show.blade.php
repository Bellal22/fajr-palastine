<x-layout :title="trans('chooses.types.'.$type)" :breadcrumbs="['dashboard.chooses.index']">

    @component('dashboard::components.box')
        @slot('bodyClass', 'p-4')

        <div class="row">
            {{-- Type Icon Section --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="type-icon mb-3">
                    <i class="fas fa-cogs fa-5x text-primary"></i>
                </div>
                <h4 class="mb-1 font-weight-bold">@lang('chooses.types.'.$type)</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-tag"></i> @lang('chooses.attributes.type')
                </p>
            </div>

            {{-- Type Details Section --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-info-circle text-primary"></i>
                            @lang('chooses.sections.basic_info')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted font-weight-bold">
                                <i class="fas fa-columns"></i> @lang('chooses.attributes.type')
                            </div>
                            <div class="col-sm-8 font-weight-bold">
                                @lang('chooses.types.'.$type)
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-0">
                            <div class="col-sm-4 text-muted font-weight-bold">
                                <i class="fas fa-list-ol"></i> @lang('chooses.count')
                            </div>
                            <div class="col-sm-8 text-primary font-weight-bold">
                                {{ count_formatted($chooses->total()) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listing Table --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-list-ul mr-1"></i>
                            @lang('chooses.plural')
                            <span class="badge badge-dark badge-pill ml-2">{{ count_formatted($chooses->total()) }}</span>
                        </h5>
                        @include('dashboard.chooses.partials.actions.trashed')
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="100" class="py-2 px-4 border-bottom-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <x-check-all-delete
                                                        type="{{ \App\Models\Choose::class }}"
                                                        :resource="trans('chooses.plural')"></x-check-all-delete>

                                                <a href="{{ route('dashboard.chooses.create', ['type' => $type]) }}" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                                                    <i class="fas fa-plus"></i> @lang('chooses.actions.create')
                                                </a>
                                            </div>
                                        </th>
                                    </tr>
                                                    <tr class="text-dark font-weight-bold border-top shadow-none">
                                        <th style="width: 50px;" class="text-center border-top-0">
                                            <x-check-all></x-check-all>
                                        </th>
                                        <th style="width: 60px" class="text-center border-top-0">#</th>
                                        <th class="border-top-0"><i class="fas fa-info-circle text-info mr-1"></i> @lang('chooses.attributes.name')</th>
                                        <th class="text-center border-top-0" style="width: 80px;"><i class="fas fa-sort-numeric-down text-info mr-1"></i> @lang('chooses.attributes.order')</th>
                                        <th class="text-center border-top-0"><i class="fas fa-history text-info mr-1"></i> @lang('chooses.attributes.created_at')</th>
                                        <th style="width: 140px" class="text-center border-top-0"><i class="fas fa-tools text-info mr-1"></i> @lang('chooses.actions.options')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chooses as $choose)
                                        <tr class="align-middle border-bottom">
                                            <td class="text-center">
                                              <x-check-all-item :model="$choose"></x-check-all-item>
                                            </td>
                                            <td class="text-center text-muted font-weight-bold">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold text-dark h6 mb-0">{{ $choose->name }}</span>
                                                    <small class="text-muted mt-1">
                                                        <i class="fas fa-code-branch font-size-10"></i> {{ $choose->slug ?? '-' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="btn btn-xs btn-light border font-weight-bold rounded-pill px-3 shadow-sm">
                                                    {{ $choose->order }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    @if($choose->created_at)
                                                        <span class="text-dark small font-weight-bold">{{ $choose->created_at->translatedFormat('Y-m-d') }}</span>
                                                        <span class="text-muted font-size-12">{{ $choose->created_at->diffForHumans() }}</span>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group shadow-sm rounded">
                                                    @include('dashboard.chooses.partials.actions.edit')
                                                    @include('dashboard.chooses.partials.actions.delete')
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                                                    <h5>@lang('chooses.empty')</h5>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($chooses->hasPages())
                        <div class="card-footer bg-white p-3 border-top">
                            {{ $chooses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @slot('footer')
            <div class="d-flex">
                <a href="{{ route('dashboard.chooses.index') }}" class="btn btn-outline-primary px-4 shadow-sm font-weight-bold">
                    <i class="fas fa-arrow-right mr-1"></i> @lang('chooses.actions.list')
                </a>
            </div>
        @endslot
    @endcomponent
</x-layout>
