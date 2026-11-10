@can('restore', $kitchenMonitor)
    <a href="#kitchen-monitor-{{ $kitchenMonitor->id }}-restore-model"
       class="btn btn-outline-primary btn-sm"
       data-toggle="modal">
        <i class="fas fa fa-fw fa-trash-restore"></i>
    </a>

    <!-- Modal -->
    <div class="modal fade" id="kitchen-monitor-{{ $kitchenMonitor->id }}-restore-model" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $kitchenMonitor->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $kitchenMonitor->id }}">استعادة الحساب</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من رغبتك في استعادة حساب متابع المطبخ هذا؟
                </div>
                <div class="modal-footer">
                    {{ BsForm::post(route('dashboard.kitchen_monitors.restore', $kitchenMonitor)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        استعادة
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
