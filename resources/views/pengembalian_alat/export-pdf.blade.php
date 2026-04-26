<h2>Data Pengembalian Alat</h2>

<table border="1" width="100%">
    <tr>
        <th>No</th>
        <th>Alat</th>
        <th>User</th>
        <th>Jumlah</th>
        <th>Tanggal</th>
    </tr>

    @foreach ($data as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->pengambilan->alat->nama_alat }}</td>
            <td>{{ $d->user->name }}</td>
            <td>{{ $d->jumlah }}</td>
            <td>{{ $d->tanggal_pengembalian }}</td>
        </tr>
    @endforeach

</table>
