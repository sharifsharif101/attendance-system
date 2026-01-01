<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class WelcomeController extends Controller
{
    public function index()
    {
        // جلب إعدادات الشركة
       $companyName = Setting::get('company_name', 'شركة خالد السوداني للمقاولات');
        $companyLogo = Setting::get('company_logo', null);
        
        return view('welcome-dashboard', compact('companyName', 'companyLogo'));
    }
}