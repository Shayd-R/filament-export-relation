<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Print' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 14px;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }
        
        .print-header h1 {
            font-size: 28px;
            color: #1a202c;
            margin-bottom: 10px;
        }
        
        .print-header .meta {
            color: #718096;
            font-size: 12px;
        }
        
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .print-table thead {
            background-color: #2d3748;
            color: white;
        }
        
        .print-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #cbd5e0;
        }
        
        .print-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .print-table tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }
        
        .print-table tbody tr:hover {
            background-color: #edf2f7;
        }
        
        .print-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 12px;
        }
        
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-button {
            background-color: #4299e1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        
        .print-button:hover {
            background-color: #3182ce;
        }
        
        @media print {
            .print-controls {
                display: none;
            }
            
            body {
                padding: 0;
            }
            
            .print-table tbody tr:hover {
                background-color: transparent;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="print-button" onclick="window.print()">
            🖨️ Imprimir
        </button>
        <button class="print-button" onclick="window.close()">
            ❌ Cerrar
        </button>
    </div>

    <div class="print-header">
        <h1>{{ $title ?? 'Impresión' }}</h1>
        <div class="meta">
            Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}
            @if(isset($recordCount))
                | Total: {{ $recordCount }} registros
            @endif
        </div>
    </div>

    @if(isset($data) && count($data) > 0)
        <table class="print-table">
            <thead>
                <tr>
                    @foreach(array_keys($data[0]) as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{{ $value ?? '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 50px; color: #a0aec0;">
            <p>📭 No hay datos disponibles para imprimir</p>
        </div>
    @endif

    <div class="print-footer">
        <p>{{ config('app.name') }} - Documento generado automáticamente por Filament Smart Export</p>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>
