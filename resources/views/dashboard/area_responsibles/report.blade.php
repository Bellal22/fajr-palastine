<x-layout :title="trans('area_responsibles.plural')" :breadcrumbs="['dashboard.area_responsibles.index']">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">@lang('area_responsibles.actions.report')</h5>
            <div>
                <a href="{{ route('dashboard.area_responsibles.report.excel') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
                <a href="{{ route('dashboard.area_responsibles.report.pdf') }}" class="btn btn-danger btn-sm ml-2">
                    <i class="fas fa-file-pdf"></i> تصدير PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center">#</th>
                            <th>@lang('area_responsibles.attributes.name') (المسؤول)</th>
                            <th>اسم المندوب (البلوك)</th>
                            <th class="text-center">عدد الأسر</th>
                            <th class="text-center">عدد الأفراد الكلي</th>
                            <th class="text-center">الموقع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $grandFamilies = 0;
                            $grandIndividuals = 0;
                            $counter = 1;
                        @endphp
                        @foreach($areaResponsibles as $areaResponsible)
                            @php 
                                $blocksCount = $areaResponsible->blocks->count();
                                $firstBlock = $areaResponsible->blocks->first();
                            @endphp
                            
                            @if($blocksCount > 0)
                                @foreach($areaResponsible->blocks as $index => $block)
                                    @php 
                                        $grandFamilies += $block->families_count;
                                        $grandIndividuals += $block->individuals_count;
                                    @endphp
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ $blocksCount }}" class="text-center align-middle font-weight-bold">{{ $counter++ }}</td>
                                            <td rowspan="{{ $blocksCount }}" class="align-middle">
                                                <div class="font-weight-bold text-primary">{{ $areaResponsible->name }}</div>
                                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $areaResponsible->address ?? '-' }}</small>
                                            </td>
                                        @endif
                                        <td class="align-middle">{{ $block->name }}</td>
                                        <td class="text-center align-middle font-weight-bold">{{ number_format($block->families_count) }}</td>
                                        <td class="text-center align-middle font-weight-bold text-info">{{ number_format($block->individuals_count) }}</td>
                                        <td class="text-center align-middle">
                                            @if($block->lat && $block->lan)
                                                <a href="https://www.google.com/maps?q={{ $block->lat }},{{ $block->lan }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="عرض الموقع">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Case where responsible has no blocks --}}
                                <tr>
                                    <td class="text-center align-middle">{{ $counter++ }}</td>
                                    <td class="align-middle font-weight-bold">{{ $areaResponsible->name }}</td>
                                    <td colspan="4" class="text-center text-muted italic">لا يوجد مناديب مرتبطين</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="3" class="text-right py-3">المجموع الكلي</td>
                            <td class="text-center py-3">{{ number_format($grandFamilies) }}</td>
                            <td class="text-center py-3 text-primary" style="font-size: 1.1rem;">{{ number_format($grandIndividuals) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-layout>
