@include('dashboard.errors')

<div class="row">
    <div class="col-md-6">
        {{ BsForm::text('name')
            ->label(trans('sub_warehouses.attributes.name'))
            ->required()
        }}
    </div>
    <div class="col-md-6">
        {{ BsForm::text('contact_person')
            ->label(trans('sub_warehouses.attributes.contact_person'))
            ->placeholder('اسم المسؤول')
        }}
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {{ BsForm::text('phone')
            ->label(trans('sub_warehouses.attributes.phone'))
            ->placeholder('رقم الهاتف')
        }}
    </div>
    <div class="col-md-6">
        <!-- فارغ للتوازن -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        {{ BsForm::textarea('address')
            ->label(trans('sub_warehouses.attributes.address'))
            ->rows(3)
            ->placeholder('العنوان الكامل')
        }}
    </div>
</div>
