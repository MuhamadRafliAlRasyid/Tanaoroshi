<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Detail Permintaan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 60px;
            float: left;
        }

        .header .info {
            text-align: right;
            margin-left: 70px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .ttd {
            margin-top: 50px;
            text-align: center;
        }

        .ttd div {
            display: inline-block;
            width: 40%;
        }

        .ttd .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('assets/logos.png') }}" alt="Logo">
        <div class="info">
            <strong>DISPERINDAG KAB. KARAWANG</strong><br>
            Jl. Jend. A. Yani No.10, Karangpawitan<br>
            Kec. Karawang Barat, Karawang, Jawa Barat 41315<br>
            Telp. (0267) 845 0633
        </div>
    </div>

    <hr>

    <h3 style="text-align:center;">Detail Permintaan Barang</h3>

    <p><strong>Nama Pengaju:</strong> {{ $permintaan->user->name }}</p>
    <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($permintaan->created_at)->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tanggal Permintaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permintaan->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ $detail->satuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($detail->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $detail->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <div>
            Mengetahui,<br><br>
            @if ($permintaan->mengetahui != null)
                <img src="{{ public_path('assets/ok.png') }}" style="width: 80px; margin-bottom: -20px;" alt="Approved">
            @endif
            <br>
            <div class="name">
                {{ $permintaan->mengetahuiUser->name ?? '................................' }}
            </div>

        </div>
        <div>
            Disetujui,<br><br>
            @if ($permintaan->approval != null)
                <img src="{{ public_path('assets/ok.png') }}" style="width: 80px; margin-bottom: -20px;"
                    alt="Approved">
            @endif <br>
            <div class="name">
                {{ $permintaan->approvalUser->name ?? '................................' }}
            </div>
        </div>
    </div>



</body>

</html>
