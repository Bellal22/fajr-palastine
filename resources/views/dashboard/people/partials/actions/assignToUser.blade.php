@if (auth()->user()?->isAdmin())
    @can('update', $person)
        <a href="#person-{{ $person->id }}-assign-to-user-model"
           class="btn btn-outline-info btn-sm"
           data-toggle="modal">
            تعيين مسؤولين
        </a>

        <!-- Modal -->
        <div class="modal fade" id="person-{{ $person->id }}-assign-to-user-model" tabindex="-1" role="dialog"
             aria-labelledby="modal-title-{{ $person->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title-{{ $person->id }}">@lang('قم بالتعيين')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label>@lang('المشرف')</label>
                        <select id="supervisor-select-{{ $person->id }}" name="supervisor_id" class="form-control">
                            <option value="">@lang('اختر مشرف')</option>
                            @foreach(\App\Models\Supervisor::all() as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>

                        <div id="user-wrapper-{{ $person->id }}" style="display:none;" class="mt-3">
                            <label>@lang('المندوب')</label>
                            <select id="user-select-{{ $person->id }}" name="user_id" class="form-control">
                                <option value="">@lang('اختر المندوب')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{ BsForm::put(route('dashboard.people.assignToSupervisor', $person)) }}

                        <input type="hidden" name="supervisor_id" id="hidden-supervisor-{{ $person->id }}">
                        <input type="hidden" name="user_id" id="hidden-user-{{ $person->id }}">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('الغاء')</button>
                        <button type="submit" class="btn btn-info" disabled id="submit-btn-{{ $person->id }}">@lang('تم')</button>
                        {{ BsForm::close() }}
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let supervisorSelect = document.getElementById("supervisor-select-{{ $person->id }}");
                    let userWrapper = document.getElementById("user-wrapper-{{ $person->id }}");
                    let userSelect = document.getElementById("user-select-{{ $person->id }}");
                    let hiddenSupervisor = document.getElementById("hidden-supervisor-{{ $person->id }}");
                    let hiddenUser = document.getElementById("hidden-user-{{ $person->id }}");
                    let submitBtn = document.getElementById("submit-btn-{{ $person->id }}");

                    supervisorSelect.addEventListener("change", function () {
                        hiddenSupervisor.value = this.value;
                        hiddenUser.value = "";
                        userSelect.innerHTML = '<option value="">@lang("اختر المندوب")</option>';
                        if (this.value) {
                            userWrapper.style.display = "block";
                            {{--axios.get("{{ route('dashboard.people.assignToSupervisor') }}", {--}}
                            axios.get("#", {
                                params: {supervisor_id: this.value}
                            }).then(res => {
                                res.data.forEach(user => {
                                    let opt = document.createElement("option");
                                    opt.value = user.id;
                                    opt.text = user.name;
                                    userSelect.appendChild(opt);
                                });
                            });
                        } else {
                            userWrapper.style.display = "none";
                        }
                        checkSubmit();
                    });

                    userSelect.addEventListener("change", function () {
                        hiddenUser.value = this.value;
                        checkSubmit();
                    });

                    function checkSubmit() {
                        submitBtn.disabled = !(hiddenSupervisor.value && hiddenUser.value);
                    }
                });
            </script>
        @endpush
    @endcan
@endif
