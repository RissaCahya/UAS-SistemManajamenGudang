<?php
include 'koneksi.php';
$kategori = $_GET['kategori'];
$data = [];

if ($kategori == 'bahan') {
    $sql = mysqli_query($conn, "SELECT idBahan as id, namaBahan as nama, jumlahStok as stok FROM bahan_baku");
} else {
    $sql = mysqli_query($conn, "SELECT idBarangJadi as id, namaRoti as nama, jumlahStok as stok FROM barang_jadi");
}

while($row = mysqli_fetch_assoc($sql)) {
    $data[] = $row;
}
echo json_encode($data);
?>