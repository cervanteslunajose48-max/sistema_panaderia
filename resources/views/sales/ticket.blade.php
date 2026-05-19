<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            /* Monospace for alignment */
            width: 80mm;
            /* Standard Thermal Width */
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            color: black;
            background: white;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .border-bottom {
            border-bottom: 1px dashed black;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 2px 0;
            vertical-align: top;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="text-center mb-2">
        <h2 class="font-bold" style="margin:0;">{{ $settings['shop_name'] ?? 'PANADERÍA & PASTELERÍA' }}</h2>
        <p style="margin:0;">{{ $settings['shop_address'] ?? 'Dirección no configurada' }}</p>
        <p style="margin:0;">Tel: {{ $settings['shop_phone'] ?? '---' }}</p>
        @if(isset($settings['shop_tax_id']))
            <p style="margin:0;">RUC/NIT: {{ $settings['shop_tax_id'] }}</p>
        @endif
    </div>

    <div class="border-bottom">
        <p><strong>Orden:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Cajero:</strong> {{ $order->user->name }}</p>
        @if($order->customer)
            <p><strong>Cliente:</strong> {{ $order->customer->name }}</p>
        @endif
    </div>

    <table class="border-bottom">
        <thead>
            <tr style="border-bottom: 1px solid black;">
                <th class="text-left">Prod</th>
                <th class="text-center">Cant</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->variant->product->name }} <br> <small>{{ $item->variant->name }}</small></td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">
                        {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-right font-bold mb-2">
        <p>TOTAL: {{ $globalSettings['currency_symbol'] ?? '$' }}{{ number_format($order->total, 2) }}</p>
    </div>

    <div class="text-center border-bottom">
        <p>¡Gracias por su compra!</p>
        <p>Vuelva pronto</p>
    </div>

    <div class="text-center no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ IMPRIMIR
            OTRA VEZ</button>
    </div>

</body>

</html>