{{-- resources/views/admin/inventory/import-list.blade.php --}}
@extends('admin.layouts.admin')
@section('title', 'Danh sách phiếu nhập kho')

@section('content')
<div class="admin-content">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                <i class="fa-solid fa-list text-primary me-2"></i>
                Phiếu nhập kho
            </h4>
            <p class="text-muted mb-0" style="font-size:0.875rem;margin-top:0.2rem;">
                Quản lý tất cả phiếu nhập hàng vào kho
            </p>
        </div>
        <a href="{{ route('admin.inventory.import.create') }}"
           class="btn btn-success fw-semibold">
            <i class="fa-solid fa-plus me-1"></i>Tạo phiếu nhập mới
        </a>
    </div>

    {{-- Stats nhanh --}}
    @php
        $allImports = \App\Models\InventoryImport::selectRaw('status, count(*) as c')
            ->groupBy('status')->pluck('c','status');
    @endphp
    <div class="row g-3 mb-6">
        @foreach([
            ['key'=>'draft',    'label'=>'Nháp',         'bg'=>'#fef3c7','color'=>'#92400e','icon'=>'fa-pen-to-square'],
            ['key'=>'confirmed','label'=>'Đã xác nhận',  'bg'=>'#dcfce7','color'=>'#166534','icon'=>'fa-circle-check'],
            ['key'=>'cancelled','label'=>'Đã hủy',       'bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'fa-circle-xmark'],
        ] as $s)
            <div class="col-md-4">
                <div class="bg-white rounded-lg shadow-sm p-4"
                     style="border-left:4px solid {{ $s['color'] }};">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-size:2rem;font-weight:800;
                                        color:{{ $s['color'] }};line-height:1;">
                                {{ $allImports[$s['key']] ?? 0 }}
                            </div>
                            <div class="text-muted" style="font-size:0.875rem;margin-top:0.25rem;">
                                {{ $s['label'] }}
                            </div>
                        </div>
                        <div style="width:44px;height:44px;border-radius:12px;
                                    background:{{ $s['bg'] }};
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid {{ $s['icon'] }}"
                               style="color:{{ $s['color'] }};font-size:1.1rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                        @foreach(['Mã phiếu','Người tạo','Số SP','Tổng giá trị',
                                   'Trạng thái','Ngày tạo',''] as $th)
                            <th style="padding:0.875rem 1rem;font-weight:600;
                                       color:#475569;font-size:0.8125rem;
                                       text-transform:uppercase;letter-spacing:0.04em;">
                                {{ $th }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($imports as $import)
                        @php
                            $importStatusStyle = [
                                'draft'     => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Nháp'],
                                'confirmed' => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Đã xác nhận'],
                                'cancelled' => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Đã hủy'],
                            ][$import->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','label'=>$import->status];
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:1rem;">
                                <span class="fw-bold text-primary">
                                    {{ $import->import_code }}
                                </span>
                            </td>
                            <td style="padding:1rem;">
                                <div style="font-size:0.9rem;color:#1e293b;font-weight:500;">
                                    {{ $import->createdBy->name ?? '—' }}
                                </div>
                            </td>
                            <td style="padding:1rem;color:#475569;font-size:0.875rem;">
                                {{ $import->details->count() }} sản phẩm
                            </td>
                            <td style="padding:1rem;font-weight:700;color:#205aa7;">
                                {{ number_format($import->total_value) }}₫
                            </td>
                            <td style="padding:1rem;">
                                <span style="padding:0.25rem 0.8rem;border-radius:9999px;
                                            font-size:0.8rem;font-weight:700;
                                            background:{{ $importStatusStyle['bg'] }};
                                            color:{{ $importStatusStyle['color'] }};">
                                    {{ $importStatusStyle['label'] }}
                                </span>
                            </td>
                            <td style="padding:1rem;color:#64748b;font-size:0.875rem;">
                                {{ $import->created_at->format('d/m/Y') }}<br>
                                <span style="font-size:0.75rem;">
                                    {{ $import->created_at->format('H:i') }}
                                </span>
                            </td>
                            <td style="padding:1rem;">
                                <a href="{{ route('admin.inventory.import.show', $import->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-8">
                                <i class="fa-solid fa-inbox"
                                   style="font-size:2.5rem;color:#cbd5e1;"></i>
                                <p class="text-muted mt-3 mb-0">Chưa có phiếu nhập nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $imports->links() }}</div>

</div>
@endsection