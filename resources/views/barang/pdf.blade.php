<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Daftar Barang</title>
    <style type="text/css">
        * {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        body {
            font-size: 10px;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }

        .subtitle {
            font-size: 10px;
            color: #666666;
            margin-bottom: 12px;
        }

        table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #b1babf;
            padding: 4px 6px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        thead th {
            background-color: #e6e6e6;
            font-size: 10px;
            text-align: center;
        }

        td.center {
            text-align: center;
        }

        td.right {
            text-align: right;
        }

        tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }
    </style>
</head>

<body>
    <h2>Daftar Barang</h2>
    <div class="subtitle">Dicetak pada {{ $generatedAt->translatedFormat('d F Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 40px;">Id</th>
                <th>Nama Barang</th>
                <th style="width: 90px;">Merk</th>
                <th style="width: 50px;">Satuan</th>
                <th style="width: 60px;">Warna</th>
                <th style="width: 60px;">Berat</th>
                <th style="width: 70px;">Ukuran</th>
                <th style="width: 100px;">Price List</th>
                <th style="width: 80px;">SN</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $barang)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $barang->id }}</td>
                    <td>{{ $barang->nama_product }}</td>
                    <td>{{ $barang->merk }}</td>
                    <td>{{ $barang->satuan }}</td>
                    <td>{{ $barang->warna }}</td>
                    <td>{{ $barang->berat }}</td>
                    <td>{{ $barang->ukuran }}</td>
                    <td class="right">Rp {{ number_format((float) $barang->harga, 0, ',', '.') }}</td>
                    <td class="center">{{ $barang->wajib_serial_number ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $barang->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td class="center" colspan="11">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
