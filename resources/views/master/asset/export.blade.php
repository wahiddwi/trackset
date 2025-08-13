<table>
  <thead>
  <tr>
      <th>Kategori</th>
      <th>Cabang</th>
      <th>Lokasi</th>
      <th>Nama Barang</th>
      <th>Short Name</th>
      <th>Tipe PIC</th>
      <th>PIC</th>
      <th>Tgl Perolehan</th>
      <th>Harga</th>
      <th>Keterangan</th>
      <th>Serial Number</th>
      <th>Dok Referensi</th>
      <th>Brand</th>
      <th>Tag</th>
  </tr>
  </thead>
  <tbody>
  @foreach($assets as $asset)
      <tr>
          <td>{{ $asset['kategori'] }}</td>
          <td>{{ $asset['cabang'] }}</td>
          <td>{{ $asset['lokasi'] }}</td>
          <td>{{ $asset['nama_barang'] }}</td>
          <td>{{ $asset['short_name'] }}</td>
          <td>{{ $asset['tipe_pic'] }}</td>
          <td>{{ $asset['pic'] }}</td>
          <td>{{ $asset['tgl_perolehan'] }}</td>
          <td>{{ $asset['harga'] }}</td>
          <td>{{ $asset['keterangan'] }}</td>
          <td>{{ $asset['serial_number'] }}</td>
          <td>{{ $asset['dok_referensi'] }}</td>
          <td>{{ $asset['brand'] }}</td>
          <td>{{ $asset['tag'] }}</td>
          <td>{{ $asset['kode'] }}</td>
          <td>{{ $asset['id'] }}</td>
      </tr>
  @endforeach
  </tbody>
</table>
