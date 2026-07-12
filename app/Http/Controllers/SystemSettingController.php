<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        return Inertia::render('SystemSettings/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'nullable|string|max:255',
            'school_address' => 'nullable|string',
            'school_phone' => 'nullable|string',
            'school_email' => 'nullable|email',
            'school_website' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        if ($request->hasFile('school_logo')) {
            $request->validate(['school_logo' => 'image|max:2048']);
            $file = $request->file('school_logo');
            $filename = time() . '_logo.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/settings', $filename);
            
            SystemSetting::updateOrCreate(
                ['key' => 'school_logo'],
                ['value' => Storage::url($path)]
            );
        }

        return redirect()->back()->with('message', 'Pengaturan sistem berhasil disimpan.');
    }
}
