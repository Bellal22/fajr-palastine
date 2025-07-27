@can('assignToCurrentSupervisor', $person)
    <a href="#person-{{ $person->id }}-assign-model"
       class="btn btn-outline-info btn-sm m-1"
       data-toggle="modal">
        ربط الفرد <i class="fas fa fa-fw fa-location-arrow"></i>
    </a>


    <!-- Modal -->
    <div class="modal fade" id="person-{{ $person->id }}-assign-model" tabindex="-1" role="dialog"
         aria-labelledby="modal-title-{{ $person->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title-{{ $person->id }}">تعيين الفرد</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    هل تريد ربط الفرد بك
                </div>
                <div class="modal-footer">
                    {{ BsForm::put(route('dashboard.people.assignToSupervisor', $person)) }}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        @lang('people.dialogs.delete.cancel')
                    </button>
                    <button type="submit" class="btn btn-info">
                        تأكيد
                    </button>
                    {{ BsForm::close() }}
                </div>
            </div>
        </div>
    </div>
@endcan
