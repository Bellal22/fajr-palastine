@can('delete', $ready_package)
    <a href="#ready_package-{{ $ready_package->id }}-delete-model"
       class="btn btn-outline-danger btn-sm"
       data-toggle="modal">
        <i class="fas fa fa-fw fa-trash"></i>
    </a>


    <!-- Modal -->
    <div class="modal fade" id="ready_package-{{ $ready_package->id }}-delete-model" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $ready_package->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $ready_package->id }}">@lang('ready_packages.dialogs.delete.title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('ready_packages.dialogs.delete.info')
                </div>
                <div class="modal-footer">
                    {{ BsForm::delete(route('dashboard.ready_packages.destroy', $ready_package)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('ready_packages.dialogs.delete.cancel')
                    </button>
                    <button type="submit" class="btn btn-danger">
                        @lang('ready_packages.dialogs.delete.confirm')
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
