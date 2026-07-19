<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #1F2937; line-height: 1.6;">
    <p>Halo <strong>{{ $participantName }}</strong>,</p>

    <p>
        Terima kasih atas kehadiran Anda pada acara
        <strong>{{ $eventTitle }}</strong>.
    </p>

    <p>
        Sertifikat kehadiran Anda sudah kami lampirkan dalam email ini
        dalam format PDF. Silakan unduh dan simpan sebagai bukti partisipasi Anda.
    </p>

    <p>Sampai jumpa di acara kami selanjutnya!</p>

    <p>
        Salam,<br>
        Tim {{ config('app.name') }}
    </p>
</body>
</html>