<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $mysql = DB::getDriverName() === 'mysql';

        // 1. orders.order_number: unsignedInteger -> VARCHAR(30) NOT NULL (unique kept)
        if ($mysql) {
            DB::statement('ALTER TABLE orders MODIFY COLUMN order_number VARCHAR(30) NOT NULL');
        }

        // 2. payments: tracking_provider? (no). Idempotency: keep nullable uniques for gateway IDs,
        //    add functional unique index so at most one PENDING payment per order at DB level.
        if ($mysql) {
            DB::statement("CREATE UNIQUE INDEX payments_one_pending_per_order ON payments (order_id, (CASE WHEN payment_status = 'pending' THEN 1 END))");
        }

        // 3. order_items: drop MySQL-only generated column, make line_total a regular decimal,
        //    backfill from unit_price * quantity.
        if (Schema::hasColumn('order_items', 'line_total')) {
            // Drop generated column (MySQL). On other drivers, column may not be generated; just ensure type.
            if ($mysql) {
                DB::statement('ALTER TABLE order_items DROP COLUMN line_total');
                DB::statement('ALTER TABLE order_items ADD COLUMN line_total DECIMAL(12,2) NOT NULL DEFAULT 0');
            }
        }
        DB::table('order_items')->update(['line_total' => DB::raw('unit_price * quantity')]);

        // 4. FK onDelete rules: drop & re-add foreign keys with explicit onDelete.
        $this->readdForeignKeys();

        // 5. carts: one active (non-deleted) cart per user via functional unique index.
        if ($mysql) {
            DB::statement("CREATE UNIQUE INDEX carts_one_active_per_user ON carts ((CASE WHEN cart_status = 'active' AND deleted_at IS NULL THEN user_id END))");
        }

        // 6. addresses: one default (non-deleted) address per user.
        if ($mysql) {
            DB::statement("CREATE UNIQUE INDEX addresses_one_default_per_user ON addresses ((CASE WHEN is_default = 1 AND deleted_at IS NULL THEN user_id END))");
        }

        // 7. product_images: one primary image per product.
        if ($mysql) {
            DB::statement("CREATE UNIQUE INDEX product_images_one_primary ON product_images ((CASE WHEN is_primary = 1 THEN product_id END))");
        }

        // 8. shipments: tracking_provider nullable (courier may not be chosen yet).
        if ($mysql) {
            DB::statement('ALTER TABLE shipments MODIFY COLUMN tracking_provider VARCHAR(60) NULL');
        }

        // 9. shipment_tracking_events: fallback dedup for rows without external_event_id.
        if ($mysql) {
            DB::statement("CREATE UNIQUE INDEX ste_null_event_dedup ON shipment_tracking_events (shipment_id, (CASE WHEN external_event_id IS NULL THEN happened_at END), (CASE WHEN external_event_id IS NULL THEN event_code END), (CASE WHEN external_event_id IS NULL THEN location END))");
        }

        // 10. order_status_histories: convert free strings to ENUM matching orders.order_status.
        if ($mysql) {
            DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN old_status ENUM('pending_payment','paid','processing','shipped','delivered','completed','cancelled','refunded') NULL");
            DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN new_status ENUM('pending_payment','paid','processing','shipped','delivered','completed','cancelled','refunded') NOT NULL");
        }
    }

    public function down(): void
    {
        $mysql = DB::getDriverName() === 'mysql';

        if ($mysql) {
            DB::statement('ALTER TABLE order_status_histories MODIFY COLUMN new_status VARCHAR(30) NOT NULL');
            DB::statement('ALTER TABLE order_status_histories MODIFY COLUMN old_status VARCHAR(30) NULL');

            DB::statement('DROP INDEX ste_null_event_dedup ON shipment_tracking_events');

            DB::statement('ALTER TABLE shipments MODIFY COLUMN tracking_provider VARCHAR(60) NOT NULL');

            DB::statement('DROP INDEX product_images_one_primary ON product_images');

            DB::statement('DROP INDEX addresses_one_default_per_user ON addresses');

            DB::statement('DROP INDEX carts_one_active_per_user ON carts');

            DB::statement('DROP INDEX payments_one_pending_per_order ON payments');

            DB::statement('ALTER TABLE orders MODIFY COLUMN order_number INT UNSIGNED NOT NULL');
        }

        // Restore line_total as generated column (MySQL only)
        if ($mysql) {
            DB::statement('ALTER TABLE order_items DROP COLUMN line_total');
            DB::statement("ALTER TABLE order_items ADD COLUMN line_total DECIMAL(12,2) AS (unit_price * quantity) STORED");
        }

        // FK restore omitted (down() for FKs is complex; leave as-is)
    }

    protected function readdForeignKeys(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Helper: drop existing FK (by conventional name) then re-add with onDelete.
        $specs = [
            // table, column, refTable, refColumn, onDelete
            ['users', 'role_id', 'roles', 'role_id', 'restrict'],
            ['addresses', 'user_id', 'users', 'user_id', 'cascade'],
            ['products', 'category_id', 'categories', 'category_id', 'restrict'],
            ['products', 'user_id', 'users', 'user_id', 'set null'],
            ['carts', 'user_id', 'users', 'user_id', 'cascade'],
            ['orders', 'customer_id', 'users', 'user_id', 'restrict'],
            ['orders', 'address_id', 'addresses', 'address_id', 'set null'],
            ['order_items', 'order_id', 'orders', 'order_id', 'cascade'],
            ['order_items', 'product_id', 'products', 'product_id', 'set null'],
            ['cart_items', 'cart_id', 'carts', 'cart_id', 'cascade'],
            ['cart_items', 'product_id', 'products', 'product_id', 'restrict'],
            ['payments', 'order_id', 'orders', 'order_id', 'cascade'],
            ['shipments', 'order_id', 'orders', 'order_id', 'cascade'],
            ['conversations', 'customer_id', 'users', 'user_id', 'restrict'],
            ['conversations', 'admin_id', 'users', 'user_id', 'restrict'],
            ['conversations', 'order_id', 'orders', 'order_id', 'set null'],
            ['conversations', 'product_id', 'products', 'product_id', 'set null'],
            ['chat_messages', 'sender_id', 'users', 'user_id', 'restrict'],
            ['chat_messages', 'conversation_id', 'conversations', 'conversation_id', 'cascade'],
            ['order_status_histories', 'order_id', 'orders', 'order_id', 'cascade'],
            ['order_status_histories', 'user_id', 'users', 'user_id', 'set null'],
            ['payment_webhook_events', 'payment_id', 'payments', 'payment_id', 'set null'],
            ['shipment_tracking_events', 'shipment_id', 'shipments', 'shipment_id', 'cascade'],
            ['product_images', 'product_id', 'products', 'product_id', 'cascade'],
        ];

        foreach ($specs as [$table, $column, $refTable, $refColumn, $onDelete]) {
            $fkName = "{$table}_{$column}_foreign";
            // Drop if exists (ignore failure)
            try {
                DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$fkName}");
            } catch (\Throwable $e) {
                // ignore
            }
            // Re-add
            $action = match ($onDelete) {
                'cascade' => 'ON DELETE CASCADE',
                'restrict' => 'ON DELETE RESTRICT',
                'set null' => 'ON DELETE SET NULL',
                default => '',
            };
            $sql = "ALTER TABLE {$table} ADD CONSTRAINT {$fkName} FOREIGN KEY ({$column}) REFERENCES {$refTable}({$refColumn}) {$action}";
            DB::statement($sql);
        }
    }
};
