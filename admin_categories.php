<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
};

// ============================================================
// TAMBAH KATEGORI
// ============================================================
if(isset($_POST['add_category'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);

   $select_category = mysqli_query($conn, "SELECT name FROM `categories` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_category) > 0){
      $message[] = 'Kategori sudah ada';
   } else {
      $add_query = mysqli_query($conn, "INSERT INTO `categories`(name) VALUES('$name')") or die('query failed');
      if($add_query){
         $message[] = 'Kategori berhasil ditambahkan!';
      } else {
         $message[] = 'Kategori gagal ditambahkan!';
      }
   }
}

// ============================================================
// HAPUS KATEGORI — cek apakah kategori digunakan di produk
// ============================================================
if(isset($_GET['delete'])){
   $delete_id = intval($_GET['delete']);

   $check_query = mysqli_query($conn, "SELECT name FROM `categories` WHERE id = '$delete_id'") or die('query failed');
   $fetch_category = mysqli_fetch_assoc($check_query);

   if($fetch_category){
      // Cek apakah kategori digunakan di produk
      $check_products = mysqli_query($conn, "SELECT COUNT(*) as count FROM `products` WHERE category = '{$fetch_category['name']}'") or die('query failed');
      $product_count = mysqli_fetch_assoc($check_products);

      if($product_count['count'] > 0){
         $message[] = 'BLOCKED:Kategori "' . htmlspecialchars($fetch_category['name']) . '" tidak dapat dihapus karena masih digunakan oleh ' . $product_count['count'] . ' produk.';
      } else {
         mysqli_query($conn, "DELETE FROM `categories` WHERE id = '$delete_id'") or die('query failed');
         header('location:admin_categories.php');
         exit;
      }
   }
}

// ============================================================
// UPDATE KATEGORI
// ============================================================
if(isset($_POST['update_category'])){
   $update_id = intval($_POST['update_id']);
   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $old_name = mysqli_real_escape_string($conn, $_POST['old_name']);

   // Cek apakah nama baru sudah ada
   $check_query = mysqli_query($conn, "SELECT id FROM `categories` WHERE name = '$update_name' AND id != '$update_id'") or die('query failed');

   if(mysqli_num_rows($check_query) > 0){
      $message[] = 'Nama kategori sudah digunakan!';
      $_GET['update'] = $update_id;
   } else {
      // Update kategori dan juga update di produk yang menggunakan kategoris lama
      mysqli_query($conn, "UPDATE `categories` SET name='$update_name' WHERE id='$update_id'") or die('query failed');
      mysqli_query($conn, "UPDATE `products` SET category='$update_name' WHERE category='$old_name'") or die('query failed');
      header('location:admin_categories.php');
      exit;
   }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manajemen Kategori - Admin</title>

   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      :root {
         --gold-50:  #FFFBF0;
         --gold-100: #FFF3CC;
         --gold-200: #FFE58A;
         --gold-400: #F0C040;
         --gold-500: #D4A017;
         --gold-600: #B8860B;
         --gold-700: #9A6F00;
         --gold-800: #7A5500;
         --gold-900: #4A3200;

         --dark:    #1E1A10;
         --surface: #FFFFFF;
         --bg:      #FBF7EE;
         --border:  rgba(180, 140, 20, 0.18);
         --shadow:  0 4px 20px rgba(180, 140, 20, 0.10);
         --radius:  1.2rem;
      }

      * { box-sizing: border-box; margin: 0; padding: 0; }

      body {
         font-family: 'Poppins', sans-serif;
         background-color: var(--bg);
         color: var(--dark);
      }

      .page-header {
         display: flex; align-items: center; gap: 1rem;
         padding: 2.5rem 3rem 0;
      }
      .page-header .icon-wrap {
         width: 4.8rem; height: 4.8rem; border-radius: 1rem;
         background: var(--gold-400);
         display: flex; align-items: center; justify-content: center;
         font-size: 2rem; color: var(--gold-900);
      }
      .page-header h1 { font-size: 2.4rem; font-weight: 700; color: var(--gold-700); letter-spacing: 0.02em; }
      .page-header span { font-size: 1.4rem; color: var(--gold-600); font-weight: 400; }

      /* Alert normal (gold) */
      .alert-box {
         margin: 2rem 3rem 0;
         padding: 1.2rem 1.8rem;
         border-radius: var(--radius);
         background: var(--gold-100);
         border-left: 4px solid var(--gold-500);
         font-size: 1.5rem; color: var(--gold-800);
      }
      /* Alert merah untuk error hapus */
      .alert-box.alert-danger {
         background: #fff5f5;
         border-left-color: #b91c1c;
         color: #b91c1c;
      }

      .add-section { padding: 2.5rem 3rem; display: flex; justify-content: center; }

      .form-card {
         max-width: 42rem; width: 100%;
         background: var(--surface); border-radius: 1.8rem;
         padding: 3rem 3.5rem;
         border: 1px solid var(--border); box-shadow: var(--shadow);
      }
      .form-card h3 {
         font-size: 1.9rem; font-weight: 700; color: var(--gold-700);
         margin-bottom: 2.5rem;
         display: flex; align-items: center; gap: .8rem;
      }
      .form-card h3 i {
         width: 3.4rem; height: 3.4rem; border-radius: .8rem;
         background: var(--gold-100); color: var(--gold-600);
         display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
      }

      .input-group { position: relative; margin-bottom: 1.4rem; }
      .form-card .box {
         width: 100%; background: var(--gold-50);
         border: 1.5px solid var(--border); border-radius: .9rem;
         padding: 1.2rem 1.5rem;
         font-size: 1.5rem; font-family: 'Poppins', sans-serif; color: var(--dark);
         transition: border-color .25s, background .25s; outline: none;
      }
      .form-card .box:focus { border-color: var(--gold-500); background: #fff; }
      .form-card .box::placeholder { color: #B0A080; }

      .btn-gold {
         width: 100%; margin-top: .6rem; padding: 1.3rem;
         border: none; border-radius: .9rem;
         background: var(--gold-500); color: #fff;
         font-size: 1.6rem; font-family: 'Poppins', sans-serif; font-weight: 600;
         letter-spacing: .04em; cursor: pointer;
         transition: background .25s, transform .15s;
         display: flex; align-items: center; justify-content: center; gap: .7rem;
      }
      .btn-gold:hover  { background: var(--gold-600); }
      .btn-gold:active { transform: scale(.98); }

      .section-label { display: flex; align-items: center; gap: 1rem; padding: 0 3rem 2rem; }
      .section-label h2 { font-size: 1.8rem; font-weight: 700; color: var(--gold-700); }
      .section-label .count-badge {
         background: var(--gold-100); color: var(--gold-700);
         font-size: 1.2rem; font-weight: 600;
         padding: .3rem .9rem; border-radius: 2rem; border: 1px solid var(--border);
      }

      .show-section { padding: 0 3rem 4rem; }
      .table-container {
         background: var(--surface); border-radius: 1.8rem;
         border: 1px solid var(--border); box-shadow: var(--shadow);
         overflow: hidden;
      }
      .table-wrapper { overflow-x: auto; }
      .category-table {
         width: 100%; border-collapse: collapse;
         font-size: 1.5rem;
      }
      .category-table thead {
         background: var(--gold-50); border-bottom: 2px solid var(--border);
      }
      .category-table thead th {
         color: var(--gold-700); font-weight: 600;
         padding: 1.5rem 2rem; text-align: left;
         letter-spacing: 0.02em;
      }
      .category-table tbody tr {
         border-bottom: 1px solid var(--border);
         transition: background .2s;
      }
      .category-table tbody tr:hover {
         background: var(--gold-50);
      }
      .category-table tbody td {
         padding: 1.5rem 2rem; color: var(--dark);
      }
      .category-table .category-name {
         font-weight: 500; color: var(--gold-700);
      }
      .category-table .product-count {
         color: #7A6A40; font-size: 1.3rem;
      }
      .action-buttons {
         display: flex; gap: .8rem;
      }
      .btn-edit-small, .btn-delete-small {
         padding: .6rem 1.2rem; border-radius: .6rem;
         font-size: 1.2rem; font-family: 'Poppins', sans-serif; font-weight: 500;
         text-decoration: none; text-align: center; cursor: pointer;
         transition: .2s; display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
         border: 1.5px solid;
      }
      .btn-edit-small { background: var(--gold-100); color: var(--gold-800); border-color: var(--gold-200); }
      .btn-edit-small:hover { background: var(--gold-200); border-color: var(--gold-400); }
      .btn-delete-small { background: #fff5f5; color: #b91c1c; border-color: #fecaca; }
      .btn-delete-small:hover { background: #fee2e2; border-color: #f87171; }

      .empty {
         text-align: center; padding: 4rem 2rem;
         color: var(--gold-600);
      }
      .empty .empty-icon { font-size: 4rem; color: var(--gold-200); margin-bottom: 1rem; }
      .empty p { font-size: 1.6rem; font-weight: 500; }
      .empty small { font-size: 1.3rem; color: #B0A080; }

      .edit-form-modal {
         position: fixed; inset: 0; z-index: 1100;
         background: rgba(30, 20, 5, 0.65);
         display: flex; align-items: center; justify-content: center;
         padding: 2rem; backdrop-filter: blur(3px);
      }
      .edit-form-inner {
         width: 42rem; max-width: 100%;
         background: var(--surface); border-radius: 2rem;
         border: 1px solid var(--border);
         box-shadow: 0 24px 60px rgba(0,0,0,.25);
         animation: popIn .28s cubic-bezier(.22,1,.36,1);
      }
      @keyframes popIn {
         from { transform: scale(.88) translateY(20px); opacity: 0; }
         to   { transform: scale(1) translateY(0); opacity: 1; }
      }
      .edit-form-header {
         background: var(--gold-500); padding: 1.8rem 2.5rem;
         display: flex; align-items: center; justify-content: space-between;
      }
      .edit-form-header h3 {
         font-size: 1.8rem; font-weight: 700; color: #fff;
         display: flex; align-items: center; gap: .7rem;
      }
      .edit-form-body { padding: 2.5rem; }
      .btn-cancel {
         width: 100%; margin-top: .8rem; padding: 1.2rem;
         border: 1.5px solid var(--border); border-radius: .9rem;
         background: transparent; color: var(--gold-700);
         font-size: 1.5rem; font-family: 'Poppins', sans-serif; font-weight: 500;
         cursor: pointer; transition: .2s;
      }
      .btn-cancel:hover { background: var(--gold-50); border-color: var(--gold-400); }

      .divider { height: 1px; background: var(--border); margin: 0 3rem 2.5rem; }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- Page Header -->
<div class="page-header">
   <div class="icon-wrap"><i class="fas fa-tags"></i></div>
   <div>
      <h1>Manajemen Kategori</h1>
      <span>Kelola kategori buku toko Anda</span>
   </div>
</div>

<!-- Alert Messages -->
<?php if(isset($message)): foreach($message as $msg): ?>
<?php
   $is_blocked = (strpos($msg, 'BLOCKED:') === 0);
   $display_msg = $is_blocked ? substr($msg, 8) : $msg;
?>
<div class="alert-box <?php echo $is_blocked ? 'alert-danger' : ''; ?>">
   <i class="fas <?php echo $is_blocked ? 'fa-ban' : 'fa-info-circle'; ?>"></i>
   <?php echo $display_msg; ?>
</div>
<?php endforeach; endif; ?>

<!-- Add Category Form -->
<section class="add-section">
   <div class="form-card">
      <h3><i class="fas fa-plus"></i> Tambah Kategori Baru</h3>
      <form action="" method="post">
         <div class="input-group">
            <input type="text" name="name" class="box" placeholder="Nama kategori" required>
         </div>
         <button type="submit" name="add_category" class="btn-gold">
            <i class="fas fa-save"></i> Simpan Kategori
         </button>
      </form>
   </div>
</section>

<div class="divider"></div>

<?php
   $select_categories = mysqli_query($conn, "SELECT * FROM `categories` ORDER BY name ASC") or die('query failed');
   $total = mysqli_num_rows($select_categories);
?>
<div class="section-label">
   <h2><i class="fas fa-list" style="color:var(--gold-400);margin-right:.5rem;"></i> Daftar Kategori</h2>
   <span class="count-badge"><?php echo $total; ?> kategori</span>
</div>

<section class="show-section">
   <div class="table-container">
      <?php if($total > 0): ?>
      <div class="table-wrapper">
         <table class="category-table">
            <thead>
               <tr>
                  <th>Nama Kategori</th>
                  <th>Jumlah Produk</th>
                  <th style="width:150px;">Aksi</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  mysqli_data_seek($select_categories, 0);
                  while($fetch_category = mysqli_fetch_assoc($select_categories)):
                     $cat_id = $fetch_category['id'];
                     $cat_name = htmlspecialchars($fetch_category['name']);
                     
                     // Hitung jumlah produk dengan kategori ini
                     $product_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM `products` WHERE category = '{$fetch_category['name']}'") or die('query failed');
                     $product_data = mysqli_fetch_assoc($product_count_query);
                     $product_count = $product_data['count'];
               ?>
               <tr>
                  <td class="category-name"><?php echo $cat_name; ?></td>
                  <td class="product-count"><?php echo $product_count; ?> produk</td>
                  <td>
                     <div class="action-buttons">
                        <a href="admin_categories.php?update=<?php echo $cat_id; ?>" class="btn-edit-small">
                           <i class="fas fa-pen"></i> Edit
                        </a>
                        <a href="admin_categories.php?delete=<?php echo $cat_id; ?>" class="btn-delete-small"
                           onclick="return confirm('Hapus kategori <?php echo $cat_name; ?>?');">
                           <i class="fas fa-trash"></i> Hapus
                        </a>
                     </div>
                  </td>
               </tr>
               <?php endwhile; ?>
            </tbody>
         </table>
      </div>
      <?php else: ?>
      <div class="empty">
         <div class="empty-icon"><i class="fas fa-inbox"></i></div>
         <p>Belum ada kategori</p>
         <small>Tambahkan kategori pertama Anda di atas</small>
      </div>
      <?php endif; ?>
   </div>
</section>

<?php if(isset($_GET['update'])): ?>
<?php
   $update_id = intval($_GET['update']);
   $update_query = mysqli_query($conn, "SELECT * FROM `categories` WHERE id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($update_query) > 0):
      $fetch_update = mysqli_fetch_assoc($update_query);
?>
<section class="edit-form-modal">
   <div class="edit-form-inner">
      <div class="edit-form-header">
         <h3><i class="fas fa-pen-to-square"></i> Edit Kategori</h3>
      </div>
      <div class="edit-form-body">
         <form action="" method="post">
            <input type="hidden" name="update_id" value="<?php echo $fetch_update['id']; ?>">
            <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($fetch_update['name']); ?>">
            <div class="input-group">
               <input type="text" name="update_name" value="<?php echo htmlspecialchars($fetch_update['name']); ?>" class="box" required placeholder="Nama kategori">
            </div>
            <button type="submit" name="update_category" class="btn-gold">
               <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <button type="button" class="btn-cancel" onclick="window.location.href='admin_categories.php'">
               <i class="fas fa-times"></i> Batal
            </button>
         </form>
      </div>
   </div>
</section>
<?php endif; ?>
<?php endif; ?>

<script src="js/admin_script.js"></script>

</body>
</html>
