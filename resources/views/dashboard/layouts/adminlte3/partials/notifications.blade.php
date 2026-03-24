@php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $latestNotifications = auth()->user()->unreadNotifications()->take(5)->get();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
        <i class="far fa-bell"></i>
        @if($unreadCount > 0)
            <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left shadow-lg border-0" style="left: 0; right: auto;">
        <span class="dropdown-item dropdown-header font-weight-bold">
            @if($unreadCount > 0)
                {{ $unreadCount }} إشعارات جديدة
            @else
                لا يوجد إشعارات جديدة
            @endif
        </span>
        <div class="dropdown-divider"></div>
        
        <div class="notification-items-container" style="max-height: 300px; overflow-y: auto;">
            @forelse($latestNotifications as $notification)
                <a href="{{ route('dashboard.notifications.index') }}" class="dropdown-item py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning mr-3" style="min-width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <div class="notification-content" style="flex: 1; white-space: normal;">
                            <p class="text-sm mb-0">{{ $notification->data['message'] ?? 'تنبيه مخزون' }}</p>
                            <span class="text-muted text-xs"><i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
            @empty
                <div class="dropdown-item text-center py-4">
                    <span class="text-muted small">لا توجد تنبيهات حالياً</span>
                </div>
            @endforelse
        </div>
        
        <div class="dropdown-divider"></div>
        <a href="{{ route('dashboard.notifications.index') }}" class="dropdown-item dropdown-footer text-primary font-weight-bold">عرض الكل</a>
    </div>
</li>
