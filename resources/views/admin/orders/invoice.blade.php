<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} — Shelf-E</title>
    @vite(['resources/css/app.css'])
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            font-family: ui-sans-serif, system-ui, sans-serif;
        }

        /* Screen: centre the A4 sheet with a nice shadow */
        .page {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 24px auto;
            background: #fff;
            box-shadow: 0 8px 48px rgba(0,0,0,.12);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
            padding: 14mm 16mm 12mm;
        }

        @media print {
            html, body { background: #fff; }
            .no-print { display: none !important; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                min-height: 297mm;
                max-height: 297mm;
                padding: 14mm 16mm 12mm;
            }
        }

        /* ── Typography helpers ── */
        .label {
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        table { border-collapse: collapse; width: 100%; }
        thead tr { border-bottom: 2px solid #0f172a; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        th { font-size: 8px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; color: #0f172a; padding: 8px 0; }
        td { font-size: 11px; padding: 7px 0; vertical-align: middle; }

        .accent { color: #06b6d4; }
    </style>
</head>
<body>

{{-- ── Screen-only action bar ── --}}
<div class="no-print" style="display:flex;align-items:center;justify-content:space-between;max-width:210mm;margin:0 auto 0;padding:16px 0;">
    <a href="{{ route('admin.orders.show', $order->id) }}"
       style="font-size:13px;font-weight:700;color:#64748b;text-decoration:none;">
        &larr; Back to Order
    </a>
    <button onclick="window.print()"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;background:#0e7490;color:#fff;font-size:13px;font-weight:700;border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 14px rgba(14,116,144,.35);">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Download / Print PDF
    </button>
</div>

{{-- ── A4 Invoice Sheet ── --}}
<div class="page">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:10mm;border-bottom:2px solid #f1f5f9;margin-bottom:8mm;">
        <div>
            <div style="font-size:28px;font-weight:900;letter-spacing:-.03em;color:#0f172a;line-height:1;">
                <span class="accent">Shelf</span>-E
            </div>
            <div style="margin-top:4px;font-size:8px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:#94a3b8;">Premium Bookstore · Dhaka, Bangladesh</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:22px;font-weight:900;letter-spacing:-.02em;color:#0f172a;">INVOICE</div>
            <div style="margin-top:6px;font-size:11px;color:#64748b;line-height:1.7;">
                <div><strong style="color:#0f172a;">Order&nbsp;ID</strong>&nbsp;&nbsp;#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><strong style="color:#0f172a;">Date</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $order->created_at->format('d M Y') }}</div>
                <div>
                    <strong style="color:#0f172a;">Payment</strong>&nbsp;&nbsp;
                    @if($order->payment_method === 'bkash')
                        Bkash
                    @else
                        {{ strtoupper($order->payment_method) }}
                    @endif
                </div>
                @if($order->payment_method === 'bkash' && $order->bkash_transaction_id)
                <div style="font-size:10px;color:#64748b;margin-top:2px;">
                    <strong style="color:#0f172a;">TxID</strong>&nbsp;&nbsp;{{ $order->bkash_transaction_id }}
                </div>
                @endif
                <div style="margin-top:4px;">
                    <span style="display:inline-block;padding:2px 10px;background:#dcfce7;color:#15803d;font-size:9px;font-weight:800;border-radius:999px;letter-spacing:.06em;text-transform:uppercase;">
                        {{ strtoupper($order->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- From / Ship To --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20mm;margin-bottom:8mm;">
        <div>
            <div class="label" style="margin-bottom:5px;">From</div>
            <div style="font-size:13px;font-weight:800;color:#0f172a;">Shelf-E</div>
            <div style="margin-top:4px;font-size:11px;color:#64748b;line-height:1.7;">
                123 Bookworm Lane<br>
                Dhaka, Bangladesh<br>

                support@shelf-e.com
            </div>
        </div>
        <div>
            <div class="label" style="margin-bottom:5px;">Ship To</div>
            <div style="font-size:13px;font-weight:800;color:#0f172a;">{{ $order->name }}</div>
            <div style="margin-top:4px;font-size:11px;color:#64748b;line-height:1.7;">
                {{ $order->address }}<br>
                {{ $order->district }}, {{ $order->division }}@if($order->postal_code) {{ $order->postal_code }}@endif<br>
                <span style="font-weight:700;color:#0f172a;">{{ $order->phone }}</span><br>
                {{ $order->email }}
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <table style="margin-bottom:6mm;">
        <thead>
            <tr>
                <th style="text-align:left;width:55%;">Item Description</th>
                <th style="text-align:center;width:10%;">Qty</th>
                <th style="text-align:right;width:17%;">Unit Price</th>
                <th style="text-align:right;width:18%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td style="font-weight:700;color:#0f172a;">
                    {{ $item->product ? $item->product->title : 'Untitled Book' }}
                    @if($item->product && $item->product->author)
                        <span style="display:block;font-size:10px;font-weight:500;color:#94a3b8;">{{ $item->product->author }}</span>
                    @endif
                </td>
                <td style="text-align:center;color:#475569;font-weight:600;">{{ $item->quantity }}</td>
                <td style="text-align:right;color:#475569;">৳ {{ number_format($item->price, 0) }}</td>
                <td style="text-align:right;font-weight:800;color:#0f172a;">৳ {{ number_format($item->price * $item->quantity, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div style="display:flex;justify-content:flex-end;border-top:2px solid #0f172a;padding-top:5mm;">
        <div style="width:62mm;">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#64748b;padding:4px 0;">
                <span>Subtotal</span>
                <span style="color:#0f172a;font-weight:600;">৳ {{ number_format($order->subtotal, 0) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#64748b;padding:4px 0;">
                <span>Shipping ({{ ucfirst($order->delivery_method) }})</span>
                <span style="color:#0f172a;font-weight:600;">৳ {{ number_format($order->shipping_cost, 0) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:900;color:#06b6d4;border-top:1px solid #e2e8f0;margin-top:4px;padding-top:6px;">
                <span>Grand Total</span>
                <span>৳ {{ number_format($order->total_amount, 0) }}</span>
            </div>
        </div>
    </div>

    {{-- Spacer pushes footer to bottom --}}
    <div style="flex:1;"></div>

    {{-- Footer --}}
    <div style="border-top:1px solid #f1f5f9;padding-top:6mm;display:flex;justify-content:space-between;align-items:flex-end;">
        <div style="font-size:10px;color:#94a3b8;line-height:1.7;">
            Returns accepted within 7 days in original condition.<br>
            Queries? <span style="color:#06b6d4;font-weight:700;">support@shelf-e.com</span>
        </div>
        <div style="text-align:right;font-size:10px;color:#cbd5e1;">
            <span style="font-size:14px;font-weight:900;color:#e2e8f0;letter-spacing:-.02em;"><span style="color:#a5f3fc;">Shelf</span>-E</span><br>
            Generated {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

</div>

</body>
</html>
