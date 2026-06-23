<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->get()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Only admins can change settings
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'phone_otp_enabled' => ['required', 'in:0,1'],
        ]);

        Setting::set('phone_otp_enabled', $request->phone_otp_enabled);

        return redirect()->route('admin.settings.index')
                         ->with('success', 'Settings saved successfully.');
    }
}
