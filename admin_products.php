<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
};

// ============================================================
// TAMBAH PRODUK
// ============================================================
if(isset($_POST['add_product'])){

   $name     = mysqli_real_escape_string($conn, $_POST['name']);
   $price    = mysqli_real_escape_string($conn, $_POST['price']);
   $stock    = intval($_POST['stock']);
   $category = mysqli_real_escape_string($conn, $_POST['category']);

   $image          = $_FILES['image']['name'];
   $image_size     = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder   = 'uploaded_img/' . $image;

   $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'Nama produk sudah ditambahkan';
   } elseif($image_size > 2000000){
      $message[] = 'Ukuran gambar terlalu besar (maks. 2 MB)';
   } else {
      $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name, price, stock, category, image) VALUES('$name', '$price', '$stock', '$category', '$image')") or die('query failed');
      if($add_product_query){
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'Produk berhasil ditambahkan!';
      } else {
         $message[] = 'Produk gagal ditambahkan!';
      }
   }
}

// ============================================================
// HAPUS PRODUK — cek stok dulu, tolak jika stok masih ada
// ============================================================
if(isset($_GET['delete'])){
   $delete_id = intval($_GET['delete']);

   $check_query  = mysqli_query($conn, "SELECT image, stock, name FROM `products` WHERE id = '$delete_id'") or die('query failed');
   $fetch_product = mysqli_fetch_assoc($check_query);

   if($fetch_product){
      $current_stock = (int)$fetch_product['stock'];

      if($current_stock > 0){
         // Stok masih ada — blokir penghapusan
         $message[] = 'BLOCKED:Produk "' . htmlspecialchars($fetch_product['name']) . '" tidak dapat dihapus karena masih memiliki stok ' . $current_stock . ' pcs. Kosongkan stok terlebih dahulu.';
      } else {
         // Stok 0 — boleh dihapus
         if(file_exists('uploaded_img/' . $fetch_product['image'])){
            unlink('uploaded_img/' . $fetch_product['image']);
         }
         mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
         header('location:admin_products.php');
         exit;
      }
   }
}

// ============================================================
// UPDATE PRODUK
// ============================================================
if(isset($_POST['update_product'])){

   $update_p_id      = intval($_POST['update_p_id']);
   $update_name      = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_price     = mysqli_real_escape_string($conn, $_POST['update_price']);
   $update_stock     = intval($_POST['update_stock']);
   $update_category  = mysqli_real_escape_string($conn, $_POST['update_category']);
   $update_old_image = mysqli_real_escape_string($conn, $_POST['update_old_image']);

   $update_image          = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size     = $_FILES['update_image']['size'];
   $update_folder         = 'uploaded_img/' . $update_image;

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'Ukuran file gambar terlalu besar (maks. 2 MB)';
         $_GET['update'] = $update_p_id;
      } else {
         mysqli_query($conn, "UPDATE `products` SET name='$update_name', price='$update_price', stock='$update_stock', category='$update_category', image='$update_image' WHERE id='$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         if(!empty($update_old_image) && $update_old_image !== $update_image && file_exists('uploaded_img/' . $update_old_image)){
            unlink('uploaded_img/' . $update_old_image);
         }
         header('location:admin_products.php');
         exit;
      }
   } else {
      mysqli_query($conn, "UPDATE `products` SET name='$update_name', price='$update_price', stock='$update_stock', category='$update_category' WHERE id='$update_p_id'") or die('query failed');
      header('location:admin_products.php');
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
   <title>Manajemen Produk - Admin</title>

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

      .add-products { padding: 2.5rem 3rem; display: flex; justify-content: center; }

      .form-card {
         max-width: 56rem; width: 100%;
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
      .input-group .input-icon {
         position: absolute; left: 1.6rem; top: 50%;
         transform: translateY(-50%);
         color: var(--gold-500); font-size: 1.5rem; pointer-events: none;
      }
      .form-card .box {
         width: 100%; background: var(--gold-50);
         border: 1.5px solid var(--border); border-radius: .9rem;
         padding: 1.2rem 1.5rem 1.2rem 4.2rem;
         font-size: 1.5rem; font-family: 'Poppins', sans-serif; color: var(--dark);
         transition: border-color .25s, background .25s; outline: none;
      }
      .form-card .box:focus { border-color: var(--gold-500); background: #fff; }
      .form-card .box::placeholder { color: #B0A080; }
      .form-card input[type="file"].box { padding-left: 1.5rem; cursor: pointer; }
      .form-card select.box {
         appearance: none; -webkit-appearance: none; cursor: pointer;
         background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23D4A017' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
         background-repeat: no-repeat; background-position: right 1.5rem center;
         background-color: var(--gold-50); padding-right: 3.5rem;
      }

      .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }

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

      .show-products { padding: 0 3rem 4rem; }
      .box-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(27rem, 1fr)); gap: 2.5rem; }

      .product-card {
         background: var(--surface); border-radius: 1.8rem;
         border: 1px solid var(--border); box-shadow: var(--shadow);
         overflow: hidden; transition: transform .25s, box-shadow .25s;
         display: flex; flex-direction: column;
      }
      .product-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(180,140,20,.15); }

      .product-card .img-wrap {
         background: var(--gold-50); height: 22rem;
         display: flex; align-items: center; justify-content: center;
         border-bottom: 1px solid var(--border); overflow: hidden; position: relative;
      }
      .product-card .img-wrap .category-badge {
         position: absolute; top: 1rem; left: 1rem;
         background: var(--gold-500); color: #fff;
         font-size: 1.1rem; font-weight: 600;
         padding: .3rem .9rem; border-radius: 2rem;
         box-shadow: 0 2px 8px rgba(180,140,20,.25);
      }
      .product-card .img-wrap .stock-badge {
         position: absolute; top: 1rem; right: 1rem;
         font-size: 1.1rem; font-weight: 600;
         padding: .3rem .9rem; border-radius: 2rem;
         box-shadow: 0 2px 8px rgba(0,0,0,.10);
      }
      .stock-badge.in-stock  { background: #d1fae5; color: #065f46; }
      .stock-badge.low-stock { background: #fef3c7; color: #92400e; }
      .stock-badge.out-stock { background: #fee2e2; color: #991b1b; }

      .product-card .img-wrap img {
         width: 100%; height: 100%; object-fit: contain;
         padding: 1rem; transition: transform .4s;
      }
      .product-card:hover .img-wrap img { transform: scale(1.05); }

      .product-card .card-body {
         padding: 1.8rem 2rem; flex: 1;
         display: flex; flex-direction: column; gap: .6rem;
      }
      .product-card .name { font-size: 1.6rem; font-weight: 600; color: var(--dark); line-height: 1.4; }
      .product-card .price {
         font-size: 2rem; font-weight: 700; color: var(--gold-600);
         display: flex; align-items: baseline; gap: .3rem;
      }
      .product-card .price span { font-size: 1.3rem; font-weight: 500; color: var(--gold-500); }
      .product-card .meta-row {
         display: flex; align-items: center; gap: .6rem;
         font-size: 1.3rem; color: #7A6A40;
      }
      .product-card .meta-row i { color: var(--gold-500); font-size: 1.2rem; }

      .card-actions { padding: 0 2rem 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }

      .btn-edit, .btn-delete {
         padding: 1rem; border-radius: .8rem;
         font-size: 1.4rem; font-family: 'Poppins', sans-serif; font-weight: 500;
         text-decoration: none; text-align: center; cursor: pointer;
         transition: .2s; display: flex; align-items: center; justify-content: center; gap: .5rem;
         border: 1.5px solid;
      }
      .btn-edit { background: var(--gold-100); color: var(--gold-800); border-color: var(--gold-200); }
      .btn-edit:hover { background: var(--gold-200); border-color: var(--gold-400); }
      .btn-delete { background: #fff5f5; color: #b91c1c; border-color: #fecaca; }
      .btn-delete:hover { background: #fee2e2; border-color: #f87171; }

      /* Tombol hapus nonaktif saat stok masih ada */
      .btn-delete-disabled {
         padding: 1rem; border-radius: .8rem;
         font-size: 1.4rem; font-family: 'Poppins', sans-serif; font-weight: 500;
         text-align: center;
         display: flex; align-items: center; justify-content: center; gap: .5rem;
         border: 1.5px solid #e5e7eb;
         background: #f3f4f6; color: #9ca3af;
         cursor: not-allowed; user-select: none;
      }

      .empty {
         grid-column: 1 / -1; text-align: center; padding: 6rem 2rem;
         background: var(--surface); border-radius: 1.8rem;
         border: 1.5px dashed var(--gold-200);
      }
      .empty .empty-icon { font-size: 5rem; color: var(--gold-200); margin-bottom: 1.5rem; }
      .empty p { font-size: 1.8rem; color: var(--gold-600); font-weight: 500; }
      .empty small { font-size: 1.4rem; color: #B0A080; }

      .edit-product-form {
         position: fixed; inset: 0; z-index: 1100;
         background: rgba(30, 20, 5, 0.65);
         display: flex; align-items: center; justify-content: center;
         padding: 2rem; backdrop-filter: blur(3px);
      }
      .edit-form-inner {
         width: 52rem; max-width: 100%; max-height: 92vh; overflow-y: auto;
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
         position: sticky; top: 0; z-index: 1;
      }
      .edit-form-header h3 {
         font-size: 1.8rem; font-weight: 700; color: #fff;
         display: flex; align-items: center; gap: .7rem;
      }
      .edit-form-body { padding: 2.5rem; }
      .edit-form-body .img-preview-wrap {
         text-align: center; margin-bottom: 2rem;
         background: var(--gold-50); border-radius: 1rem;
         border: 1px solid var(--border); padding: 1.5rem;
      }
      .edit-form-body .img-preview-wrap img {
         height: 18rem; max-width: 100%; object-fit: contain; border-radius: .8rem;
      }
      .btn-cancel {
         width: 100%; margin-top: .8rem; padding: 1.2rem;
         border: 1.5px solid var(--border); border-radius: .9rem;
         background: transparent; color: var(--gold-700);
         font-size: 1.5rem; font-family: 'Poppins', sans-serif; font-weight: 500;
         cursor: pointer; transition: .2s;
      }
      .btn-cancel:hover { background: var(--gold-50); border-color: var(--gold-400); }

      .divider { height: 1px; background: var(--border); margin: 0 3rem 2.5rem; }
      .edit-form-body .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- Page Header -->
<div class="page-header">
   <div class="icon-wrap"><i class="fas fa-store"></i></div>
   <div>
      <h1>Manajemen Produk</h1>
      <span>Kelola semua produk toko Anda</span>
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

<!-- Add Product Form -->
<section class="add-products">
   <div class="form-card">
      <h3><i class="fas fa-plus"></i> Tambah Produk Baru</h3>
      <form action="" method="post" enctype="multipart/form-data">
         <div class="input-group">
            <input type="text" name="name" class="box" placeholder="Masukan judul buku" required>
         </div>
         <div class="input-row">
            <div class="input-group" style="margin-bottom:0">
               <input type="number" min="0" name="price" class="box" placeholder="Harga (Rp)" required>
            </div>
            <div class="input-group" style="margin-bottom:0">
               <input type="number" min="0" name="stock" class="box" placeholder="Jumlah stok" required>
            </div>
         </div>
         <div class="input-group" style="margin-top:1.4rem">
            <select name="category" class="box" required>
               <option value="" disabled selected>-- Pilih Kategori --</option>
               <option value="Novel">Novel</option>
               <option value="Pendidikan">Pendidikan</option>
               <option value="Biografi">Romance</option>
               <option value="Sejarah">Sejarah</option>
               <option value="Lainnya">Lainnya</option>
            </select>
         </div>
         <div class="input-group">
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
         </div>
         <button type="submit" name="add_product" class="btn-gold">
            <i class="fas fa-save"></i> Simpan Produk
         </button>
      </form>
   </div>
</section>

<div class="divider"></div>

<?php
   $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
   $total = mysqli_num_rows($select_products);
?>
<div class="section-label">
   <h2><i class="fas fa-boxes" style="color:var(--gold-400);margin-right:.5rem;"></i> Daftar Produk</h2>
   <span class="count-badge"><?php echo $total; ?> produk</span>
</div>

<section class="show-products">
   <div class="box-container">
      <?php
         if($total > 0){
            mysqli_data_seek($select_products, 0);
            while($fetch_products = mysqli_fetch_assoc($select_products)){

               $stock_val = isset($fetch_products['stock']) ? (int)$fetch_products['stock'] : 0;
               if($stock_val <= 0){
                  $stock_class = 'out-stock';
                  $stock_label = 'Habis';
               } elseif($stock_val <= 5){
                  $stock_class = 'low-stock';
                  $stock_label = 'Sisa '.$stock_val;
               } else {
                  $stock_class = 'in-stock';
                  $stock_label = 'Stok '.$stock_val;
               }

               $category_val = isset($fetch_products['category']) && !empty($fetch_products['category'])
                               ? htmlspecialchars($fetch_products['category']) : '';
      ?>
      <div class="product-card">
         <div class="img-wrap">
            <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="<?php echo htmlspecialchars($fetch_products['name']); ?>">
            <?php if($category_val): ?>
            <span class="category-badge"><i class="fas fa-tag" style="margin-right:.3rem;font-size:.9rem;"></i><?php echo $category_val; ?></span>
            <?php endif; ?>
            <span class="stock-badge <?php echo $stock_class; ?>">
               <i class="fas fa-cubes" style="margin-right:.3rem;font-size:.9rem;"></i><?php echo $stock_label; ?>
            </span>
         </div>
         <div class="card-body">
            <div class="name"><?php echo htmlspecialchars($fetch_products['name']); ?></div>
            <div class="price">
               <span>Rp</span><?php echo number_format($fetch_products['price'], 0, ',', '.'); ?>
            </div>
            <div class="meta-row">
               <i class="fas fa-layer-group"></i>
               <?php echo $category_val ? $category_val : '<span style="color:#C0B090;font-style:italic;">Tanpa kategori</span>'; ?>
               &nbsp;·&nbsp;
               <i class="fas fa-boxes"></i>
               <?php echo $stock_val; ?> pcs
            </div>
         </div>
         <div class="card-actions">
            <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="btn-edit">
               <i class="fas fa-pen"></i> Update
            </a>

            <?php if($stock_val > 0): ?>
            <span class="btn-delete-disabled" title="Kosongkan stok terlebih dahulu sebelum menghapus">
               <i class="fas fa-lock"></i> Hapus
            </span>
            <?php else: ?>
            <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="btn-delete"
               onclick="return confirm('Stok produk ini sudah 0. Hapus produk ini?');">
               <i class="fas fa-trash"></i> Hapus
            </a>
            <?php endif; ?>

         </div>
      </div>
      <?php
            }
         }else{
      ?>
      <div class="empty">
         <div class="empty-icon"><i class="fas fa-box-open"></i></div>
         <p>Belum ada produk</p>
         <small>Tambahkan produk pertama Anda di atas</small>
      </div>
      <?php } ?>
   </div>
</section>

<?php if(isset($_GET['update'])): ?>
<?php
   $update_id    = intval($_GET['update']);
   $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
   if(mysqli_num_rows($update_query) > 0):
      $fetch_update  = mysqli_fetch_assoc($update_query);
      $edit_stock    = isset($fetch_update['stock']) ? $fetch_update['stock'] : 0;
      $edit_category = isset($fetch_update['category']) ? $fetch_update['category'] : '';
      $categories    = ['Novel','Pendidikan','Romance','Sejarah','Fiksi','Lainnya'];
?>
<section class="edit-product-form">
   <div class="edit-form-inner">
      <div class="edit-form-header">
         <h3><i class="fas fa-pen-to-square"></i> Edit Produk</h3>
      </div>
      <div class="edit-form-body">
         <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
            <input type="hidden" name="update_old_image" value="<?php echo htmlspecialchars($fetch_update['image']); ?>">
            <div class="img-preview-wrap">
               <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="Preview">
            </div>
            <div class="input-group">
               <input type="text" name="update_name" value="<?php echo htmlspecialchars($fetch_update['name']); ?>" class="box" required placeholder="Nama produk">
            </div>
            <div class="input-row">
               <div class="input-group" style="margin-bottom:0">
                  <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Harga">
               </div>
               <div class="input-group" style="margin-bottom:0">
                  <input type="number" name="update_stock" value="<?php echo $edit_stock; ?>" min="0" class="box" required placeholder="Stok">
               </div>
            </div>
            <div class="input-group" style="margin-top:1.4rem">
               <select name="update_category" class="box" required>
                  <option value="" disabled <?php echo empty($edit_category) ? 'selected' : ''; ?>>-- Pilih Kategori --</option>
                  <?php foreach($categories as $cat): ?>
                  <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($edit_category === $cat) ? 'selected' : ''; ?>>
                     <?php echo htmlspecialchars($cat); ?>
                  </option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div class="input-group">
               <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
            </div>
            <button type="submit" name="update_product" class="btn-gold">
               <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <button type="button" class="btn-cancel" onclick="window.location.href='admin_products.php'">
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