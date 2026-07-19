<?php

namespace App\Jobs;

use App\Mail\CertificateMail;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function handle(): void
    {
        // Guard: jangan generate ulang / kirim ulang kalau sudah pernah terkirim
        if ($this->transaction->certificate_sent_at !== null) {
            return;
        }

        // Guard: pastikan relasi event ada (nama event dipakai di sertifikat)
        $event = $this->transaction->event;
        if (! $event) {
            Log::warning("GenerateCertificateJob: event tidak ditemukan untuk transaction #{$this->transaction->id}");
            return;
        }

        // 1. Render PDF dari template Blade
        $pdf = Pdf::loadView('certificates.template', [
            'participantName' => $this->transaction->customer_name,
            'eventTitle' => $event->title,
            'eventDate' => $event->date,
        ])->setPaper('a4', 'landscape');

        // 2. Simpan PDF ke storage (disk 'public', folder certificates/)
        $fileName = 'certificates/'.$this->transaction->order_id.'.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        // 3. Update record transaksi
        $this->transaction->update([
            'certificate_path' => $fileName,
        ]);

        // 4. Kirim email dengan PDF terlampir
        Mail::to($this->transaction->customer_email)
            ->send(new CertificateMail($this->transaction, $event));

        // 5. Tandai sudah terkirim (mencegah double-send kalau job di-retry)
        $this->transaction->update([
            'certificate_sent_at' => now(),
        ]);
    }
}