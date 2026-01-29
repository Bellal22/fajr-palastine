<x-layout :title="$game_winning->name" :breadcrumbs="['dashboard.game_winnings.show', $game_winning]">
    <div class="row">
        <div class="col-md-6">
            @component('dashboard::components.box')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle">
                    <tbody>
                    <tr>
                        <th width="200">@lang('game_winnings.attributes.code')</th>
                        <td>{{ $game_winning->code }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('game_winnings.attributes.person_id')</th>
                        <td>
                             @if($game_winning->person)
                                <a href="{{ route('dashboard.people.show', $game_winning->person) }}">
                                    {{ $game_winning->person->name }}
                                </a>
                            @else
                                <span class="text-muted">@lang('game_winnings.statuses.system')</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th width="200">@lang('game_winnings.attributes.coupon_type_id')</th>
                        <td>{{ $game_winning->couponType->name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('game_winnings.attributes.status')</th>
                        <td>
                            @if($game_winning->status == 'redeemed')
                                <span class="badge badge-success">@lang('game_winnings.statuses.redeemed')</span>
                            @else
                                <span class="badge badge-warning">@lang('game_winnings.statuses.pending')</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th width="200">@lang('game_winnings.attributes.delivered_at')</th>
                        <td>{{ $game_winning->delivered_at ? $game_winning->delivered_at->format('Y-m-d H:i') : '---' }}</td>
                    </tr>
                    <tr>
                        <th width="200">@lang('game_winnings.attributes.created_at')</th>
                        <td>{{ $game_winning->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.game_winnings.partials.actions.edit')
                    @include('dashboard.game_winnings.partials.actions.delete')
                    @include('dashboard.game_winnings.partials.actions.restore')
                    @include('dashboard.game_winnings.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>
</x-layout>
