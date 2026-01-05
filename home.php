<?php
include 'koneksi.php';

$menu = $_GET['menu'] ?? 'home';
?>

<!DOCTYPE html>
<html>
<head>
  <title>CMS Gudang</title>

  <!-- WAJIB UNTUK RESPONSIVE -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
$web = mysqli_fetch_assoc(
  mysqli_query($conn,"SELECT * FROM cms_web_info LIMIT 1")
);
?>

<header>
  <img src="gambar/<?= $web['logo']; ?>" alt="Logo">

  <div class="header-text">
    <h1><?= $web['nama_web']; ?></h1>
    <p><?= $web['slogan']; ?></p>
    <small><?= $web['lokasi']; ?></small>
  </div>
</header>


<div class="main">

<!-- NAV -->
<nav>
<?php
$menuDB = mysqli_query($conn,"SELECT * FROM cms_menu");
while($m = mysqli_fetch_assoc($menuDB)){
  echo "<a href='{$m['url']}'>{$m['nama_menu']}</a>";
}
?>
</nav>

<!-- ARTICLE -->
<article>

<?php
/* =========================
   HOME
========================= */
if($menu == 'home'){
  $q = mysqli_query($conn,"SELECT * FROM cms_artikel ORDER BY tanggal DESC");
  while($a = mysqli_fetch_assoc($q)){
?>
<section>
  <h3>
    <a href="home.php?menu=detail&id=<?= $a['id']; ?>">
      <?= $a['judul']; ?>
    </a>
  </h3>

  <p><?= $a['ringkasan']; ?></p>

  <!-- BUTTON TEMPLATE -->
  <a href="home.php?menu=detail&id=<?= $a['id']; ?>" class="btn">
    Lihat Detail
  </a>
</section>

<hr>
<?php } }

/* =========================
   PROFIL SISTEM
========================= */
elseif($menu == 'sistem'){
?>
<section>
  <h2>Profil Sistem Manajemen Gudang</h2>

  <p>
    Sistem Manajemen Gudang Roti merupakan sebuah aplikasi berbasis web yang
    dirancang untuk mendukung proses pengelolaan persediaan gudang pada
    perusahaan manufaktur roti secara terintegrasi, sistematis, dan terdokumentasi
    dengan baik, mulai dari pencatatan penerimaan bahan baku, pengeluaran bahan
    untuk proses produksi, penerimaan barang jadi hasil produksi, hingga penyajian
    laporan persediaan yang akurat dan dapat digunakan sebagai dasar pengambilan
    keputusan manajerial.
  </p>

  <p>
    Sistem ini dikembangkan sebagai solusi atas permasalahan pencatatan manual
    yang berpotensi menimbulkan kesalahan perhitungan stok, keterlambatan
    informasi, serta kurangnya transparansi data antara pihak gudang, produksi,
    dan manajemen, sehingga seluruh aktivitas pergudangan dapat dipantau secara
    real-time dan terkontrol dengan lebih baik.
  </p>

  <p>
    Secara fungsional, Sistem Manajemen Gudang Roti memungkinkan pengguna
    untuk mencatat transaksi barang masuk dan barang keluar yang secara otomatis
    memperbarui jumlah stok di dalam sistem, melakukan proses stok opname untuk
    mencocokkan data sistem dengan kondisi fisik barang di gudang, serta
    menghasilkan laporan stok dan transaksi dalam format digital yang mudah
    dipahami dan diarsipkan.
  </p>

  <p>
    Sistem ini menerapkan mekanisme autentikasi dan hak akses berbasis peran
    (role-based access control), dimana Admin Gudang memiliki wewenang penuh
    dalam mengelola data dan laporan, Staf Gudang bertanggung jawab terhadap
    aktivitas operasional pencatatan barang, serta Owner atau Manajer memiliki
    akses untuk memantau laporan dan kondisi gudang secara keseluruhan tanpa
    dapat mengubah data.
  </p>

  <p>
    Dengan diterapkannya Sistem Manajemen Gudang Roti ini, perusahaan
    diharapkan mampu meningkatkan efisiensi operasional gudang, meminimalkan
    risiko kesalahan pencatatan, mempercepat proses pelaporan, serta menciptakan
    transparansi dan pengendalian stok yang lebih baik guna mendukung kelancaran
    proses produksi dan pengambilan keputusan bisnis.
  </p>
</section>
<?php
}

elseif($menu == 'profil'){
?>
<section>
  <h2>Profil Sistem Manajemen Gudang</h2>

  <p>
    Sistem Manajemen Gudang Roti merupakan aplikasi berbasis web yang
    digunakan untuk mengelola seluruh aktivitas pergudangan pada perusahaan
    manufaktur roti, mulai dari pengelolaan data barang, pencatatan transaksi
    barang masuk dan barang keluar, proses stok opname, hingga penyusunan
    laporan persediaan secara terstruktur dan akurat.
  </p>

  <p>
    Sistem ini dirancang untuk membantu Admin Gudang, Staf Gudang, dan
    Owner atau Manajer dalam memantau ketersediaan stok bahan baku maupun
    barang jadi secara efektif, mengurangi ketergantungan pada pencatatan
    manual, serta meningkatkan akurasi data sebagai dasar pengambilan
    keputusan operasional dan manajerial.
  </p>
</section>
<?php
}

/* =========================
   ARTIKEL (SEMUA ARTIKEL)
========================= */
elseif($menu == 'artikel'){
  $q = mysqli_query($conn,"SELECT * FROM cms_artikel ORDER BY tanggal DESC");
  while($a = mysqli_fetch_assoc($q)){
?>
<section>
  <h3>
    <a href="home.php?menu=detail&id=<?= $a['id']; ?>">
      <?= $a['judul']; ?>
    </a>
  </h3>
  <p><?= $a['ringkasan']; ?></p>

  <a href="home.php?menu=detail&id=<?= $a['id']; ?>" class="btn">
    Baca Artikel
  </a>
</section>
<hr>
<?php } }


/* =========================
   KATEGORI FILTER
========================= */
elseif(in_array($menu,['sistem','modul','diagram','tentang'])){
  $map = [
    'sistem'  => 'Sistem',
    'modul'   => 'Modul',
    'diagram' => 'Diagram',
    'tentang' => 'Informasi'
  ];

  $kat = $map[$menu];
  $q = mysqli_query($conn,"
    SELECT a.* FROM cms_artikel a
    JOIN cms_kategori k ON a.id_kategori=k.id
    WHERE k.nama_kategori='$kat'
  ");

  while($a = mysqli_fetch_assoc($q)){
?>
<section>
  <h3>
    <a href="home.php?menu=detail&id=<?= $a['id']; ?>">
      <?= $a['judul']; ?>
    </a>
  </h3>
  <p><?= $a['ringkasan']; ?></p>
</section>
<hr>
<?php } }

/* =========================
   DETAIL ARTIKEL
========================= */
elseif($menu == 'detail' && isset($_GET['id'])){
  $id = $_GET['id'];
  $a = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM cms_artikel WHERE id=$id")
  );
?>
<section>
  <h2><?= $a['judul']; ?></h2>
  <p><?= $a['isi']; ?></p>

  <?php if($a['gambar']){ ?>
    <img src="gambar/<?= $a['gambar']; ?>">
  <?php } ?>
</section>
<?php } ?>

</article>

<!-- ASIDE -->
<aside>
<h3>Kategori</h3>
<ul>
<?php
$kat = mysqli_query($conn,"SELECT * FROM cms_kategori");
while($k = mysqli_fetch_assoc($kat)){

  $slug = strtolower($k['nama_kategori']);
  $active = ($menu == $slug) ? 'active' : '';

  echo "
    <li>
      <a href='home.php?menu=$slug' class='$active'>
        {$k['nama_kategori']}
      </a>
    </li>
  ";
}
?>
</ul>
</aside>



</div>

<footer>
  <div class="footer-left">
    <?php
    $s = mysqli_query($conn,"SELECT * FROM cms_sosial_media");
    while($sm = mysqli_fetch_assoc($s)){
      echo "<a href='{$sm['url']}'>{$sm['nama_platform']}</a>";
    }
    ?>
  </div>

  <div class="footer-center">
    © 2026 <?= $web['nama_web']; ?>
  </div>

  <div class="footer-right">
    <strong><?= $web['nama_web']; ?></strong>
    <span><?= $web['slogan']; ?></span>
  </div>
</footer>


</body>
</html>
