@can('delete', $area_responsible)
    <a href="#area_responsible-{{ $area_responsible->id }}-delete-model"
       class="btn btn-outline-danger btn-sm"
       data-toggle="modal">
        <i class="fas fa fa-fw fa-trash"></i>
    </a>


    <!-- Modal -->
    <div class="modal fade" id="area_responsible-{{ $area_responsible->id }}-delete-model" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $area_responsible->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $area_responsible->id }}">@lang('area_responsibles.dialogs.delete.title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('area_responsibles.dialogs.delete.info')
                </div>
                <div class="modal-footer">
                    {{ BsForm::delete(route('dashboard.area_responsibles.destroy', $area_responsible)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('area_responsibles.dialogs.delete.cancel')
                    </button>
                    <button type="submit" class="btn btn-danger">
                        @lang('area_responsibles.dialogs.delete.confirm')
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
