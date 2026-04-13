<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\License;
use App\Models\CommercialRegister;
use App\Models\Chamber;
use App\Models\Importer;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // 1. إحصائيات الشركات
        $totalCompanies = Company::count();

        // 2. إحصائيات التراخيص
        $totalLicenses = License::count();
        $expiredLicenses = License::where('expiry_date', '<', Carbon::now())->count();
        $activeLicenses = License::where('expiry_date', '>=', Carbon::now())->count();

        // 3. إحصائيات السجل التجاري
        $totalRegisters = CommercialRegister::count();
        $expiredRegisters = CommercialRegister::where('expiry_date', '<', Carbon::now())->count();

        // 4. إحصائيات الغرفة التجارية
        $totalChamber = Chamber::count();
        $nearExpiryChamber = Chamber::whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)])->count();
          $expiredChamber = Chamber::where('expiry_date', '<', Carbon::now())->count();
        $activeChamber = Chamber::where('expiry_date', '>=', Carbon::now())->count();


        // 5. إحصائيات سجل المستوردين
        $totalImporters = Importer::count();
         $expiredImporters = Importer::where('expiry_date', '<', Carbon::now())->count();
        $activeImporters = Importer::where('expiry_date', '>=', Carbon::now())->count();


        return view('reports.index', compact(
            'totalCompanies', 
            'totalLicenses', 
            'expiredLicenses', 
            'activeLicenses',
            'totalRegisters', 
            'expiredChamber', // أضفنا هذا المتغير لعرض عدد الغرف التجارية التي ستنتهي قريبًا
            'expiredRegisters', 
            'totalChamber', 
            'nearExpiryChamber', 
            'totalImporters',
            'expiredImporters', // أضفنا هذا المتغير لعرض عدد المستوردين الذين ستنتهي تراخيصهم قريبًا
            'activeImporters'
        ));
    }
}