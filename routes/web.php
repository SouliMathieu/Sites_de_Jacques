<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produit/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categorie/{slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::view('/contact', 'contact')->name('contact');

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

//
// Routes des commandes PUBLIC
//

// Page de création de commande (formulaire) avec produit pré‑sélectionné via ?product_id=
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');

// Enregistrer la commande
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Afficher une commande (si besoin côté public)
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Paiement & états
Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');
Route::post('/orders/{order}/payment-at-delivery', [OrderController::class, 'paymentAtDelivery'])->name('orders.payment-at-delivery');

//
// ROUTES DE STOCKAGE (Images et Vidéos)
//
Route::get('storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404, 'Fichier non trouvé');
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type'  => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
        'Expires'       => gmdate('D, d M Y H:i:s \\G\\M\\T', time() + 31536000),
    ]);
})->where('path', '.*')->name('storage.file');

//
// ROUTES D'AUTHENTIFICATION
//
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

require __DIR__ . '/auth.php';

//
// ROUTES D'ADMINISTRATION
//
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);

        Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/payment-status', [App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
        Route::delete('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');
        Route::get('orders/{order}/receipt', [App\Http\Controllers\Admin\OrderController::class, 'generateReceipt'])->name('orders.receipt');

        Route::post('upload-files', [App\Http\Controllers\Admin\ImageUploadController::class, 'uploadProductImages'])->name('upload-files');
        Route::delete('delete-file', [App\Http\Controllers\Admin\ImageUploadController::class, 'deleteFile'])->name('delete-file');

        Route::resource('ad-campaigns', App\Http\Controllers\Admin\AdCampaignController::class);
        Route::post('ad-campaigns/{adCampaign}/launch', [App\Http\Controllers\Admin\AdCampaignController::class, 'launch'])->name('ad-campaigns.launch');

        Route::get('test-upload', function () {
            return response()->json([
                'message'        => 'Upload route accessible!',
                'upload_url'     => route('admin.upload-files'),
                'delete_url'     => route('admin.delete-file'),
                'storage_path'   => storage_path('app/public'),
                'storage_writable' => is_writable(storage_path('app/public')),
                'storage_link'   => url('storage'),
            ]);
        })->name('test-upload');
    });
