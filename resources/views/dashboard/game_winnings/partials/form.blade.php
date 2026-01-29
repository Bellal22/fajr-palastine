@include('dashboard.errors')

<div class="row">
    <div class="col-md-6">
        {{ BsForm::text('code')->label(trans('game_winnings.attributes.code'))->required() }}
    </div>
    <div class="col-md-6">
        {{ BsForm::select('status')
            ->options([
                'pending' => trans('game_winnings.statuses.pending'),
                'redeemed' => trans('game_winnings.statuses.redeemed'),
            ])
            ->label(trans('game_winnings.attributes.status'))
            ->required()
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {{ BsForm::select('person_id')
            ->options($people)
            ->label(trans('game_winnings.attributes.person_id'))
            ->placeholder(trans('game_winnings.attributes.person_placeholder'))
        }}
    </div>
    <div class="col-md-6">
        {{ BsForm::select('coupon_type_id')
            ->options($couponTypes)
            ->label(trans('game_winnings.attributes.coupon_type_id'))
            ->placeholder(trans('game_winnings.attributes.coupon_type_placeholder'))
            ->required()
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {{ BsForm::date('delivered_at')->label(trans('game_winnings.attributes.delivered_at')) }}
    </div>
</div>

