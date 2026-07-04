<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'phone'         => 'nullable|string|max:30',
            'whatsapp'      => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'address'       => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'maps_embed'    => 'nullable|string',
            'facebook_url'  => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url'   => 'nullable|url|max:255',
            'about_title'   => 'nullable|string|max:255',
            'about_text'    => 'nullable|string',
            'slogan'        => 'nullable|string|max:255',
        ]);

        $keys = [
            'phone', 'whatsapp', 'email', 'address', 'opening_hours', 'maps_embed',
            'facebook_url', 'instagram_url', 'twitter_url',
            'about_title', 'about_text', 'slogan',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key) ?? '');
            }
        }

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function updateAdmin(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:100',
            'current_password'      => 'nullable|string',
            'new_password'          => 'nullable|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Vérifier mot de passe actuel si changement demandé
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Compte admin mis à jour.');
    }

    public function uploadSliderImage(Request $request)
    {
        $request->validate([
            'slider_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->file('slider_image')->store('slider', 'public');

        // Récupère les images existantes
        $existing = json_decode(SiteSetting::get('slider_images', '[]'), true) ?? [];
        $existing[] = $path;

        SiteSetting::set('slider_images', json_encode($existing));

        return back()->with('success', 'Image ajoutée au slider.');
    }

    public function deleteSliderImage(Request $request)
    {
        $request->validate(['path' => 'required|string']);

        $existing = json_decode(SiteSetting::get('slider_images', '[]'), true) ?? [];
        $existing = array_filter($existing, fn($p) => $p !== $request->path);

        Storage::disk('public')->delete($request->path);
        SiteSetting::set('slider_images', json_encode(array_values($existing)));

        return back()->with('success', 'Image supprimée.');
    }
}