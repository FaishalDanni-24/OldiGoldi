<?php

use App\Http\Controllers\Chats\ChatMessageController;
use App\Http\Controllers\Chats\ConversationController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Orders\OrderItemController;
use App\Http\Controllers\Orders\OrderStatusHistoryController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Payments\PaymentWebhookEventController;
use App\Http\Controllers\Products\CategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\ProductImageController;
use App\Http\Controllers\Shipments\ShipmentController;
use App\Http\Controllers\Shipments\ShipmentTrackingEventController;
use App\Http\Controllers\Users\AddressController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Carts\CartController;
use App\Http\Controllers\Carts\CartItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- Public ---
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);

    // --- Auth (sanctum) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [UserController::class, 'me']);
        Route::patch('me', [UserController::class, 'updateMe']);

        // Users (admin)
        Route::middleware('admin')->group(function () {
            Route::get('admin/users', [UserController::class, 'index']);
            Route::get('admin/users/{user}', [UserController::class, 'show']);
            Route::patch('admin/users/{user}/status', [UserController::class, 'updateStatus']);
            Route::patch('admin/users/{user}/role', [UserController::class, 'updateRole']);

            // Roles (admin)
            Route::apiResource('roles', RoleController::class);
        });

        // Addresses
        Route::get('addresses', [AddressController::class, 'index']);
        Route::post('addresses', [AddressController::class, 'store']);
        Route::get('addresses/{address}', [AddressController::class, 'show']);
        Route::patch('addresses/{address}', [AddressController::class, 'update']);
        Route::delete('addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('addresses/{address}/default', [AddressController::class, 'setDefault']);

        // Products (admin/seller)
        Route::middleware('admin')->group(function () {
            Route::post('products', [ProductController::class, 'store']);
            Route::patch('products/{product}', [ProductController::class, 'update']);
            Route::delete('products/{product}', [ProductController::class, 'destroy']);
            Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);

            // Product images (admin)
            Route::post('products/{product}/images', [ProductImageController::class, 'store']);
            Route::patch('product-images/{image}', [ProductImageController::class, 'update']);
            Route::delete('product-images/{image}', [ProductImageController::class, 'destroy']);

            // Categories (admin)
            Route::post('categories', [CategoryController::class, 'store']);
            Route::patch('categories/{category}', [CategoryController::class, 'update']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
        });

        Route::get('products/{product}/images', [ProductImageController::class, 'index']);

        // Cart
        Route::get('cart', [CartController::class, 'show']);
        Route::delete('cart', [CartController::class, 'clear']);
        Route::post('cart/items', [CartItemController::class, 'store']);
        Route::patch('cart/items/{cartItem}', [CartItemController::class, 'update']);
        Route::delete('cart/items/{cartItem}', [CartItemController::class, 'destroy']);

        // Checkout / Orders
        Route::post('checkout', [OrderController::class, 'store']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
        Route::get('orders/{order}/items', [OrderItemController::class, 'index']);
        Route::get('orders/{order}/status-history', [OrderStatusHistoryController::class, 'index']);

        // Payments
        Route::post('orders/{order}/payments', [PaymentController::class, 'store']);
        Route::get('payments/{payment}', [PaymentController::class, 'show']);
        Route::post('payments/{payment}/cancel', [PaymentController::class, 'cancel']);
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund']);

        // Shipments
        Route::post('orders/{order}/shipment', [ShipmentController::class, 'store']);
        Route::get('shipments/{shipment}', [ShipmentController::class, 'show']);
        Route::post('shipments/{shipment}/track', [ShipmentController::class, 'track']);
        Route::post('shipments/{shipment}/cancel', [ShipmentController::class, 'cancel']);
        Route::get('shipments/{shipment}/tracking-events', [ShipmentTrackingEventController::class, 'index']);

        // Chats
        Route::get('conversations', [ConversationController::class, 'index']);
        Route::post('conversations', [ConversationController::class, 'store']);
        Route::get('conversations/{conversation}', [ConversationController::class, 'show']);
        Route::post('conversations/{conversation}/close', [ConversationController::class, 'close']);
        Route::get('conversations/{conversation}/messages', [ChatMessageController::class, 'index']);
        Route::post('conversations/{conversation}/messages', [ChatMessageController::class, 'store']);
        Route::patch('chat-messages/{chatMessage}/read', [ChatMessageController::class, 'markRead']);
    });

    // --- Webhooks (public, throttled, signature-verified) ---
    Route::middleware(['throttle:60,1', 'verify.payment'])->group(function () {
        Route::post('payments/webhook/{provider}', [PaymentWebhookEventController::class, 'store']);
    });
    Route::middleware(['throttle:60,1', 'verify.tracking'])->group(function () {
        Route::post('tracking/webhook/{provider}', [ShipmentTrackingEventController::class, 'store']);
    });
});
