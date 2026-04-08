{{--resources/views/admin/orders/invoice.blade.php --}}
 <!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn {{ $order->order_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }

        .invoice {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }

        .invoice-header {
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }

        .invoice-header table {
            width: 100%;
        }

        .company-info h1 {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 5px;
        }

        .invoice-details {
            margin-bottom: 30px;
        }

        .invoice-details table {
            width: 100%;
        }

        .invoice-details td {
            vertical-align: top;
            padding: 10px;
        }

        .section-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            font-size: 16px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items thead {
            background-color: #007bff;
            color: white;
        }

        table.items th,
        table.items td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table.items th {
            font-weight: bold;
        }

        table.items tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-row {
            margin: 10px 0;
        }

        .total-row.grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            border-top: 2px solid #007bff;
            padding-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending { background-color: #ffc107; color: #000; }
        .status-confirmed { background-color: #17a2b8; color: #fff; }
        .status-shipping { background-color: #007bff; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header -->
        <div class="invoice-header">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div class="company-info">
                            <h1>🖥️ ElectroShop</h1>
                            <p>Trường ĐH Phenikaa</p>
                            <p>SĐT: 0912345678</p>
                            <p>Email: info@electroshop.vn</p>
                        </div>
                    </td>
                    <td style="width: 50%;">
                        <div class="invoice-title">
                            <h2>HÓA ĐƠN</h2>
                            <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
                            <p><strong>Ngày:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div class="section-title">THÔNG TIN KHÁCH HÀNG</div>
                        <p><strong>Họ tên:</strong> {{ $order->fullname }}</p>
                        <p><strong>Email:</strong> {{ $order->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                    </td>
                    <td style="width: 50%;">
                        <div class="section-title">THÔNG TIN ĐƠN HÀNG</div>
                        <p><strong>Trạng thái:</strong>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ $order->status_label }}
                            </span>
                        </p>
                        @if($order->note)
                            <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Order Items Table -->
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 50px;">STT</th>
                    <th>Sản phẩm</th>
                    <th style="width: 100px;" class="text-center">Số lượng</th>
                    <th style="width: 120px;" class="text-right">Đơn giá</th>
                    <th style="width: 120px;" class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->product->name }}</td>
                        <td class="text-center">{{ $detail->quantity }}</td>
                        <td class="text-right">{{ number_format($detail->final_price) }}₫</td>
                        <td class="text-right">{{ number_format($detail->final_price * $detail->quantity) }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            @if($order->discount_amount > 0)
                <div class="total-row">
                    Tạm tính: <strong>{{ number_format($order->total_amount) }}₫</strong>
                </div>
                <div class="total-row" style="color: #dc3545;">
                    Giảm giá: <strong>-{{ number_format($order->discount_amount) }}₫</strong>
                </div>
            @endif
            <div class="total-row grand-total">
                <strong>TỔNG THANH TOÁN: {{ number_format($order->final_amount) }}₫</strong>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Cảm ơn quý khách đã mua hàng tại ElectroShop!</strong></p>
            <p>Mọi thắc mắc xin vui lòng liên hệ: 0912345678 hoặc info@electroshop.vn</p>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                Hóa đơn được in tự động bởi hệ thống - Không cần chữ ký
            </p>
        </div>
    </div>
</body>
</html>
