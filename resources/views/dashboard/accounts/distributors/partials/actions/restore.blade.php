@can('restore', $distributor)
    <a href="#distributor-restore-modal-{{ $distributor->id }}"
       class="btn btn-outline-success btn-sm"
       data-toggle="modal">
        <i class="fas fa-undo"></i>
    </a>

    <!-- Modal -->
    <div class="modal fade" id="distributor-restore-modal-{{ $distributor->id }}" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-restore-{{ $distributor->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-restore-{{ $distributor->id }}">@lang('distributors.dialogs.restore.title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    {{ trans('distributors.dialogs.restore.info') }}
                </div>
                <div class="modal-footer">
                    {{ BsForm::post(route('dashboard.distributors.restore', $distributor)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ trans('distributors.dialogs.restore.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        {{ trans('distributors.dialogs.restore.confirm') }}
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
