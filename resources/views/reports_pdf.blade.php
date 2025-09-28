<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير البطاقات</title>
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('{{ public_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Amiri', serif;
            direction: rtl;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: right;
        }

        thead th {
            background: #f3f4f6;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>تقرير البطاقات والنقاط</h1>

    <h2>البطاقات</h2>
    <table>
        <thead>
            <tr>
                <th>الكوافير</th>
                <th>البطاقة</th>
                <th>تاريخ الانتهاء</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expiring as $e)
                <tr>
                    <td>{{ $e->Hairdresser_Name }}</td>
                    <td>{{ $e->Card_Name }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($e->Expiration_Date)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>نقاط الكوافيرات</h2>
    <table>
        <thead>
            <tr>
                <th>الكوافير</th>
                <th>النشاط</th>
                <th>النقاط</th>
                <th>نوع البطاقة</th>
                <th>تاريخ الإصدار</th>
                <th>تاريخ الانتهاء</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $r)
                <tr>
                    <td>{{ $r->Hairdresser_Name }}</td>
                    <td>{{ $r->Activity_Name ?? '-' }}</td>
                    <td>{{ $r->Total_Points }}</td>
                    <td>{{ $r->Card_Name ?? '-' }}</td>
                    <td>{{ $r->Release_Date ? \Illuminate\Support\Carbon::parse($r->Release_Date)->format('Y-m-d') : '-' }}
                    </td>
                    <td>{{ $r->Expiration_Date ? \Illuminate\Support\Carbon::parse($r->Expiration_Date)->format('Y-m-d') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
