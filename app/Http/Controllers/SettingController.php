<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $appName = Setting::get('app_name', 'SIAKAD');
        $appSubtitle = Setting::get('app_subtitle', 'SMP Digital System');

        return view('master.settings', compact('appName', 'appSubtitle'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
            'app_subtitle' => 'nullable|string|max:255',
        ]);

        Setting::set('app_name', $request->app_name);
        Setting::set('app_subtitle', $request->app_subtitle ?? '');

        return back()->with('success', 'Pengaturan Aplikasi berhasil diperbarui.');
    }
}
