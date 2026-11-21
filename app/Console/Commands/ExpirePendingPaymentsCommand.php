<?php

namespace App\Console\Commands;

use App\Jobs\ExpirePendingPayments;
use Illuminate\Console\Command;

class ExpirePendingPaymentsCommand extends Command
{
    protected $signature = 'payments:expire-pending';

    protected $description = 'Expire pending payments past their expiry date';

    public function handle(): int
    {
        ExpirePendingPayments::dispatchSync();

        $this->info('Expired pending payments processed.');

        return self::SUCCESS;
    }
}
