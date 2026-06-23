<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال حركة خزينة ماليّة #{{ $transaction->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 30px;
            color: #222;
            background-color: #fff;
        }
        .receipt-wrapper {
            max-width: 750px;
            margin: 0 auto;
            border: 3px double #222;
            padding: 30px;
            border-radius: 8px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-table td {
            border: none;
            vertical-align: top;
        }
        .brand-title {
            font-size: 26px;
            font-weight: bold;
            color: #1b4f72;
        }
        .brand-sub {
            font-size: 14px;
            color: #555;
            margin-top: 3px;
        }
        .receipt-meta {
            text-align: left;
            font-size: 15px;
            line-height: 1.6;
        }
        .receipt-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 25px 0;
            padding: 8px 0;
            background-color: #f2f4f4;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table td {
            padding: 14px 10px;
            font-size: 18px;
            border-bottom: 1px dashed #bbb;
        }
        .label {
            font-weight: bold;
            width: 25%;
            color: #34495e;
        }
        .amount-container {
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #222;
            padding: 8px 20px;
            background-color: #fdfefe;
            display: inline-block;
            border-radius: 5px;
        }
        .signatures {
            margin-top: 60px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .sig-box {
            text-align: center;
            width: 45%;
            font-size: 16px;
        }
        .sig-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 80%;
            margin-right: auto;
            margin-left: auto;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #eaeded;
            padding-top: 15px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .receipt-wrapper { border: 2px solid #000; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 750px; margin: 0 auto 15px auto; text-align: left;">
        <button onclick="window.print();" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">طباعة السند الحالي</button>
        <button onclick="window.close();" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">إغلاق النافذة</button>
    </div>

    <div class="receipt-wrapper">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand-title">Albuazi_soft</div>
                    <div class="brand-sub">أنظمة إدارة الشركات وتدقيق الحسابات</div>
                </td>
                <td class="receipt-meta">
                    <div><strong>رقم الإيصال:</strong> #{{ $transaction->id }}</div>
                    <div><strong>تاريخ الحركة:</strong> {{ $transaction->transaction_date }}</div>
                    <div><strong>حالة الحركة:</strong> معتمدة ومقيدة</div>
                </td>
            </tr>
        </table>

        <div class="receipt-title">
            @if($transaction->type == 'deposit')
                وصل إيداع سيولة ماليّة
            @else
                وصل تسليم سيولة نقديّة (سحب للإدارة)
            @endif
        </div>

        <table class="details-table">
            <tr>
                <td class="label">القيمة المالية:</td>
                <td>
                    <span class="amount-container">
                        {{ number_format($transaction->amount, 2) }} د.ل
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label">المُسلِّم للمبلغ:</td>
                <td><strong>{{ $transaction->delivered_by ?? 'غير محدد' }}</strong></td>
            </tr>
            <tr>
                <td class="label">المُستلِّم للمبلغ:</td>
                <td><strong>{{ $transaction->received_by ?? 'غير محدد' }}</strong></td>
            </tr>
            <tr>
                <td class="label">البيان / السبب:</td>
                <td style="font-size: 16px; color: #555;">{{ $transaction->notes ?? 'لا توجد ملاحظات إضافية مسجلة.' }}</td>
            </tr>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <strong>توقيع واعتِماد (المُسلِّم)</strong>
                <div class="sig-line"></div>
            </div>
            <div class="sig-box">
                <strong>توقيع وإقرار (المُستلِّم)</strong>
                <div class="sig-line"></div>
            </div>
        </div>

        <div class="footer">
            تم إصدار هذا السند تلقائياً عبر منظومة الخزينة والصناديق | © {{ date('Y') }} Albuazi_soft
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>