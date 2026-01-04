<?php
include 'koneksi.php';

$menu = $_GET['menu'] ?? 'home';
?>

<!DOCTYPE html>
<html>
<head>
<title>CMS Gudang</title>
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
    Sistem Manajemen Gudang adalah aplikasi berbasis web
    yang digunakan untuk mengelola proses pergudangan
    seperti barang masuk, barang keluar, stok opname,
    dan pembuatan laporan.
  </p>

  <p>
    Sistem ini membantu admin, staf, dan owner
    dalam memantau stok barang secara efektif
    dan terstruktur.
  </p>
</section>
<?php
}

elseif($menu == 'profil'){
?>
<section>
  <h2>Profil Sistem Manajemen Gudang</h2>
  <p>
    Sistem Manajemen Gudang adalah aplikasi berbasis web
    yang digunakan untuk mengelola proses pergudangan
    seperti barang masuk, barang keluar, stok opname,
    dan pembuatan laporan.
  </p>

  <p>
    Sistem ini membantu admin, staf, dan owner
    dalam memantau stok barang secara efektif
    dan terstruktur.
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
