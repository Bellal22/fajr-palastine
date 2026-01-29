<x-layout :title="trans('game_winnings.plural')" :breadcrumbs="['dashboard.game_winnings.index']">
    @include('dashboard.game_winnings.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('game_winnings.actions.list') ({{ $game_winnings->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <div class="d-flex">
                <x-check-all-delete
                        type="{{ \App\Models\GameWinning::class }}"
                        :resource="trans('game_winnings.plural')"></x-check-all-delete>

                <div class="ml-2 d-flex justify-content-between flex-grow-1">
                    @include('dashboard.game_winnings.partials.actions.create')
                    @include('dashboard.game_winnings.partials.actions.trashed')
                </div>
            </div>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('game_winnings.attributes.code')</th>
            <th>@lang('game_winnings.attributes.person_id')</th>
            <th>@lang('game_winnings.attributes.coupon_type_id')</th>
            <th>@lang('game_winnings.attributes.status')</th>
            <th style="width: 150px;">@lang('game_winnings.attributes.created_at')</th>
            <th style="width: 160px">...</th>
        </tr>
        </thead>
        <tbody>
        @forelse($game_winnings as $game_winning)
            <tr>
                <td class="text-center">
                  <x-check-all-item :model="$game_winning"></x-check-all-item>
                </td>
                <td>
                    <a href="{{ route('dashboard.game_winnings.show', $game_winning) }}"
                       class="text-decoration-none text-ellipsis">
                        <strong>{{ $game_winning->code }}</strong>
                    </a>
                </td>
                <td>
                    @if($game_winning->person)
                        <a href="{{ route('dashboard.people.show', $game_winning->person) }}">
                            {{ $game_winning->person->name }}
                        </a>
                    @else
                        <span class="text-muted">@lang('game_winnings.statuses.system')</span>
                    @endif
                </td>
                <td>
                    {{ $game_winning->couponType->name ?? '---' }}
                </td>
                <td>
                    @if($game_winning->status == 'redeemed')
                        <span class="badge badge-success">@lang('game_winnings.statuses.redeemed')</span>
                    @else
                        <span class="badge badge-warning">@lang('game_winnings.statuses.pending')</span>
                    @endif
                </td>
                <td>
                    <span class="text-muted">
                        <i class="fas fa-calendar text-info"></i> {{ $game_winning->created_at->format('Y-m-d') }}
                    </span>
                </td>

                <td style="width: 160px">
                    @include('dashboard.game_winnings.partials.actions.show')
                    @include('dashboard.game_winnings.partials.actions.edit')
                    @include('dashboard.game_winnings.partials.actions.delete')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">@lang('game_winnings.empty')</td>
            </tr>
        @endforelse

        @if($game_winnings->hasPages())
            @slot('footer')
                {{ $game_winnings->links() }}
            @endslot
        @endif
    @endcomponent
</x-layout>
