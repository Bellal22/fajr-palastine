@props([
    'can' => null,
    'url' => '#',
    'name' => '',
    'active' => false,
    'icon' => 'fas fa-circle',
    'tree' => []
])

@can($can)
<li class="nav-item {{ $active ? 'menu-open' : '' }}">
    <a href="{{ $url }}" class="nav-link {{ $active ? 'active' : '' }}">
        <i class="{{ $icon }}"></i>
        <p>
            {{ $name }}
            @if(!empty($tree))
                <i class="right fas fa-angle-left"></i>
            @endif
        </p>
    </a>
    @if(!empty($tree))
    <ul class="nav nav-treeview">
        @foreach($tree as $item)
            @if(isset($item['can']) ? auth()->user()->can($item['can']) : true)
            <li class="nav-item">
                <a href="{{ $item['url'] }}" class="nav-link {{ $item['active'] ?? false ? 'active' : '' }}">
                    @if(isset($item['icon']))
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                    @endif
                    <p>{{ $item['name'] }}</p>
                </a>
            </li>
            @endif
        @endforeach
    </ul>
    @endif
</li>
@endcan
