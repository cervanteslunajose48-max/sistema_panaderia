<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Inventario - Impresión</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Historial de Movimientos de Inventario</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer;">Imprimir / Guardar
            como PDF</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Producto/Insumo</th>
                <th>Almacén</th>
                <th>Cantidad</th>
                <th>Usuario</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($movement->type == 'production_in') <span style="color: green;">Producción (Entrada)</span>
                        @elseif($movement->type == 'production_out') <span style="color: red;">Producción (Salida)</span>
                        @elseif($movement->type == 'sale') <span style="color: blue;">Venta</span>
                        @elseif($movement->type == 'adjustment') <span style="color: orange;">Ajuste</span>
                        @elseif($movement->type == 'purchase') <span style="color: green;">Compra</span>
                        @else {{ $movement->type }}
                        @endif
                    </td>
                    <td>
                        @if($movement->productVariant)
                            {{ $movement->productVariant->product->name }} - {{ $movement->productVariant->name }}
                        @elseif($movement->supply)
                            {{ $movement->supply->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $movement->warehouse ? $movement->warehouse->name : '-' }}</td>
                    <td>
                        <span style="font-weight: bold; color: {{ $movement->quantity > 0 ? 'green' : 'red' }};">
                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 4) }}
                        </span>
                    </td>
                    <td>{{ $movement->user ? $movement->user->name : 'Sís' }}</td>
                    <td>{{ $movement->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        // Auto-print on load if desired, or let user click button
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>