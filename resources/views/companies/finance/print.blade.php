<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة فاتورة #{{ $invoice->id }}</title>
    <style>
        body { 
            font-family: 'XITS', 'Cairo', sans-serif; 
            padding: 40px; 
            color: #333;
        }
        .header-table { 
            width: 100%; 
            margin-bottom: 30px; 
            text-align: center; 
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .info-section { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 20px; 
            font-size: 1.1rem;
        }
        .info-box {
            font-weight: bold;
        }
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        table.main-table th { 
            background-color: #f2f2f2; 
            border: 1px solid #000; 
            padding: 10px; 
        }
        table.main-table td { 
            border: 1px solid #000; 
            padding: 10px; 
            text-align: center; 
        }
        .total-row {
            font-weight: bold;
            background-color: #fafafa;
        }
        .footer-signatures { 
            margin-top: 60px; 
            display: flex; 
            justify-content: space-around; 
            text-align: center; 
        }
        .signature-item p {
            margin: 5px 0;
        }
        .signature-box { 
            border-top: 1px dashed #000; 
            margin-top: 45px; 
            padding-top: 5px; 
            min-width: 200px; 
            font-size: 0.9rem;
            color: #666;
        }
        @media print { 
            .no-print { display: none; } 
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: left; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 25px; cursor: pointer; background: #333; color: #fff; border: none; border-radius: 5px;">
            <i class="fas fa-print"></i> تأكيد أمر الطباعة
        </button>
    </div>

    <div class="header-table">
        <h2>شركة الشهاب للأعمال والاستشارات القانونية</h2>
        <p>سجل العمليات المالية - فاتورة رقم (#{{ $invoice->id }})</p>
    </div>

    <div class="info-section">
        <div class="info-box">اسم الشركة: {{ $invoice->company->name }}</div>
        <div class="info-box">المفوض (الزبون): {{ $invoice->company->representative_name ?? '..........' }}</div>
        <div class="info-box">التاريخ: {{ $invoice->created_at->format('Y-m-d') }}</div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 50px;">رقم</th>
                <th>اسم الخدمة</th>
                <th>الإجراء</th>
                <th>العدد</th>
                <th>المبلغ المالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: right;">{{ $item->service_name }}</td>
                <td>{{ $item->action }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }} د.ل</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: left; padding-left: 20px;">إجمالي القيمة:</td>
                <td>{{ number_format($invoice->total_amount, 2) }} د.ل</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align: left; padding-left: 20px; color: green;">المبلغ الواصل (المدفوع):</td>
                <td>{{ number_format($invoice->paid_amount, 2) }} د.ل</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align: left; padding-left: 20px; color: red;">المبلغ المتبقي:</td>
                <td>{{ number_format($invoice->remaining_amount, 2) }} د.ل</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-signatures">
        <div class="signature-item">
            <p style="font-weight: bold; text-decoration: underline;">محاسب عام الشركة</p>
            <p style="margin-top: 10px;">{{ request('accountant', '..............................') }}</p>
            <div class="signature-box">التوقيع والختم</div>
        </div>

        <div class="signature-item">
            <p style="font-weight: bold; text-decoration: underline;">مشرف عام الشركة</p>
            <p style="margin-top: 10px;">{{ request('manager', '..............................') }}</p>
            <div class="signature-box">التوقيع والختم</div>
        </div>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 0.8rem; color: #888;">
        <p>طبعت بواسطة النظام المالي لشركة الشهاب - {{ date('Y-m-d H:i') }}</p>
    </div>

</body>
</html>