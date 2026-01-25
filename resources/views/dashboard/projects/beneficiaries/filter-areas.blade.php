<x-layout :title="'ترشيح المستفيدين حسب المناطق - ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries.filter-areas', $project]">

    {{ BsForm::post(route('dashboard.projects.beneficiaries.add-by-areas', $project)) }}

    @component('dashboard::components.box')
        @slot('title', 'ترشيح المستفيدين حسب المناطق')

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>ملاحظة:</strong> سيتم إضافة جميع الأشخاص الموجودين في المربع المحدد كمستفيدين في المشروع
        </div>

        <div class="row">
            <div class="col-md-12 form-group">
                <label for="id_nums">أرقام الهويات (اختياري)</label>
                <textarea name="id_nums" id="id_nums" class="form-control" rows="3" placeholder="أدخل أرقام الهويات هنا، كل رقم في سطر أو مفصولة بفواصل. عند إدخال الهويات سيتم تجاهل فلتر المنطقة والمربع."></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <hr>
                <h5>أو اختر حسب المنطقة والمربع</h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 form-group">
                <label for="area_responsible_id">مسؤول المنطقة</label>
                <select name="area_responsible_id"
                        id="area_responsible_id"
                        class="form-control @error('area_responsible_id') is-invalid @enderror">
                    <option value="">اختر مسؤول المنطقة</option>
                    @foreach($areaResponsibles as $area)
                        <option value="{{ $area->id }}" {{ old('area_responsible_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
                @error('area_responsible_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 form-group">
                <label for="block_id">المربع</label>
                <select name="block_id"
                        id="block_id"
                        class="form-control @error('block_id') is-invalid @enderror"
                        disabled>
                    <option value="">اختر المربع</option>
                </select>
                @error('block_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">يجب اختيار مسؤول المنطقة أولاً</small>
            </div>

            <div class="col-md-4 form-group">
                <label for="sub_warehouse_id">المخزن الفرعي <span class="text-danger">*</span></label>
                <select name="sub_warehouse_id"
                        id="sub_warehouse_id"
                        class="form-control @error('sub_warehouse_id') is-invalid @enderror"
                        required>
                    <option value="">-- اختر المخزن الفرعي --</option>
                    @foreach(\App\Models\SubWarehouse::all() as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('sub_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
                @error('sub_warehouse_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">المخزن الذي سيستلم منه المستفيدون</small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                {{ BsForm::number('quantity')
                    ->label('الكمية لكل مستفيد')
                    ->value(1)
                    ->min(1)
                    ->required() }}
            </div>
            <div class="col-md-4 form-group">
                <label for="ignore_conflicts">تجاوز التعارض المشروع <span class="text-danger">*</span></label>
                <select name="ignore_conflicts" id="ignore_conflicts" class="form-control">
                    <option value="0">لا (منع إضافة المستفيدين في حال وجود تعارض)</option>
                    <option value="1">نعم (إسقاط التعارض وإضافة المستفيدين)</option>
                </select>
                <small class="text-muted">إذا اخترت "نعم"، سيتم إضافة المستفيدين حتى لو كانوا مضافين في مشاريع متعارضة.</small>
            </div>
        </div>

        @slot('footer')
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                إضافة المستفيدين
            </button>
            <a href="{{ route('dashboard.projects.beneficiaries', $project) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        @endslot
    @endcomponent

    {{ BsForm::close() }}

    @push('scripts')
        <script>
            $(document).ready(function() {
                console.log('✅ الصفحة جاهزة');

                const areaSelect = $('#area_responsible_id');
                const blockSelect = $('#block_id');
                const idNumsTextarea = $('#id_nums');

                function updateFieldsState() {
                    const hasIdNums = idNumsTextarea.val().trim().length > 0;

                    if (hasIdNums) {
                        areaSelect.prop('disabled', true);
                        blockSelect.prop('disabled', true);
                    } else {
                        areaSelect.prop('disabled', false);
                        if (areaSelect.val()) {
                            blockSelect.prop('disabled', false);
                        } else {
                            blockSelect.prop('disabled', true);
                        }
                    }
                }

                idNumsTextarea.on('input', function() {
                    updateFieldsState();
                });

                updateFieldsState();

                areaSelect.on('change', function() {
                    const responsibleId = $(this).val();
                    console.log('🔄 تم تغيير المنطقة، ID:', responsibleId);

                    if (!responsibleId) {
                        blockSelect.html('<option value="">اختر المربع</option>');
                        blockSelect.prop('disabled', true);
                        return;
                    }

                    if (idNumsTextarea.val().trim().length > 0) {
                        console.log('⚠️ يوجد هويات في textarea، لن يتم جلب المربعات');
                        return;
                    }

                    blockSelect.html('<option value="">جارِ التحميل...</option>');
                    blockSelect.prop('disabled', true);

                    $.ajax({
                        url: "/dashboard/ajax/blocks-by-responsible", // ✅ المسار الصحيح
                        type: 'GET',
                        data: { responsible_id: responsibleId },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            console.log('✅ تم استلام البيانات:', response);

                            blockSelect.html('<option value="">اختر المربع</option>');

                            if (response.blocks && response.blocks.length > 0) {
                                $.each(response.blocks, function(index, block) {
                                    blockSelect.append(`<option value="${block.id}">${block.name}</option>`);
                                });

                                blockSelect.prop('disabled', false);
                                console.log('✅ تم إضافة ' + response.blocks.length + ' مربع وتفعيل السيليكت');

                                if ($.fn.select2 && blockSelect.hasClass('select2-hidden-accessible')) {
                                    blockSelect.select2('destroy').select2();
                                }
                            } else {
                                blockSelect.html('<option value="">لا توجد مربعات لهذه المنطقة</option>');
                                blockSelect.prop('disabled', true);
                                console.log('⚠️ لا توجد مربعات متاحة');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ خطأ في جلب المربعات:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                error: error
                            });

                            blockSelect.html('<option value="">حدث خطأ في التحميل</option>');
                            blockSelect.prop('disabled', true);

                            alert('حدث خطأ في تحميل المربعات. الرجاء المحاولة مرة أخرى.');
                        }
                    });
                });

                if (areaSelect.val() && idNumsTextarea.val().trim().length === 0) {
                    console.log('🚀 تحميل المربعات للمنطقة المختارة مسبقاً...');
                    areaSelect.trigger('change');
                }
            });
        </script>
    @endpush

</x-layout>




