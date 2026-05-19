<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method', 'shop_logo');

        // Handle File Upload
        if ($request->hasFile('shop_logo')) {
            $path = $request->file('shop_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'shop_logo'],
                ['value' => $path]
            );
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear cache if we were using it (optional optimization)
        \Illuminate\Support\Facades\Cache::forget('global_settings');

        return redirect()->back()->with('success', 'Configuración actualizada.');
    }
}
