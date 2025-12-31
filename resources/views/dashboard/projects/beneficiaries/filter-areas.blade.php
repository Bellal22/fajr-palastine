<x-layout :title="'ترشيح المستفيدين حسب المناطق - ' . $project->name" :breadcrumbs="['dashboard.projects.beneficiaries.filter-areas', $project]">

    {{ BsForm::post(route('dashboard.projects.beneficiaries.add-by-areas', $project)) }}

    @component('dashboard::components.box')
        @slot('title', 'ترشيح المستفيدين حسب المناطق')

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="area_responsible_id">مسؤول المنطقة <span class="text-danger">*</span></label>
                <select name="area_responsible_id"
                        id="area_responsible_id"
                        class="form-control"
                        required>
                    <option value="">اختر مسؤول المنطقة</option>
                    @foreach($areaResponsibles as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="block_id">المربع <span class="text-danger">*</span></label>
                <select name="block_id"
                        id="block_id"
                        class="form-control"
                        required
                        disabled>
                    <option value="">اختر المربع</option>
                </select>
                <small class="form-text text-muted">يجب اختيار مسؤول المنطقة أولاً</small>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                {{ BsForm::number('quantity')
                    ->label('الكمية لكل مستفيد')
                    ->value(1)
                    ->min(1)
                    ->required() }}
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

</x-layout>

<script>
// استخدم JavaScript عادي بدون jQuery
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Page loaded');

    const areaSelect = document.getElementById('area_responsible_id');
    const blockSelect = document.getElementById('block_id');

    console.log('Area select found:', areaSelect !== null);
    console.log('Block select found:', blockSelect !== null);

    if (!areaSelect || !blockSelect) {
        console.error('❌ Select elements not found!');
        return;
    }

    areaSelect.addEventListener('change', function() {
        const responsibleId = this.value;
        console.log('🔄 Area changed, ID:', responsibleId);

        if (!responsibleId) {
            blockSelect.innerHTML = '<option value="">اختر المربع</option>';
            blockSelect.disabled = true;
            return;
        }

        blockSelect.innerHTML = '<option value="">جارِ التحميل...</option>';
        blockSelect.disabled = true;

        const url = "{{ route('dashboard.ajax.getBlocksByResponsible') }}?responsible_id=" + responsibleId;
        console.log('📡 Fetching from:', url);

        fetch(url)
            .then(response => {
                console.log('📥 Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('✅ Data received:', data);

                blockSelect.innerHTML = '<option value="">اختر المربع</option>';

                if (data.blocks && data.blocks.length > 0) {
                    console.log('📋 Blocks count:', data.blocks.length);

                    data.blocks.forEach(function(block) {
                        const option = document.createElement('option');
                        option.value = block.id;
                        option.textContent = block.name;
                        blockSelect.appendChild(option);
                        console.log('➕ Added block:', block.name);
                    });

                    blockSelect.disabled = false;
                    console.log('✅ Block select enabled');
                } else {
                    console.log('⚠️ No blocks found');
                    blockSelect.innerHTML = '<option value="">لا توجد مربعات</option>';
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                blockSelect.innerHTML = '<option value="">حدث خطأ</option>';
                alert('حدث خطأ في تحميل المربعات: ' + error.message);
            });
    });
});
</script>
