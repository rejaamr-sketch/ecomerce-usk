<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart_numbers) > 0){
      $message[] = 'already added to cart!';
   }else{
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
      $message[] = 'product added to cart!';
   }

};

?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cari Buku | Premium Book Store</title>

   <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* ── DESIGN TOKENS ── */
      :root {
         --gold: #c9a84c;
         --gold-light: #e8c96d;
         --gold-dark: #a07830;
         --gold-dim: rgba(201,168,76,0.12);
         --gold-border: rgba(201,168,76,0.22);
         --gold-border-h: rgba(201,168,76,0.5);
         --bg: #0c0c0c;
         --surf: #141414;
         --surf2: #1c1c1c;
         --text: #f0ebe0;
         --muted: #8a7f6a;

         /* ── UNIFIED SIZE TOKENS ── */
         --page-pad: 48px;          /* horizontal padding seluruh halaman */
         --radius-card: 16px;       /* border-radius semua kartu */
         --radius-btn: 12px;        /* border-radius semua tombol & input */
         --radius-sm: 8px;          /* border-radius elemen kecil */
         --font-xs: 0.75rem;        /* 12px — label kecil */
         --font-sm: 0.85rem;        /* 13.6px — teks sekunder */
         --font-md: 0.9rem;         /* 14.4px — body / input */
         --font-lg: 1rem;           /* 16px — harga / judul kartu */
         --font-xl: 2rem;           /* 32px — judul halaman */
         --ctrl-h: 44px;            /* tinggi semua kontrol: input, tombol, qty */
         --card-img-h: 200px;       /* tinggi gambar semua kartu */
         --card-pad: 16px;          /* padding dalam kartu */
         --gap: 20px;               /* gap grid kartu */
      }

      * { margin: 0; padding: 0; box-sizing: border-box; }

      body {
         font-family: 'Plus Jakarta Sans', sans-serif;
         font-size: var(--font-md);
         background: var(--bg);
         color: var(--text);
         line-height: 1.5;
      }

      /* ── PAGE HEADER ── */
      .page-header {
         background: var(--surf);
         border-bottom: 1px solid var(--gold-border);
         padding: 36px var(--page-pad) 28px;
         position: relative;
         overflow: hidden;
      }

      .page-header::before {
         content: '';
         position: absolute;
         top: 0; left: 0; right: 0; height: 1px;
         background: linear-gradient(90deg, transparent 0%, var(--gold) 30%, var(--gold-light) 50%, var(--gold) 70%, transparent 100%);
         opacity: 0.6;
      }

      .breadcrumb {
         display: flex;
         align-items: center;
         gap: 6px;
         font-size: var(--font-xs);
         color: var(--muted);
         margin-bottom: 12px;
      }

      .breadcrumb a {
         color: var(--gold);
         text-decoration: none;
         display: flex;
         align-items: center;
         gap: 4px;
         transition: color 0.2s;
      }

      .breadcrumb a:hover { color: var(--gold-light); }
      .breadcrumb .sep { font-size: 9px; }

      .page-title {
         font-size: var(--font-xl);
         font-weight: 800;
         letter-spacing: -0.5px;
         color: var(--text);
      }

      .page-title span { color: var(--gold-light); }

      .page-sub {
         margin-top: 6px;
         font-size: var(--font-sm);
         color: var(--muted);
      }

      /* ── NOTIFICATIONS ── */
      .toast-stack {
         position: fixed;
         top: 20px; right: 20px;
         z-index: 9999;
         display: flex;
         flex-direction: column;
         gap: 8px;
      }

      .toast {
         background: var(--surf);
         border: 1px solid rgba(16,185,129,0.3);
         border-left: 3px solid #10b981;
         padding: 12px 16px;
         border-radius: var(--radius-btn);
         display: flex;
         align-items: center;
         gap: 10px;
         font-size: var(--font-sm);
         font-weight: 600;
         color: #34d399;
         min-width: 260px;
         animation: toastIn 0.3s ease forwards;
      }

      .toast.err {
         border-color: rgba(231,76,60,0.3);
         border-left-color: #e74c3c;
         color: #f87171;
      }

      .toast .close-x {
         margin-left: auto;
         cursor: pointer;
         color: var(--muted);
         font-size: var(--font-xs);
         transition: color 0.2s;
      }

      .toast .close-x:hover { color: var(--text); }

      @keyframes toastIn {
         from { opacity: 0; transform: translateX(14px); }
         to   { opacity: 1; transform: translateX(0); }
      }

      /* ── SEARCH AREA ── */
      .search-area {
         padding: 24px var(--page-pad);
         background: var(--surf2);
         border-bottom: 1px solid var(--gold-border);
         display: flex;
         justify-content: center;
      }

      .search-wrap {
         display: flex;
         gap: 10px;
         width: 100%;
         max-width: 680px;
      }

      .search-field {
         flex: 1;
         position: relative;
      }

      .search-field i {
         position: absolute;
         left: 14px; top: 50%;
         transform: translateY(-50%);
         color: var(--gold);
         font-size: 14px;
         pointer-events: none;
      }

      /* input & tombol cari — tinggi seragam --ctrl-h */
      .search-input {
         width: 100%;
         height: var(--ctrl-h);
         padding: 0 16px 0 44px;
         background: var(--surf);
         border: 1px solid var(--gold-border);
         border-radius: var(--radius-btn);
         color: var(--text);
         font-size: var(--font-md);
         font-family: 'Plus Jakarta Sans', sans-serif;
         outline: none;
         transition: border-color 0.2s, box-shadow 0.2s;
      }

      .search-input::placeholder { color: var(--muted); }

      .search-input:focus {
         border-color: var(--gold-border-h);
         box-shadow: 0 0 0 3px rgba(201,168,76,0.08);
      }

      .search-btn {
         height: var(--ctrl-h);
         padding: 0 28px;
         background: var(--gold);
         color: #150d00;
         border: none;
         border-radius: var(--radius-btn);
         font-size: var(--font-md);
         font-weight: 700;
         cursor: pointer;
         font-family: 'Plus Jakarta Sans', sans-serif;
         display: flex;
         align-items: center;
         gap: 8px;
         transition: background 0.2s, transform 0.15s;
         white-space: nowrap;
      }

      .search-btn:hover { background: var(--gold-light); transform: translateY(-1px); }
      .search-btn:active { transform: scale(0.98); }

      /* ── RESULTS BAR ── */
      .results-bar {
         padding: 14px var(--page-pad);
         display: flex;
         align-items: center;
         gap: 8px;
         font-size: var(--font-sm);
         color: var(--muted);
         border-bottom: 1px solid rgba(201,168,76,0.08);
      }

      .results-bar .count {
         background: var(--gold-dim);
         border: 1px solid var(--gold-border);
         color: var(--gold-light);
         font-size: var(--font-xs);
         font-weight: 700;
         padding: 3px 10px;
         border-radius: 20px;
      }

      .results-bar .keyword {
         color: var(--text);
         font-weight: 600;
      }

      /* ── PRODUCTS ── */
      .products {
         padding: var(--gap) var(--page-pad) 64px;
      }

      .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
         gap: var(--gap);
         align-items: stretch;        /* semua baris sama tinggi */
      }

      /* ── CARD — semua kartu identik tingginya via flex ── */
      .product-card {
         background: var(--surf);
         border: 1px solid var(--gold-border);
         border-radius: var(--radius-card);
         overflow: hidden;
         display: flex;
         flex-direction: column;
         transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
      }

      .product-card:hover {
         border-color: var(--gold-border-h);
         transform: translateY(-4px);
         box-shadow: 0 12px 36px rgba(0,0,0,0.45), 0 0 0 1px rgba(201,168,76,0.1);
      }

      /* gambar — tinggi seragam --card-img-h */
      .img-wrap { overflow: hidden; flex-shrink: 0; }

      .card-img {
         width: 100%;
         height: var(--card-img-h);
         object-fit: cover;
         background: var(--surf2);
         display: block;
         transition: transform 0.4s;
      }

      .product-card:hover .card-img { transform: scale(1.04); }

      /* body tumbuh mengisi ruang sisa → footer selalu di bawah */
      .card-body {
         padding: var(--card-pad) var(--card-pad) 0;
         flex: 1;
         display: flex;
         flex-direction: column;
         gap: 6px;
      }

      .card-name {
         font-size: var(--font-md);
         font-weight: 700;
         color: var(--text);
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
      }

      .card-price {
         font-size: var(--font-lg);
         font-weight: 800;
         color: var(--gold-light);
         letter-spacing: -0.2px;
      }

      .card-price .rp {
         font-size: var(--font-xs);
         font-weight: 600;
         color: var(--gold);
         opacity: 0.85;
         margin-right: 1px;
      }

      /* ── CARD FOOTER — qty & tombol, tinggi seragam ── */
      .card-footer {
         display: flex;
         align-items: center;
         gap: 8px;
         padding: var(--card-pad);
      }

      /* qty stepper — tinggi = --ctrl-h */
      .qty-wrap {
         display: flex;
         align-items: center;
         height: var(--ctrl-h);
         background: var(--surf2);
         border: 1px solid var(--gold-border);
         border-radius: var(--radius-sm);
         overflow: hidden;
         flex-shrink: 0;
      }

      .qty-btn {
         width: 30px;
         height: 100%;
         background: none;
         border: none;
         color: var(--gold);
         font-size: 1rem;
         cursor: pointer;
         display: flex; align-items: center; justify-content: center;
         transition: background 0.15s;
      }

      .qty-btn:hover { background: rgba(201,168,76,0.1); }

      .qty-inp {
         width: 34px;
         height: 100%;
         background: none;
         border: none;
         color: var(--text);
         font-size: var(--font-sm);
         font-weight: 700;
         text-align: center;
         font-family: 'Plus Jakarta Sans', sans-serif;
         outline: none;
      }

      .qty-inp::-webkit-inner-spin-button,
      .qty-inp::-webkit-outer-spin-button { -webkit-appearance: none; }

      /* tombol tambah — tinggi = --ctrl-h */
      .add-btn {
         flex: 1;
         height: var(--ctrl-h);
         padding: 0 12px;
         background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
         color: #150d00;
         border: none;
         border-radius: var(--radius-sm);
         font-size: var(--font-sm);
         font-weight: 800;
         cursor: pointer;
         font-family: 'Plus Jakarta Sans', sans-serif;
         display: flex;
         align-items: center;
         justify-content: center;
         gap: 6px;
         transition: filter 0.2s, transform 0.15s;
      }

      .add-btn:hover { filter: brightness(1.12); transform: translateY(-1px); }
      .add-btn:active { transform: scale(0.97); }

      /* ── EMPTY STATE ── */
      .empty {
         grid-column: 1/-1;
         text-align: center;
         padding: 80px 0;
         color: var(--muted);
      }

      .empty-ring {
         width: 80px; height: 80px;
         border-radius: var(--radius-card);
         background: var(--gold-dim);
         border: 1px solid var(--gold-border);
         display: flex; align-items: center; justify-content: center;
         margin: 0 auto 20px;
         font-size: 1.7rem;
         color: var(--gold);
         position: relative;
      }

      .empty-ring::before {
         content: '';
         position: absolute;
         inset: -4px;
         border-radius: 20px;
         border: 1px solid var(--gold-border);
         opacity: 0.4;
      }

      .empty h4 {
         font-size: var(--font-lg);
         font-weight: 800;
         color: var(--text);
         margin-bottom: 8px;
      }

      .empty p {
         font-size: var(--font-sm);
         color: var(--muted);
         max-width: 300px;
         margin: 0 auto;
         line-height: 1.6;
      }

      /* ── GOLD DIVIDER ── */
      .gold-divider {
         height: 1px;
         background: linear-gradient(90deg, transparent, var(--gold-border), transparent);
         margin: 0 var(--page-pad);
      }

      /* ── RESPONSIVE ── */
      @media (max-width: 768px) {
         :root {
            --page-pad: 20px;
            --card-img-h: 180px;
            --gap: 14px;
         }
         .page-title { font-size: 1.6rem; }
         .box-container { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
         .search-wrap { flex-direction: column; }
         .search-btn { justify-content: center; }
         .gold-divider { margin: 0 var(--page-pad); }
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<?php
if(isset($message)){
   echo '<div class="toast-stack">';
   foreach($message as $msg){
      $is_error = strpos($msg, 'already') !== false;
      $icon  = $is_error ? 'fa-circle-exclamation' : 'fa-circle-check';
      $cls   = $is_error ? 'toast err' : 'toast';
      echo "
      <div class='$cls'>
         <i class='fas $icon'></i>
         <span>$msg</span>
         <i class='fas fa-xmark close-x' onclick='this.parentElement.remove()'></i>
      </div>";
   }
   echo '</div>';
}
?>

<!-- PAGE HEADER -->
<div class="page-header">
   <div class="breadcrumb">
      <a href="home.php">
         <i class="fas fa-house" style="font-size:11px;"></i>
         Beranda
      </a>
      <span class="sep">›</span>
      <span>Pencarian</span>
   </div>
   <h1 class="page-title">Cari <span>Buku</span></h1>
   <p class="page-sub">Temukan ribuan koleksi buku pilihan terbaik</p>
</div>

<!-- SEARCH FORM -->
<div class="search-area">
   <form action="" method="post">
      <div class="search-wrap">
         <div class="search-field">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" name="search" class="search-input"
               placeholder="Cari judul, pengarang, atau kata kunci…"
               value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
         </div>
         <button type="submit" name="submit" class="search-btn">
            <i class="fas fa-magnifying-glass"></i> Cari
         </button>
      </div>
   </form>
</div>

<?php if(isset($_POST['submit'])): ?>
   <?php
   $search_item = mysqli_real_escape_string($conn, $_POST['search']);
   $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
   $count = mysqli_num_rows($select_products);
   ?>

   <!-- RESULTS META -->
   <div class="results-bar">
      <span class="count"><?php echo $count; ?></span>
      hasil untuk <span class="keyword">"<?php echo htmlspecialchars($_POST['search']); ?>"</span>
   </div>

   <div class="gold-divider"></div>

   <section class="products">
      <div class="box-container">

      <?php if($count > 0): ?>
         <?php while($p = mysqli_fetch_assoc($select_products)): ?>

         <form action="" method="post" class="product-card">
            <div class="img-wrap">
               <img src="uploaded_img/<?php echo $p['image']; ?>"
                    alt="<?php echo htmlspecialchars($p['name']); ?>"
                    class="card-img">
            </div>
            <div class="card-body">
               <div class="card-name"><?php echo htmlspecialchars($p['name']); ?></div>
               <div class="card-price">
                  <span class="rp">Rp</span><?php echo number_format($p['price'], 0, ',', '.'); ?>
               </div>
            </div>
            <input type="hidden" name="product_name"  value="<?php echo $p['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $p['price']; ?>">
            <input type="hidden" name="product_image" value="<?php echo $p['image']; ?>">
            <div class="card-footer">
               <div class="qty-wrap">
                  <button type="button" class="qty-btn" onclick="adjQty(this,-1)">−</button>
                  <input type="number" class="qty-inp" name="product_quantity" value="1" min="1">
                  <button type="button" class="qty-btn" onclick="adjQty(this,1)">+</button>
               </div>
               <button type="submit" class="add-btn" name="add_to_cart">
                  <i class="fas fa-cart-plus" style="font-size:12px;"></i> Tambah
               </button>
            </div>
         </form>

         <?php endwhile; ?>

      <?php else: ?>

         <div class="empty">
            <div class="empty-ring"><i class="fas fa-magnifying-glass"></i></div>
            <h4>Tidak ditemukan</h4>
            <p>Tidak ada buku yang cocok dengan "<?php echo htmlspecialchars($_POST['search']); ?>"</p>
         </div>

      <?php endif; ?>

      </div>
   </section>

<?php else: ?>

   <section class="products">
      <div class="box-container">
         <div class="empty">
            <div class="empty-ring"><i class="fas fa-book-open"></i></div>
            <h4>Cari buku favorit Anda</h4>
            <p>Ketikkan judul, pengarang, atau kata kunci di kolom pencarian di atas</p>
         </div>
      </div>
   </section>

<?php endif; ?>

<?php include 'footer.php'; ?>

<script>
function adjQty(btn, d) {
   const inp = btn.parentElement.querySelector('.qty-inp');
   let v = parseInt(inp.value) + d;
   if (v < 1) v = 1;
   inp.value = v;
}
</script>

<script src="js/script.js"></script>

</body>
</html>