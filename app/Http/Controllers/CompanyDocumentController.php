<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyDocumentController extends Controller
{
    /**
     * 1. عرض صفحة الأرشفة الرئيسية
     */
    public function index()
    {
        // تم استخدام withCount لجلب عدد المستندات بكفاءة
        $companies = Company::withCount('documents')->get();
        return view('companies.CompanyDocument.index', compact('companies'));
    }

    /**
     * 2. جلب كافة المستندات لشركة معينة (JSON)
     */
    public function getCompanyDocuments($id)
    {
        $company = Company::findOrFail($id);
        $allDocuments = collect();

        // المستندات الأساسية من جدول الشركات
        // ملاحظة: تأكد أن هذه الحقول في قاعدة البيانات تخزن المسار بدون كلمة storage/ في بدايتها
        $basics = [
            'سجل تجاري' => $company->cr_full_path, 
            'ترخيص' => $company->license_full_path,
        ];

        foreach ($basics as $name => $path) {
            if ($path) {
                $allDocuments->push([
                    'id' => 0,
                    'document_name' => $name,
                    // نمرر المسار كما هو، وسنتعامل معه في الفرونت إند عبر دالة asset()
                    'file_path' => $path, 
                    'file_size' => 'أساسي',
                    'notes' => 'مستند نظام أساسي',
                    'created_at' => $company->created_at->format('Y-m-d'),
                ]);
            }
        }

        // المرفقات الإضافية من الجدول الجديد
        $extraDocs = $company->documents()->latest()->get();
        foreach ($extraDocs as $doc) {
            $allDocuments->push([
                'id' => $doc->id,
                'document_name' => $doc->document_name,
                // الإصلاح هنا: نرسل المسار النظيف فقط
                'file_path' => $doc->file_path, 
                'file_size' => $doc->file_size,
                'notes' => $doc->notes,
                'created_at' => $doc->created_at->format('Y-m-d'),
            ]);
        }

        return response()->json($allDocuments);
    }

    /**
     * 3. دالة رفع مستند جديد
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // التخزين باستخدام القرص 'public'
            // هذا سيضع الملف في: storage/app/public/assets/admin/uploads/documents
            $path = $file->store('assets/admin/uploads/documents', 'public');
            
            // هام: نخزن المسار مسبوقاً بـ storage/ لمرة واحدة فقط لسهولة الاستدعاء
            $finalPath = 'storage/' . $path;

            $document = CompanyDocument::create([
                'company_id' => $id,
                'document_name' => $request->document_name,
                'file_path' => $finalPath,
                'file_extension' => $file->getClientOriginalExtension(),
                'file_size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                'document_type' => 'additional',
                'notes' => $request->notes,
            ]);

            return response()->json(['status' => 'success', 'document' => $document]);
        }

        return response()->json(['status' => 'error', 'message' => 'فشل رفع الملف'], 400);
    }
}