{{-- resources/views/admin/inventory/import-show.blade.php --}}
@extends('admin.layouts.admin')
@section('title', 'Chi tiết phiếu nhập')

@section('content')
<div class="admin-content">

    @php
        $importStatusStyle = [
            'draft'     => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Nháp'],
            'confirmed' => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Đã xác nhận'],
            'cancelled' => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Đã hủy'],
        ][$import->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','label'=>$import->status];
    @endphp

    {{-- Header --}}
    <div class="d-flex align-items-center mb-6" style="gap:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.inventory.import.list') }}"
           class="btn btn-sm btn-outline-secondary rounded-lg">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div style="flex:1;">
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                Phiếu nhập
                <span class="text-primary">{{ $import->import_code }}</span>
            </h4>
            <div class="text-muted" style="font-size:0.8125rem;margin-top:0.2rem;">
                Tạo bởi <strong>{{ $import->createdBy->name ?? '—' }}</strong>
                lúc {{ $import->created_at->format('H:i, d/m/Y') }}
            </div>
        </div>
        <span style="padding:0.35rem 1.1rem;border-radius:9999px;
                     font-size:0.875rem;font-weight:700;
                     background:{{ $importStatusStyle['bg'] }};
                     color:{{ $importStatusStyle['color'] }};">
            {{ $importStatusStyle['label'] }}
        </span>
    </div>

    <div class="row g-4">

        {{-- Danh sách sản phẩm --}}
        <div class="col-lg-8">
            <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
                <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold" style="color:#0f172a;">
                            <i class="fa-solid fa-box text-primary me-1"></i>
                            Danh sách sản phẩm nhập
                        </span>
                        <span class="text-muted" style="font-size:0.8125rem;">
                            {{ $import->details->count() }} sản phẩm
                        </span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:0.9rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                @foreach(['Sản phẩm','Số lượng','Đơn giá','Thành tiền'] as $th)
                                    <th style="padding:0.75rem 1rem;font-weight:600;
                                               color:#475569;font-size:0.8125rem;">
                                        {{ $th }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($import->details as $detail)
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:0.875rem 1rem;">
                                        <div class="fw-semibold" style="color:#0f172a;">
                                            {{ $detail->product->name ?? '(Đã xóa)' }}
                                        </div>
                                        @if($detail->product)
                                            <div class="text-muted" style="font-size:0.8rem;margin-top:0.15rem;">
                                                {{ $detail->product->category->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding:0.875rem 1rem;">
                                        <span style="display:inline-block;padding:0.2rem 0.75rem;
                                                    background:#dbeafe;color:#1e40af;
                                                    border-radius:9999px;font-weight:700;">
                                            {{ $detail->quantity }}
                                        </span>
                                    </td>
                                    <td style="padding:0.875rem 1rem;color:#475569;">
                                        {{ number_format($detail->unit_price) }}₫
                                    </td>
                                    <td style="padding:0.875rem 1rem;font-weight:700;
                                               color:#205aa7;">
                                        {{ number_format($detail->total_price) }}₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#f8fafc;">
                            <tr>
                                <td colspan="3" style="padding:0.875rem 1rem;
                                                       text-align:right;font-weight:700;">
                                    Tổng giá trị nhập:
                                </td>
                                <td style="padding:0.875rem 1rem;font-size:1.2rem;
                                           font-weight:800;color:#205aa7;">
                                    {{ number_format($import->total_value) }}₫
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Thông tin phiếu --}}
            <div class="bg-white rounded-lg shadow-sm mb-4" style="overflow:hidden;">
                <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                    <span class="fw-semibold" style="color:#0f172a;">
                        <i class="fa-solid fa-info-circle text-primary me-1"></i>
                        Thông tin phiếu
                    </span>
                </div>
                <div style="padding:1.25rem;">
                    @php
                        $infoRows = [
                            ['label'=>'Người tạo','value'=>$import->createdBy->name ?? '—'],
                            ['label'=>'Ngày tạo', 'value'=>$import->created_at->format('H:i, d/m/Y')],
                            ['label'=>'Số sản phẩm','value'=>$import->details->count().' SP'],
                        ];
                    @endphp
                    @foreach($infoRows as $row)
                        <div class="d-flex justify-content-between align-items-center"
                             style="{{ !$loop->last ? 'margin-bottom:0.75rem;
                                       padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9;' : '' }}">
                            <span class="text-muted" style="font-size:0.875rem;">
                                {{ $row['label'] }}
                            </span>
                            <span style="font-size:0.9rem;font-weight:600;color:#1e293b;">
                                {{ $row['value'] }}
                            </span>
                        </div>
                    @endforeach

                    @if($import->note)
                        <div style="margin-top:0.875rem;padding:0.75rem;background:#f8fafc;
                                    border-radius:8px;border-left:3px solid #205aa7;">
                            <div style="font-size:0.75rem;color:#64748b;
                                        font-weight:600;margin-bottom:0.25rem;">Ghi chú</div>
                            <div style="font-size:0.875rem;color:#374151;">{{ $import->note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            @if($import->status === 'draft')
                <div class="rounded-lg shadow-sm mb-3"
                     style="overflow:hidden;border:2px solid #22c55e;">
                    <div style="padding:1rem 1.25rem;background:#22c55e;">
                        <span class="fw-semibold" style="color:#fff;">
                            <i class="fa-solid fa-check-double me-1"></i>
                            Xác nhận nhập kho
                        </span>
                    </div>
                    <div style="padding:1.25rem;background:#fff;">
                        <div style="padding:0.75rem;background:#fef9c3;border-radius:8px;
                                    border-left:3px solid #fbbf24;
                                    font-size:0.8125rem;color:#78350f;
                                    line-height:1.6;margin-bottom:1rem;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Sau khi xác nhận, kho sẽ được cộng hàng.
                            <strong>Hành động này không thể hoàn tác.</strong>
                        </div>
                        <form method="POST"
                              action="{{ route('admin.inventory.import.confirm', $import->id) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-success fw-semibold w-100"
                                    style="padding:0.75rem;"
                                    onclick="return confirm(
                                        'Xác nhận nhập kho phiếu {{ $import->import_code }}?\n\nHành động này không thể hoàn tác!'
                                    )">
                                <i class="fa-solid fa-check me-1"></i>
                                Xác nhận nhập kho
                            </button>
                        </form>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('admin.inventory.import.cancel', $import->id) }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-outline-danger fw-semibold w-100"
                            onclick="return confirm('Hủy phiếu nhập {{ $import->import_code }}?')">
                        <i class="fa-solid fa-xmark me-1"></i>Hủy phiếu nhập
                    </button>
                </form>

            @elseif($import->status === 'confirmed')
                <div style="padding:1.25rem;background:#f0fdf4;border-radius:12px;
                            border:1px solid #bbf7d0;text-align:center;">
                    <i class="fa-solid fa-circle-check"
                       style="font-size:2rem;color:#22c55e;margin-bottom:0.5rem;"></i>
                    <p class="fw-semibold mb-0" style="color:#166534;">Đã nhập vào kho thành công</p>
                </div>

            @elseif($import->status === 'cancelled')
                <div style="padding:1.25rem;background:#fef2f2;border-radius:12px;
                            border:1px solid #fecaca;text-align:center;">
                    <i class="fa-solid fa-circle-xmark"
                       style="font-size:2rem;color:#ef4444;margin-bottom:0.5rem;"></i>
                    <p class="fw-semibold mb-0" style="color:#991b1b;">Phiếu đã bị hủy</p>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection