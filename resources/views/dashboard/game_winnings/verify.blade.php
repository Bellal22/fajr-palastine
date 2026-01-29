<x-layout :title="trans('game_winnings.actions.verify')" :breadcrumbs="['dashboard.game_winnings.verify']">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            {{-- Search Card --}}
            <div class="tile shadow-sm border-top border-primary mb-4">
                <div class="tile-body">
                    <div class="text-center mb-4">
                        <div class="display-4 text-primary mb-2">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <h4 class="font-weight-bold">{{ trans('game_winnings.actions.verify') }}</h4>
                        <p class="text-muted">أدخل كود الاستلام المكون من 10 أرقام للتحقق من الجائزة وصاحبها.</p>
                    </div>

                    {{ BsForm::post(route('dashboard.game_winnings.verify.post')) }}
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <div class="input-group input-group-lg shadow-sm rounded overflow-hidden border">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 text-primary">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                    </div>
                                    {{ BsForm::text('code')->value(request('code'))->placeholder('FAJR-XXXXXXXXXX')->required()->attribute('class', 'form-control border-0 text-center font-weight-bold') }}
                                    <div class="input-group-append">
                                        <button class="btn btn-primary px-4" type="submit">
                                            <i class="fas fa-search"></i> التحقق
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{ BsForm::close() }}

                    @if(!isset($winning) && request()->isMethod('post'))
                        <div class="mt-4 text-center">
                            <div class="alert alert-custom bg-danger-light text-danger border-danger py-3 px-4 d-inline-block shadow-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i> @lang('game_winnings.messages.not_found')
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Results Section --}}
            @if(isset($winning))
                <div class="tile shadow border-0 overflow-hidden p-0 mb-5">
                    {{-- Status Header --}}
                    <div class="bg-{{ $winning->status == 'redeemed' ? 'warning' : 'success' }} p-3 text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-info-circle mr-2"></i> @lang('game_winnings.verify.details')
                        </h5>
                        <div class="badge badge-light badge-pill px-3 py-2 text-{{ $winning->status == 'redeemed' ? 'warning' : 'success' }} font-weight-bold shadow-sm">
                            @if($winning->status == 'redeemed')
                                <i class="fas fa-hand-holding-heart mr-1"></i> @lang('game_winnings.statuses.redeemed')
                            @else
                                <i class="fas fa-check mr-1"></i> @lang('game_winnings.statuses.valid')
                            @endif
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row">
                            {{-- Prize Info --}}
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="p-3 bg-light rounded border-right border-{{ $winning->status == 'redeemed' ? 'warning' : 'success' }} border-width-3">
                                    <small class="text-muted d-block mb-1 uppercase tracking-wider">@lang('game_winnings.attributes.prize_name')</small>
                                    <h4 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-gift text-{{ $winning->status == 'redeemed' ? 'warning' : 'success' }} mr-2"></i>
                                        {{ $winning->couponType->name ?? '---' }}
                                    </h4>
                                </div>
                            </div>

                            {{-- Winner Info --}}
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border-right border-info border-width-3">
                                    <small class="text-muted d-block mb-1">@lang('game_winnings.attributes.person_id')</small>
                                    <h5 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-user text-info mr-2"></i>
                                        @if($winning->person)
                                            <a href="{{ route('dashboard.people.show', $winning->person) }}" target="_blank" class="text-dark">
                                                {{ $winning->person->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">@lang('game_winnings.statuses.system')</span>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-4 text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">@lang('game_winnings.attributes.code')</small>
                                <span class="h5 text-monospace font-weight-bold tracking-widest">{{ $winning->code }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">@lang('game_winnings.attributes.created_at')</small>
                                <span class="text-dark">{{ $winning->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>

                        @if($winning->delivered_at)
                            <div class="alert alert-warning border-0 bg-light-warning text-dark py-3 px-4 rounded d-flex align-items-center mb-4">
                                <i class="fas fa-calendar-check fa-2x opacity-50 mr-3"></i>
                                <div>
                                    <small class="d-block text-muted">@lang('game_winnings.attributes.delivered_at')</small>
                                    <strong class="h6 mb-0">{{ $winning->delivered_at->format('Y-m-d H:i') }}</strong>
                                </div>
                            </div>
                        @endif

                        {{-- Final Action --}}
                        @if($winning->status != 'redeemed')
                            <div class="mt-2">
                                {{ BsForm::post(route('dashboard.game_winnings.redeem', $winning)) }}
                                    <button type="submit" class="btn btn-success btn-lg btn-block py-3 shadow-sm font-weight-bold rounded-pill" onclick="return confirm('@lang('game_winnings.messages.confirm_redeem')')">
                                        <i class="fas fa-check-double mr-2"></i> @lang('game_winnings.verify.confirm_button')
                                    </button>
                                {{ BsForm::close() }}
                            </div>
                        @else
                            <div class="bg-warning-light text-warning-dark p-4 rounded-lg text-center border">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <h5 class="font-weight-bold mb-0">@lang('game_winnings.messages.already_redeemed')</h5>
                                <p class="mb-0 small">تم تسليم هذه الجائزة بالفعل مسبقاً.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="tile d-flex align-items-center justify-content-center border-0 bg-transparent text-center" style="min-height: 250px;">
                    <div>
                        <div class="mb-4 text-muted opacity-25">
                            <i class="fas fa-qrcode fa-5x"></i>
                        </div>
                        <h5 class="text-muted font-weight-normal">بانتظار مسح الكود أو إدخاله للتحقق...</h5>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
        .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
        .bg-warning-light { background-color: rgba(255, 193, 7, 0.05); }
        .text-warning-dark { color: #856404; }
        .border-width-3 { border-width: 3px !important; }
        .tracking-widest { letter-spacing: 0.15em; }
        .rounded-pill { border-radius: 50rem !important; }
        .rounded-lg { border-radius: 1rem !important; }
    </style>
</x-layout>
