<x-layout :title="trans('game_winnings.trashed')" :breadcrumbs="['dashboard.game_winnings.trashed']">
    @include('dashboard.game_winnings.partials.filter')

    @component('dashboard::components.table-box')
        @slot('title')
            @lang('game_winnings.actions.list') ({{ $game_winnings->total() }})
        @endslot

        <thead>
        <tr>
          <th colspan="100">
            <x-check-all-force-delete
                    type="{{ \App\Models\GameWinning::class }}"
                    :resource="trans('game_winnings.plural')"></x-check-all-force-delete>
            <x-check-all-restore
                    type="{{ \App\Models\GameWinning::class }}"
                    :resource="trans('game_winnings.plural')"></x-check-all-restore>
          </th>
        </tr>
        <tr>
            <th style="width: 30px;" class="text-center">
              <x-check-all></x-check-all>
            </th>
            <th>@lang('game_winnings.attributes.name')</th>
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
                    <a href="{{ route('dashboard.game_winnings.trashed.show', $game_winning) }}"
                       class="text-decoration-none text-ellipsis">
                        {{ $game_winning->name }}
                    </a>
                </td>

                <td style="width: 160px">
                    @include('dashboard.game_winnings.partials.actions.show')
                    @include('dashboard.game_winnings.partials.actions.restore')
                    @include('dashboard.game_winnings.partials.actions.forceDelete')
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
