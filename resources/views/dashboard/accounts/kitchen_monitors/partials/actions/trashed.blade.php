@can('viewAnyTrash', \App\Models\KitchenMonitor::class)
    <a href="{{ route('dashboard.kitchen_monitors.trashed') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa fa-fw fa-trash"></i>
        الأرشيف
    </a>
@endcan
