<x-layout :title="'طرد داخلي: ' . $internal_package->name" :breadcrumbs="['dashboard.internal_packages.show', $internal_package]">
    <div class="row">
        <div class="col-md-8">
            @component('dashboard::components.box')
                @slot('title', 'معلومات الطرد الداخلي')
                @slot('class', 'p-0')
                @slot('bodyClass', 'p-0')

                <table class="table table-striped table-middle mb-0">
                    <tbody>
                    <tr>
                        <th width="200">@lang('internal_packages.attributes.name')</th>
                        <td><strong>{{ $internal_package->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>@lang('internal_packages.attributes.description')</th>
                        <td>{{ $internal_package->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('internal_packages.attributes.created_by')</th>
                        <td>
                            @if($internal_package->creator)
                                <span class="badge badge-secondary">{{ $internal_package->creator->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('internal_packages.attributes.quantity')</th>
                        <td><span class="badge badge-primary">{{ $internal_package->quantity }}</span></td>
                    </tr>
                    <tr>
                        <th>@lang('internal_packages.attributes.weight')</th>
                        <td>{{ $internal_package->weight ? number_format($internal_package->weight, 2) . ' كجم' : '-' }}</td>
                    </tr>
                    <tr>
                        <th>الوزن الإجمالي</th>
                        <td>
                            <strong>{{ number_format($internal_package->quantity * ($internal_package->weight ?? 0), 2) }} كجم</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('internal_packages.attributes.created_at')</th>
                        <td>{{ $internal_package->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                    </tbody>
                </table>

                @slot('footer')
                    @include('dashboard.internal_packages.partials.actions.edit')
                    @include('dashboard.internal_packages.partials.actions.delete')
                    @include('dashboard.internal_packages.partials.actions.restore')
                    @include('dashboard.internal_packages.partials.actions.forceDelete')
                @endslot
            @endcomponent
        </div>
    </div>

    @if($internal_package->contents && $internal_package->contents->count() > 0)
    <!-- محتويات الطرد الداخلي -->
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('title', 'محتويات الطرد')

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">النوع</th>
                                <th style="width: 30%;">اسم الصنف</th>
                                <th style="width: 10%;">الكمية</th>
                                <th style="width: 12%;">الوزن (كجم)</th>
                                <th style="width: 13%;">الوزن الإجمالي</th>
                                <th style="width: 10%;">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQuantity = 0;
                                $totalWeight = 0;
                            @endphp
                            @foreach($internal_package->contents as $index => $content)
                                @php
                                    $itemWeight = $content->item->weight ?? 0;
                                    $itemTotalWeight = $content->quantity * $itemWeight;
                                    $totalQuantity += $content->quantity;
                                    $totalWeight += $itemTotalWeight;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            <i class="fas fa-box"></i> صنف
                                        </span>
                                    </td>
                                    <td><strong>{{ $content->item->name ?? '-' }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $content->quantity }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($itemWeight, 2) }}</td>
                                    <td class="text-center">
                                        <strong>{{ number_format($itemTotalWeight, 2) }}</strong>
                                    </td>
                                    <td class="text-center">{{ $content->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="3" class="text-right">الإجمالي</th>
                                <th class="text-center">{{ $totalQuantity }}</th>
                                <th></th>
                                <th class="text-center"><strong class="text-success">{{ number_format($totalWeight, 2) }} كجم</strong></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            @component('dashboard::components.box')
                @slot('title', 'محتويات الطرد')

                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> لا توجد محتويات في هذا الطرد الداخلي
                </div>
            @endcomponent
        </div>
    </div>
    @endif
</x-layout>
