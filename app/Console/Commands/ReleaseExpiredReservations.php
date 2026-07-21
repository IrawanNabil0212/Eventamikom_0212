<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'tickets:release-expired';

    protected $description = 'Lepas reservasi tiket yang sudah kadaluarsa (belum dibayar dalam 15 menit) dan kembalikan stoknya';

    public function handle(): int
    {
        $expiredTransactions = Transaction::where('status', 'Pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredTransactions->isEmpty()) {
            $this->info('Tidak ada reservasi yang kadaluarsa.');
            return self::SUCCESS;
        }

        foreach ($expiredTransactions as $transaction) {
            DB::transaction(function () use ($transaction) {
                $transaction->update(['status' => 'expired']);

                if ($transaction->event) {
                    $transaction->event()->increment('stock');
                }
            });

            $this->info("Reservasi {$transaction->order_id} dilepas, stok event #{$transaction->event_id} dikembalikan.");
        }

        $this->info("Total {$expiredTransactions->count()} reservasi dilepas.");

        return self::SUCCESS;
    }
}