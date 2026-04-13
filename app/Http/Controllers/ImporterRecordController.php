<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Importer; // الموديل الأساسي المستخدم في index و store
use App\Models\Company;


// إذا كان جدولك الأساسي هو importers، فالموديل هو Importer

class ImporterRecordController extends Controller
{
    /**
     * عرض قائمة سجلات المستوردين
     */
    public function index(Request $request)
    {
        $query = Importer::with('company');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('importer_number', 'like', '%' . $search . '%')
                  ->orWhereHas('company', function($sub) use ($search) {
                      $sub->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $importers = $query->orderBy('expiry_date', 'asc')->paginate(10);
        return view('companies.importers.index', compact('importers'));
    }

    /**
     * عرض صفحة إضافة سجل جديد
     */
    public function create()
    {
        $companies = Company::whereDoesntHave('importer', function ($query) {
            $query->whereNotNull('importer_number')->where('importer_number', '!=', '');
        })->get();
        return view('companies.importers.create', compact('companies'));
    }

    /**
     * حفظ سجل جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id'      => 'required|exists:companies,id',
            'importer_number' => 'required|unique:importers,importer_number',
            'issue_date'      => 'required|date',
            'expiry_date'     => 'required|date',
        ]);

        Importer::create([
            'company_id'      => $request->company_id,
            'importer_number' => $request->importer_number,
            'issue_date'      => $request->issue_date,
            'expiry_date'     => $request->expiry_date,
            'document_path'   => $request->temp_file_path,
            'created_by'      => auth()->id(),
        ]);

        return redirect()->route('importers.index')->with('success', 'تم إضافة سجل المستوردين بنجاح ✅');
    }

    /**
     * الدالة الناقصة: عرض صفحة التجديد
     */
    public function renew($id)
    {
        // تأكد من استخدام اسم الموديل الصحيح (Importer)
        $importer = Importer::with('company')->findOrFail($id);
        return view('companies.importers.renew', compact('importer'));
    }

    /**
     * معالجة بيانات التجديد
     */
   public function updateRenew(Request $request, $id)
{
    // 1. التحقق من البيانات المرسلة من الفورم
    $request->validate([
        'issue_date'     => 'required|date',
        'expiry_date'    => 'required|date',
        'temp_file_path' => 'nullable|string', 
    ]);

    $importer = \App\Models\Importer::findOrFail($id);

    // 2. التحديث بالأسماء الصحيحة لقاعدة البيانات
    $importer->update([
        'issue_date'    => $request->issue_date,
        'expiry_date'   => $request->expiry_date,
        // هنا نستخدم document_path حسب صورة قاعدة البيانات
        'document_path' => $request->temp_file_path ?? $importer->document_path, 
    ]);

    return redirect()->route('importers.index')->with('success', 'تم تجديد سجل المستوردين بنجاح ✅');
}

    public function edit(Importer $importer)
    {
        $companies = Company::all();
        return view('companies.importers.edit', compact('importer', 'companies'));
    }

    public function destroy(Importer $importer)
    {
        $importer->delete();
        return redirect()->route('importers.index')->with('success', 'تم حذف سجل المستورد بنجاح 🗑️');
    }
}