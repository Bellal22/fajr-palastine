<x-layout :title="$admin->name" :breadcrumbs="['dashboard.admins.show', $admin]">
    @component('dashboard::components.box')
        @slot('bodyClass', 'p-4')

        <div class="row">
            {{-- Profile Header Section --}}
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="profile-avatar mb-3">
                    @if($admin->getFirstMedia('avatars'))
                        <file-preview :media="{{ $admin->getMediaResource('avatars') }}"></file-preview>
                    @else
                        <img src="{{ $admin->getAvatar() }}"
                             class="rounded-circle img-thumbnail"
                             width="150"
                             height="150"
                             alt="{{ $admin->name }}">
                    @endif
                </div>
                <h4 class="mb-1">{{ $admin->name }}</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-user-tie"></i> @lang('admins.singular')
                </p>
            </div>

            {{-- Profile Details Section --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary"></i>
                            @lang('admins.profile_details')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-user"></i> @lang('admins.attributes.name')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $admin->name }}
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-envelope"></i> @lang('admins.attributes.email')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                <a href="mailto:{{ $admin->email }}" class="text-decoration-none">
                                    {{ $admin->email }}
                                </a>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="row mb-0">
                            <div class="col-sm-4">
                                <strong class="text-muted">
                                    <i class="fas fa-phone"></i> @lang('admins.attributes.phone')
                                </strong>
                            </div>
                            <div class="col-sm-8">
                                <a href="tel:{{ $admin->phone }}" class="text-decoration-none">
                                    {{ $admin->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @slot('footer')
            <div class="d-flex gap-2 flex-wrap">
                @include('dashboard.accounts.admins.partials.actions.edit')
                @include('dashboard.accounts.admins.partials.actions.delete')
                @include('dashboard.accounts.admins.partials.actions.restore')
                @include('dashboard.accounts.admins.partials.actions.forceDelete')
            </div>
        @endslot
    @endcomponent
</x-layout>
