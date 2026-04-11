<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color: #f4f4f5; margin: 0; padding: 0; color: #18181b; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e4e4e7; }
        .header { background-color: #18181b; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; letter-spacing: -0.3px; }
        .header p { color: #a1a1aa; font-size: 13px; margin: 4px 0 0; }
        .body { padding: 32px 40px; }
        .success-badge { display: inline-block; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; border-radius: 9999px; padding: 6px 16px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
        h2 { font-size: 20px; font-weight: 700; margin: 0 0 8px; }
        .intro { color: #52525b; font-size: 15px; margin: 0 0 28px; }
        .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #a1a1aa; margin: 0 0 12px; }
        .info-box { background: #f4f4f5; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; border-bottom: 1px solid #e4e4e7; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #71717a; }
        .info-row .value { font-weight: 600; text-align: right; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.items th { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #a1a1aa; padding: 0 0 8px; text-align: left; border-bottom: 1px solid #e4e4e7; }
        table.items th.right, table.items td.right { text-align: right; }
        table.items td { font-size: 14px; padding: 10px 0; border-bottom: 1px solid #f4f4f5; vertical-align: top; }
        table.items td .book-title { font-weight: 600; color: #18181b; }
        table.items td .book-qty { font-size: 13px; color: #71717a; }
        .totals { background: #f4f4f5; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; }
        .totals .row { display: flex; justify-content: space-between; font-size: 14px; padding: 4px 0; }
        .totals .row .label { color: #71717a; }
        .totals .row.total { border-top: 1px solid #e4e4e7; margin-top: 8px; padding-top: 12px; font-weight: 700; font-size: 15px; color: #18181b; }
        .footer { background: #f4f4f5; padding: 24px 40px; text-align: center; font-size: 13px; color: #71717a; border-top: 1px solid #e4e4e7; }
        .footer a { color: #18181b; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Your online bookstore</p>
    </div>

    <div class="body">

        <div class="success-badge">&#10003; Order Confirmed</div>

        <h2>Thanks for your order, {{ $order->name }}!</h2>
        <p class="intro">
            We've received your order and we're getting your books ready for shipment.
            You'll be notified when your order is on its way.
        </p>

        <p class="section-title">Order Summary</p>
        <div class="info-box">
            <div class="info-row">
                <span class="label">Order Number</span>
                <span class="value">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date</span>
                <span class="value">{{ $order->created_at->format('M j, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Payment</span>
                <span class="value">
                    @if($order->payment_method === 'bkash')
                        Bkash
                        @if($order->bkash_transaction_id)
                            <br><span style="font-weight:400;font-size:12px;color:#71717a;">TxnID: {{ $order->bkash_transaction_id }}</span>
                        @endif
                    @else
                        Cash on Delivery
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="label">Delivery Address</span>
                <span class="value" style="max-width:260px;">
                    {{ $order->address }},<br>
                    {{ $order->district }}, {{ $order->division }}
                    @if($order->postal_code)
                        – {{ $order->postal_code }}
                    @endif
                </span>
            </div>
        </div>

        <p class="section-title">Items Ordered</p>
        <table class="items">
            <thead>
                <tr>
                    <th>Book</th>
                    <th class="right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="book-title">{{ $item->product->title ?? 'Unknown Book' }}</div>
                        <div class="book-qty">Qty: {{ $item->quantity }}</div>
                    </td>
                    <td class="right">৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="row">
                <span class="label">Subtotal</span>
                <span>৳{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="row">
                <span class="label">Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</span>
                <span style="color:#16a34a;">−৳{{ number_format($order->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="row">
                <span class="label">Shipping</span>
                <span>
                    @if($order->shipping_cost == 0)
                        <span style="color:#16a34a;">Free</span>
                    @else
                        ৳{{ number_format($order->shipping_cost, 2) }}
                    @endif
                </span>
            </div>
            <div class="row total">
                <span>Total</span>
                <span>৳{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

    </div>

    <div class="footer">
        <p>Questions? <a href="mailto:{{ config('mail.from.address') }}">Contact us</a></p>
        <p style="margin-top:8px;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>

</div>
</body>
</html>
