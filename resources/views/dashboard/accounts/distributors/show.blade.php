<x-layout :title="$distributor->name" :breadcrumbs="['dashboard.distributors.show', $distributor]">
    @component('dashboard::components.box')
        @slot('bodyClass', 'p-4')

        <div class="row">
            {{-- Profile Header Section --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="profile-avatar mb-3 position-relative d-inline-block">
                    @if($distributor->getFirstMedia('avatars'))
                        <file-preview :media="{{ $distributor->getMediaResource('avatars') }}"></file-preview>
                    @else
                        <img src="{{ $distributor->getAvatar() }}"
                             class="rounded-circle img-thumbnail"
                             width="150"
                             height="150"
                             alt="{{ $distributor->name }}">
                    @endif
                </div>
                <h4 class="mb-1">{{ $distributor->name }}</h4>
                <p class="text-muted mb-2">
                    <i class="fas fa-truck text-success"></i> @lang('distributors.singular')
                </p>
                {{-- Status Badge --}}
                <div class="mb-0">
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> @lang('distributors.status.active')
                    </span>
                </div>
            </div>

            {{-- Profile Details Section --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary"></i>
                            @lang('distributors.profile_details')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-user"></i> @lang('distributors.attributes.name')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $distributor->name }}
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-envelope"></i> @lang('distributors.attributes.email')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                <a href="mailto:{{ $distributor->email }}" class="text-decoration-none">
                                    {{ $distributor->email }}
                                </a>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-phone"></i> @lang('distributors.attributes.phone')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                @if($distributor->phone)
                                    <a href="tel:{{ $distributor->phone }}" class="text-decoration-none">
                                        {{ $distributor->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-0">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-calendar-alt"></i> @lang('distributors.attributes.created_at')
                                </strong>
                            </div>
                            <div class="col-sm-8 text-muted">
                                {{ $distributor->created_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @slot('footer')
            <div class="d-flex gap-2 flex-wrap">
                @include('dashboard.accounts.distributors.partials.actions.edit')
                @include('dashboard.accounts.distributors.partials.actions.delete')
                @include('dashboard.accounts.distributors.partials.actions.restore')
                @include('dashboard.accounts.distributors.partials.actions.forceDelete')
            </div>
        @endslot
    @endcomponent
</x-layout>
