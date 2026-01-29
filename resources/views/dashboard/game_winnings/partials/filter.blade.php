{{ BsForm::resource('game_winnings')->get(url()->current()) }}
@component('dashboard::components.box')
    @slot('title', trans('game_winnings.filter'))

    <div class="row">
        <div class="col-md-4">
            {{ BsForm::text('code')->value(request('code'))->label(trans('game_winnings.attributes.code')) }}
        </div>
        <div class="col-md-4">
            {{ BsForm::select('status')
                ->options([
                    '' => trans('game_winnings.all'),
                    'pending' => trans('game_winnings.statuses.pending'),
                    'redeemed' => trans('game_winnings.statuses.redeemed'),
                ])
                ->value(request('status'))
                ->label(trans('game_winnings.attributes.status'))
            }}
        </div>
        <div class="col-md-4">
            {{ BsForm::number('perPage')
                ->value(request('perPage', 15))
                ->min(1)
                 ->label(trans('game_winnings.perPage')) }}
        </div>
    </div>

    @slot('footer')
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa fa-fw fa-filter"></i>
            @lang('game_winnings.actions.filter')
        </button>
    @endslot
@endcomponent
{{ BsForm::close() }}
