@can('delete', $neighborhood)
    <a href="#neighborhood-{{ $neighborhood->id }}-delete-model"
       class="btn btn-outline-danger btn-sm"
       data-toggle="modal">
        <i class="fas fa fa-fw fa-trash"></i>
    </a>


    <!-- Modal -->
    <div class="modal fade" id="neighborhood-{{ $neighborhood->id }}-delete-model" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $neighborhood->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $neighborhood->id }}">@lang('neighborhoods.dialogs.delete.title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('neighborhoods.dialogs.delete.info')
                </div>
                <div class="modal-footer">
                    {{ BsForm::delete(route('dashboard.neighborhoods.destroy', $neighborhood)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('neighborhoods.dialogs.delete.cancel')
                    </button>
                    <button type="submit" class="btn btn-danger">
                        @lang('neighborhoods.dialogs.delete.confirm')
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
