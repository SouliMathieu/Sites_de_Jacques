<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Afficher le formulaire de profil
     *
     * @param Request $request
     * @return View
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Charger les statistiques utilisateur si nécessaire
        $stats = [
            'orders_count' => 0,
            'total_spent' => 0,
            'last_login' => $user->last_login_at,
        ];

        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Mettre à jour les informations du profil
     *
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();

            // Remplir les données validées
            $user->fill($validated);

            // Réinitialiser la vérification email si modifié
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
                
                Log::info('Email modifié - Vérification réinitialisée', [
                    'user_id' => $user->id,
                    'old_email' => $user->getOriginal('email'),
                    'new_email' => $user->email,
                ]);
            }

            $user->save();

            Log::info('Profil mis à jour', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($user->getChanges()),
            ]);

            return redirect()
                ->route('profile.edit')
                ->with('status', 'profile-updated')
                ->with('success', 'Profil mis à jour avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour profil', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Mettre à jour l'avatar
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'avatar.required' => 'Veuillez sélectionner une image.',
            'avatar.image' => 'Le fichier doit être une image.',
            'avatar.mimes' => 'Formats autorisés : JPEG, PNG, WebP.',
            'avatar.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        try {
            $user = $request->user();

            // Supprimer l'ancien avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Sauvegarder le nouveau
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);

            Log::info('Avatar mis à jour', [
                'user_id' => $user->id,
                'avatar_path' => $path,
            ]);

            return back()->with('success', 'Avatar mis à jour avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour avatar', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la mise à jour de l\'avatar.');
        }
    }

    /**
     * Supprimer l'avatar
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->update(['avatar' => null]);

            Log::info('Avatar supprimé', ['user_id' => $user->id]);

            return back()->with('success', 'Avatar supprimé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur suppression avatar', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Mettre à jour le mot de passe
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        try {
            $user = $request->user();
            
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('Mot de passe modifié', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            // Révoquer les autres sessions (optionnel)
            // Auth::logoutOtherDevices($validated['password']);

            return back()->with('success', 'Mot de passe modifié avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur modification mot de passe', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Envoyer un email de vérification
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendVerification(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Votre email est déjà vérifié.');
        }

        try {
            $user->sendEmailVerificationNotification();

            Log::info('Email de vérification envoyé', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return back()->with('success', 'Email de vérification envoyé !');

        } catch (\Exception $e) {
            Log::error('Erreur envoi email vérification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de l\'envoi de l\'email.');
        }
    }

    /**
     * Supprimer le compte utilisateur
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Veuillez entrer votre mot de passe pour confirmer.',
            'password.current_password' => 'Le mot de passe est incorrect.',
        ]);

        try {
            DB::beginTransaction();

            $user = $request->user();
            $userId = $user->id;
            $userName = $user->name;

            // Supprimer l'avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Anonymiser ou supprimer les données liées (optionnel)
            // $user->orders()->update(['customer_name' => 'Utilisateur supprimé']);

            // Déconnecter l'utilisateur
            Auth::logout();

            // Supprimer le compte (soft delete)
            $user->delete();

            // Invalider la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            DB::commit();

            Log::info('Compte utilisateur supprimé', [
                'user_id' => $userId,
                'user_name' => $userName,
                'ip' => $request->ip(),
            ]);

            return redirect('/')
                ->with('success', 'Votre compte a été supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur suppression compte', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression du compte.');
        }
    }

    /**
     * Télécharger les données personnelles (RGPD)
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadData(Request $request)
    {
        try {
            $user = $request->user();

            $data = [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at->toDateTimeString(),
                    'last_login_at' => $user->last_login_at?->toDateTimeString(),
                ],
                // Ajouter autres données (commandes, etc.)
            ];

            $fileName = 'donnees-personnelles-' . $user->id . '-' . now()->format('Y-m-d') . '.json';

            Log::info('Téléchargement données personnelles', [
                'user_id' => $user->id,
            ]);

            return response()
                ->streamDownload(function () use ($data) {
                    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }, $fileName, [
                    'Content-Type' => 'application/json',
                ]);

        } catch (\Exception $e) {
            Log::error('Erreur téléchargement données', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors du téléchargement.');
        }
    }

    /**
     * Activer l'authentification à deux facteurs (2FA)
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function enable2FA(Request $request): RedirectResponse
    {
        // TODO: Implémenter 2FA avec package comme laravel/fortify
        
        try {
            $user = $request->user();

            // Logique d'activation 2FA ici

            Log::info('2FA activé', ['user_id' => $user->id]);

            return back()->with('success', 'Authentification à deux facteurs activée !');

        } catch (\Exception $e) {
            Log::error('Erreur activation 2FA', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de l\'activation.');
        }
    }

    /**
     * Désactiver l'authentification à deux facteurs
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function disable2FA(Request $request): RedirectResponse
    {
        $request->validateWithBag('disable2FA', [
            'password' => ['required', 'current_password'],
        ]);

        try {
            $user = $request->user();

            // Logique de désactivation 2FA ici

            Log::info('2FA désactivé', ['user_id' => $user->id]);

            return back()->with('success', 'Authentification à deux facteurs désactivée.');

        } catch (\Exception $e) {
            Log::error('Erreur désactivation 2FA', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la désactivation.');
        }
    }

    /**
     * Afficher les sessions actives
     *
     * @param Request $request
     * @return View
     */
    public function sessions(Request $request): View
    {
        // TODO: Récupérer les sessions actives depuis la base de données
        
        $sessions = [];

        return view('profile.sessions', [
            'user' => $request->user(),
            'sessions' => $sessions,
        ]);
    }

    /**
     * Révoquer une session spécifique
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function revokeSession(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            // TODO: Logique de révocation de session

            return back()->with('success', 'Session révoquée avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur révocation session', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de la révocation.');
        }
    }
}
