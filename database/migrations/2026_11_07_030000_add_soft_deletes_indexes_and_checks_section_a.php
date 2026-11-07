<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Section A #5 — No soft deletes, checks, or indexes anywhere beyond FK auto-indexes.
     * This migration adds:
     *  - soft deletes (deleted_at) to users, products, categories, addresses, carts, orders
     *  - indexes on foreign keys + status/filter columns
     *  - CHECK constraints (MySQL only) on money / quantity columns
     */
    public function up(): void
    {
        // --- Soft deletes ---
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('role_id');
            $table->index('account_status');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('user_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('category_id');
            $table->index('user_id');
            $table->index('product_status');
            $table->index('condition_grade');
            $table->index('brand');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('user_id');
            $table->index('cart_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('customer_id');
            $table->index('address_id');
            $table->index('order_status');
        });

        // --- Indexes only (no soft deletes; transactional / append-only tables) ---
        Schema::table('product_images', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('payment_status');
            $table->index('expires_at');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('shipment_status');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('admin_id');
            $table->index('order_id');
            $table->index('product_id');
            $table->index('conversation_status');
            $table->index(['customer_id', 'conversation_status']);
        });

        Schema::table('payment_webhook_events', function (Blueprint $table) {
            $table->index('payment_id');
            $table->index('processing_status');
            $table->index('received_at');
        });

        Schema::table('shipment_tracking_events', function (Blueprint $table) {
            $table->index('happened_at');
            $table->index('event_status');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index('sender_id');
            $table->index('conversation_id');
            $table->index('is_read');
            $table->index('sent_at');
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('user_id');
            $table->index('new_status');
            $table->index('changed_at');
        });

        // --- CHECK constraints (MySQL 8+ only; SQLite cannot ALTER TABLE ADD CONSTRAINT) ---
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD CONSTRAINT chk_products_price_ge_0 CHECK (price >= 0)');

            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_subtotal_ge_0 CHECK (subtotal >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_shipping_cost_ge_0 CHECK (shipping_cost >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_discount_amount_ge_0 CHECK (discount_amount >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_total_amount_ge_0 CHECK (total_amount >= 0)');

            DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_items_unit_price_ge_0 CHECK (unit_price >= 0)');
            DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_items_quantity_gt_0 CHECK (quantity > 0)');

            DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payments_amount_ge_0 CHECK (amount >= 0)');

            DB::statement('ALTER TABLE cart_items ADD CONSTRAINT chk_cart_items_quantity_gt_0 CHECK (quantity > 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products DROP CHECK chk_products_price_ge_0');

            DB::statement('ALTER TABLE orders DROP CHECK chk_orders_subtotal_ge_0');
            DB::statement('ALTER TABLE orders DROP CHECK chk_orders_shipping_cost_ge_0');
            DB::statement('ALTER TABLE orders DROP CHECK chk_orders_discount_amount_ge_0');
            DB::statement('ALTER TABLE orders DROP CHECK chk_orders_total_amount_ge_0');

            DB::statement('ALTER TABLE order_items DROP CHECK chk_order_items_unit_price_ge_0');
            DB::statement('ALTER TABLE order_items DROP CHECK chk_order_items_quantity_gt_0');

            DB::statement('ALTER TABLE payments DROP CHECK chk_payments_amount_ge_0');

            DB::statement('ALTER TABLE cart_items DROP CHECK chk_cart_items_quantity_gt_0');
        }

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropIndex(['changed_at']);
            $table->dropIndex(['new_status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['is_read']);
            $table->dropIndex(['conversation_id']);
            $table->dropIndex(['sender_id']);
        });

        Schema::table('shipment_tracking_events', function (Blueprint $table) {
            $table->dropIndex(['event_status']);
            $table->dropIndex(['happened_at']);
        });

        Schema::table('payment_webhook_events', function (Blueprint $table) {
            $table->dropIndex(['received_at']);
            $table->dropIndex(['processing_status']);
            $table->dropIndex(['payment_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'conversation_status']);
            $table->dropIndex(['conversation_status']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['order_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['customer_id']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['shipment_status']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_status']);
            $table->dropIndex(['address_id']);
            $table->dropIndex(['customer_id']);
            $table->dropSoftDeletes();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['cart_status']);
            $table->dropIndex(['user_id']);
            $table->dropSoftDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['brand']);
            $table->dropIndex(['condition_grade']);
            $table->dropIndex(['product_status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_id']);
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['account_status']);
            $table->dropIndex(['role_id']);
            $table->dropSoftDeletes();
        });
    }
};
