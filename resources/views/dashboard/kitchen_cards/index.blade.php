<x-layout :title="trans('kitchen_cards.plural')" :breadcrumbs="['dashboard.kitchen_cards.index']">

    {{-- Import/Export Section --}}
    <div class="mb-4">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-file-excel text-primary"></i> استيراد وتصدير بطاقات المطبخ
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="row align-items-end">
                    <div class="col-md-6 border-left">
                        <form action="{{ route('dashboard.kitchen_cards.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-sm-7">
                                    <div class="form-group mb-0">
                                        <label class="text-muted small mb-1">اختر ملف الإكسل للاستيراد <small class="text-danger">(العمود 1: اسم المخيم، العمود 2: رقم البطاقة)</small></label>
                                        <input type="file" name="excel_file" class="form-control form-control-sm" required>
                                    </div>
                                </div>
                                <div class="col-sm-5 mt-2 mt-sm-0">
                                    <button type="submit" class="btn btn-primary btn-block btn-sm">
                                        <i class="fas fa-upload"></i> بدء الاستيراد
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 mt-3 mt-md-0 d-flex gap-2 align-items-center justify-content-end">
                        <a href="{{ route('dashboard.kitchen_cards.template') }}" class="btn btn-outline-success btn-sm mr-2">
                            <i class="fas fa-download"></i> تحميل نموذج الاستيراد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="mb-4">
        <form action="{{ route('dashboard.kitchen_cards.index') }}" method="GET">
            @component('dashboard::components.box')
                @slot('title')
                    <i class="fas fa-filter text-primary"></i> تصفية وفلترة بطاقات المطبخ
                @endslot

                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-campground"></i> اسم المخيم</label>
                        <input type="text" name="camp_name" class="form-control form-control-sm" placeholder="ابحث باسم المخيم..." value="{{ request('camp_name') }}">
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="font-weight-bold small"><i class="fas fa-id-card"></i> رقم البطاقة</label>
                        <input type="text" name="card_number" class="form-control form-control-sm" placeholder="ابحث برقم البطاقة..." value="{{ request('card_number') }}">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-search"></i> تصفية
                        </button>
                    </div>
                </div>
            @endcomponent
        </form>
    </div>

    @component('dashboard::components.table-box')
        @slot('title')
            <i class="fas fa-id-card text-primary"></i> @lang('kitchen_cards.actions.list')
            <span class="badge badge-primary badge-pill">{{ count_formatted($cards->total()) }}</span>
        @endslot

        <thead>
        <tr>
            <th colspan="100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div></div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <a href="{{ route('dashboard.kitchen_cards.export_zip') }}" class="btn btn-info btn-sm text-white shadow-sm">
                            <i class="fas fa-file-archive"></i> تصدير جماعي (إكسل + صور باركود)
                        </a>
                        <a href="{{ route('dashboard.kitchen_cards.create') }}" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-plus"></i> @lang('kitchen_cards.actions.create')
                        </a>
                    </div>
                </div>
            </th>
        </tr>

        {{-- Table Column Headers --}}
        <tr class="bg-light">
            <th><i class="fas fa-campground"></i> @lang('kitchen_cards.attributes.camp_name')</th>
            <th><i class="fas fa-id-card"></i> @lang('kitchen_cards.attributes.card_number')</th>
            <th><i class="fas fa-barcode"></i> @lang('kitchen_cards.attributes.barcode')</th>
            <th class="text-center" style="width: 320px"><i class="fas fa-cog"></i> الإجراءات</th>
        </tr>
        </thead>

        <tbody>
        @forelse($cards as $card)
            <tr class="align-middle">
                <td>{{ $card->camp_name }}</td>
                <td>{{ $card->card_number }}</td>
                <td><code>{{ $card->barcode }}</code></td>
                <td class="text-center">
                    <div class="d-inline-flex gap-1" style="gap: 5px;">
                        <a href="{{ route('dashboard.kitchen_cards.show', $card) }}" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-print"></i> الباركود
                        </a>
                        <a href="{{ route('dashboard.kitchen_cards.edit', $card) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <form action="{{ route('dashboard.kitchen_cards.destroy', $card) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه البطاقة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> حذف
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">لا يوجد بطاقات مطبخ مضافة بعد.</td>
            </tr>
        @endforelse
        </tbody>

        @slot('footer')
            {{ $cards->links() }}
        @endslot
    @endcomponent

</x-layout>
