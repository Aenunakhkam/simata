<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Siswa Kelas {{ $classroom->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-box p {
            margin: 0;
            padding: 0;
        }
        .signature-space {
            height: 80px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>DAFTAR ANGGOTA SISWA</h1>
        <p>KELAS {{ $classroom->name }} - TINGKAT {{ $classroom->level }} ({{ $classroom->major->name }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">NISN</th>
                <th width="20%">NIS</th>
                <th width="40%">Nama Lengkap</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $student->nisn }}</td>
                    <td class="text-center">{{ $student->nis ?? '-' }}</td>
                    <td>{{ $student->name }}</td>
                    <td class="text-center">
                        @if($student->status === 'active')
                            Aktif
                        @elseif($student->status === 'graduated')
                            Lulus
                        @else
                            Keluar/Pindah
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data siswa untuk kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p>Wali Kelas / Pengurus</p>
            <div class="signature-space"></div>
            <p>_______________________</p>
        </div>
    </div>

</body>
</html>
