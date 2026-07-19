<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
        }
        .certificate {
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
            border: 12px solid #4338CA;
            padding: 60px;
            text-align: center;
        }
        .label {
            font-size: 16px;
            letter-spacing: 4px;
            color: #6B7280;
            text-transform: uppercase;
            margin-top: 40px;
        }
        .title {
            font-size: 42px;
            font-weight: bold;
            color: #1F2937;
            margin: 10px 0 30px 0;
        }
        .diberikan {
            font-size: 14px;
            color: #6B7280;
        }
        .nama {
            font-size: 36px;
            font-weight: bold;
            color: #4338CA;
            margin: 15px 0 30px 0;
            border-bottom: 2px solid #4338CA;
            display: inline-block;
            padding-bottom: 10px;
        }
        .deskripsi {
            font-size: 16px;
            color: #374151;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .event-name {
            font-weight: bold;
            color: #1F2937;
        }
        .tanggal {
            margin-top: 50px;
            font-size: 14px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="label">Sertifikat Kehadiran</div>
        <div class="title">CERTIFICATE OF ATTENDANCE</div>

        <div class="diberikan">Diberikan kepada</div>
        <div class="nama">{{ $participantName }}</div>

        <div class="deskripsi">
            Atas partisipasinya sebagai peserta dalam acara
            <span class="event-name">{{ $eventTitle }}</span>
            yang diselenggarakan pada tanggal {{ \Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y') }}.
        </div>

        <div class="tanggal">
            Diterbitkan otomatis melalui sistem — {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>
</body>
</html>