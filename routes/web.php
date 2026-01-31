<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
// Routes publiques
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produit/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categorie/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::view('/contact', 'contact')->name('contact');
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::resource('orders', App\Http\Controllers\OrderController::class);
Route::get('/orders/{order}/payment', [App\Http\Controllers\OrderController::class, 'payment'])->name('orders.payment');
Route::post('/orders/{order}/confirm-payment', [App\Http\Controllers\OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
Route::get('/orders/{order}/success', [App\Http\Controllers\OrderController::class, 'success'])->name('orders.success');
Route::post('/orders/{order}/payment-at-delivery', [OrderController::class, 'paymentAtDelivery'])->name('orders.payment-at-delivery');


// Routes d'authentification par défaut
Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Routes d'administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);

    // Gestion des commandes
    Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment-status', [App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    // ✅ NOUVEAU : Route pour supprimer les commandes
    Route::delete('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');

    // Upload de fichiers - Routes corrigées
    Route::post('upload-files', [App\Http\Controllers\Admin\ImageUploadController::class, 'uploadProductImages'])->name('upload-files');
    Route::delete('delete-file', [App\Http\Controllers\Admin\ImageUploadController::class, 'deleteFile'])->name('delete-file');
 // Routes pour les campagnes publicitaires
    Route::resource('ad-campaigns', App\Http\Controllers\Admin\AdCampaignController::class);
    Route::post('ad-campaigns/{adCampaign}/launch', [App\Http\Controllers\Admin\AdCampaignController::class, 'launch'])->name('ad-campaigns.launch');
    // Route de test pour vérifier les uploads
    Route::get('test-upload', function() {
        return response()->json([
            'message' => 'Upload route accessible!',
            'upload_url' => route('admin.upload-files'),
            'delete_url' => route('admin.delete-file'),
            'storage_path' => storage_path('app/public'),
            'storage_writable' => is_writable(storage_path('app/public'))
        ]);
    })->name('test-upload');
});
