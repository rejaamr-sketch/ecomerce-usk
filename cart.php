<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
   $message[] = 'cart quantity updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
   header('location:cart.php');
}

if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Keranjang Belanja</title>

   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

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
      }

      * { box-sizing: border-box; margin: 0; padding: 0; }

      body {
         font-family: 'Poppins', sans-serif;
         background: var(--bg);
         color: var(--dark);
      }

      /* ── Breadcrumb ── */
      .heading {
         min-height: 20vh;
         background:
            linear-gradient(135deg, rgba(30,15,0,.85) 0%, rgba(100,70,0,.70) 100%),
            url(../images/heading-bg.webp) no-repeat center center;
         background-size: cover;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         gap: .8rem;
         text-align: center;
         padding: 2rem;
      }

      .heading h3 {
         font-size: 3.6rem;
         font-weight: 700;
         color: var(--gold-400);
         text-transform: uppercase;
         letter-spacing: .12em;
      }

      .heading p {
         font-size: 1.5rem;
         color: rgba(255,255,255,.55);
         letter-spacing: .05em;
      }

      .heading p a {
         color: var(--gold-200);
         text-decoration: none;
      }

      .heading p a:hover { text-decoration: underline; }

      /* ── Alert ── */
      .alert-msg {
         max-width: 1200px;
         margin: 2rem auto 0;
         padding: 1.2rem 2rem;
         background: var(--gold-100);
         border-left: 4px solid var(--gold-500);
         border-radius: .8rem;
         font-size: 1.5rem;
         color: var(--gold-800);
      }

      /* ── Layout ── */
      .shopping-cart {
         padding: 3.5rem 2rem 6rem;
         max-width: 1200px;
         margin: 0 auto;
      }

      .shopping-cart .title { display: none; }

      .cart-layout {
         display: grid;
         grid-template-columns: 1fr 36rem;
         gap: 2.4rem;
         align-items: flex-start;
      }

      /* ── Items Panel ── */
      .cart-items-panel {
         background: var(--surface);
         border-radius: 1.8rem;
         border: 1px solid var(--border);
         box-shadow: var(--shadow);
         overflow: hidden;
      }

      .cart-panel-header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 1.8rem 2.4rem;
         background: var(--gold-50);
         border-bottom: 1px solid var(--border);
      }

      .cart-panel-header h2 {
         font-size: 1.7rem;
         font-weight: 600;
         color: var(--gold-800);
         display: flex;
         align-items: center;
         gap: .7rem;
      }

      .cart-panel-header h2 i { color: var(--gold-500); }

      .cart-panel-header .item-count {
         background: var(--gold-100);
         color: var(--gold-700);
         border: 1px solid var(--gold-200);
         font-size: 1.2rem;
         font-weight: 600;
         padding: .3rem 1rem;
         border-radius: 2rem;
      }

      /* ── Cart Row ── */
      .cart-row {
         display: grid;
         grid-template-columns: 10rem 1fr auto;
         gap: 1.8rem;
         align-items: center;
         padding: 2rem 2.4rem;
         border-bottom: 1px solid var(--border);
         transition: background .15s;
      }

      .cart-row:last-child { border-bottom: none; }
      .cart-row:hover { background: var(--gold-50); }

      .cart-row .item-img {
         width: 10rem;
         height: 10rem;
         object-fit: cover;
         border-radius: 1rem;
         border: 1px solid var(--border);
      }

      .cart-row .item-info {
         display: flex;
         flex-direction: column;
         gap: .4rem;
      }

      .cart-row .item-name {
         font-size: 1.6rem;
         font-weight: 600;
         color: var(--dark);
         line-height: 1.3;
      }

      .cart-row .item-price {
         font-size: 1.5rem;
         font-weight: 700;
         color: var(--gold-600);
      }

      .cart-row .item-subtotal {
         font-size: 1.3rem;
         color: #A09070;
      }

      .cart-row .item-subtotal strong {
         color: var(--gold-700);
         font-weight: 600;
      }

      /* Qty form */
      .qty-form {
         display: flex;
         align-items: center;
         gap: .6rem;
         margin-top: .8rem;
      }

      .qty-form input[type="number"] {
         width: 6.5rem;
         padding: .6rem 1rem;
         font-size: 1.4rem;
         font-family: 'Poppins', sans-serif;
         border: 1.5px solid var(--border);
         border-radius: .7rem;
         color: var(--dark);
         background: var(--gold-50);
         outline: none;
         transition: border-color .2s;
      }

      .qty-form input[type="number"]:focus {
         border-color: var(--gold-500);
         background: #fff;
      }

      .btn-update {
         padding: .6rem 1.4rem;
         font-size: 1.3rem;
         font-weight: 500;
         font-family: 'Poppins', sans-serif;
         background: var(--gold-100);
         color: var(--gold-800);
         border: 1.5px solid var(--gold-200);
         border-radius: .7rem;
         cursor: pointer;
         transition: .2s;
      }

      .btn-update:hover {
         background: var(--gold-500);
         color: #fff;
         border-color: var(--gold-500);
      }

      /* Remove button */
      .btn-remove {
         width: 3.6rem;
         height: 3.6rem;
         border-radius: 50%;
         background: #fff5f5;
         color: #b91c1c;
         border: 1.5px solid #fecaca;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.3rem;
         text-decoration: none;
         flex-shrink: 0;
         transition: .2s;
      }

      .btn-remove:hover {
         background: #b91c1c;
         color: #fff;
         border-color: #b91c1c;
      }

      /* ── Clear All ── */
      .cart-clear-row {
         padding: 1.4rem 2.4rem;
         border-top: 1px solid var(--border);
         background: var(--gold-50);
         text-align: right;
      }

      .btn-clear-all {
         display: inline-flex;
         align-items: center;
         gap: .6rem;
         padding: .7rem 1.6rem;
         font-size: 1.3rem;
         font-weight: 500;
         font-family: 'Poppins', sans-serif;
         background: #fff5f5;
         color: #b91c1c;
         border: 1.5px solid #fecaca;
         border-radius: .7rem;
         text-decoration: none;
         cursor: pointer;
         transition: .2s;
      }

      .btn-clear-all:hover {
         background: #b91c1c;
         color: #fff;
         border-color: #b91c1c;
      }

      .btn-clear-all.disabled {
         pointer-events: none;
         opacity: .35;
         user-select: none;
      }

      /* ── Empty State ── */
      .cart-empty {
         padding: 6rem 2rem;
         text-align: center;
      }

      .cart-empty .empty-icon {
         font-size: 5.5rem;
         color: var(--gold-200);
         margin-bottom: 1.5rem;
      }

      .cart-empty p {
         font-size: 1.8rem;
         color: var(--gold-600);
         font-weight: 500;
         margin-bottom: 2.5rem;
      }

      .cart-empty a {
         display: inline-block;
         padding: 1.2rem 3rem;
         background: var(--gold-500);
         color: #fff;
         font-size: 1.5rem;
         font-weight: 600;
         font-family: 'Poppins', sans-serif;
         border-radius: .9rem;
         text-decoration: none;
         transition: .2s;
      }

      .cart-empty a:hover { background: var(--gold-600); }

      /* ── Summary Sidebar ── */
      .cart-summary {
         background: var(--surface);
         border-radius: 1.8rem;
         border: 1px solid var(--border);
         box-shadow: var(--shadow);
         overflow: hidden;
         position: sticky;
         top: 9rem;
      }

      .summary-header {
         padding: 2rem 2.4rem;
         background: var(--dark);
         display: flex;
         align-items: center;
         gap: .8rem;
      }

      .summary-header i {
         color: var(--gold-400);
         font-size: 1.6rem;
      }

      .summary-header h2 {
         font-size: 1.6rem;
         font-weight: 600;
         color: #fff;
         letter-spacing: .06em;
         text-transform: uppercase;
      }

      .summary-body {
         padding: 2rem 2.4rem 1.5rem;
      }

      .summary-row {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 1rem 0;
         border-bottom: 1px solid var(--border);
         font-size: 1.5rem;
      }

      .summary-row:last-of-type { border-bottom: none; }
      .summary-row .label { color: #A09070; }
      .summary-row .value { font-weight: 500; color: var(--dark); }
      .summary-row .free  { color: #15803d; font-weight: 600; }

      .summary-divider {
         height: 1px;
         background: var(--gold-200);
         margin: 1rem 0;
      }

      .summary-total {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 1.2rem 0 .5rem;
      }

      .summary-total .label {
         font-size: 1.5rem;
         font-weight: 700;
         color: var(--dark);
         text-transform: uppercase;
         letter-spacing: .06em;
      }

      .summary-total .value {
         font-size: 2.4rem;
         font-weight: 700;
         color: var(--gold-600);
      }

      .summary-actions {
         padding: 0 2.4rem 2.4rem;
         display: flex;
         flex-direction: column;
         gap: 1rem;
      }

      .btn-checkout {
         display: flex;
         align-items: center;
         justify-content: center;
         gap: .7rem;
         width: 100%;
         padding: 1.4rem;
         background: var(--gold-500);
         color: #fff;
         font-size: 1.6rem;
         font-weight: 600;
         font-family: 'Poppins', sans-serif;
         letter-spacing: .04em;
         border-radius: 1rem;
         text-decoration: none;
         transition: background .2s, transform .15s;
         border: none;
         cursor: pointer;
      }

      .btn-checkout:hover {
         background: var(--gold-600);
         transform: translateY(-2px);
      }

      .btn-checkout.disabled {
         pointer-events: none;
         opacity: .4;
         user-select: none;
      }

      .btn-continue {
         display: flex;
         align-items: center;
         justify-content: center;
         gap: .7rem;
         width: 100%;
         padding: 1.2rem;
         background: transparent;
         color: var(--gold-700);
         font-size: 1.5rem;
         font-weight: 500;
         font-family: 'Poppins', sans-serif;
         border: 1.5px solid var(--border);
         border-radius: 1rem;
         text-decoration: none;
         transition: .2s;
      }

      .btn-continue:hover {
         background: var(--gold-50);
         border-color: var(--gold-400);
      }

      /* Security note */
      .secure-note {
         display: flex;
         align-items: center;
         justify-content: center;
         gap: .5rem;
         font-size: 1.2rem;
         color: #A09070;
         padding: 0 2.4rem 2rem;
      }

      .secure-note i { color: var(--gold-400); }

      /* ── Responsive ── */
      @media (max-width: 900px) {
         .cart-layout { grid-template-columns: 1fr; }
         .cart-summary { position: static; }
      }

      @media (max-width: 600px) {
         .cart-row {
            grid-template-columns: 8rem 1fr auto;
            gap: 1.2rem;
            padding: 1.5rem;
         }
         .cart-row .item-img { width: 8rem; height: 8rem; }
         .shopping-cart { padding: 2rem 1.2rem 5rem; }
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Breadcrumb -->
<div class="heading">
   <h3>Keranjang Belanja</h3>
   <p><a href="home.php">Home</a> / Cart</p>
</div>

<?php if(isset($message)): foreach($message as $msg): ?>
<div class="alert-msg"><i class="fas fa-check-circle"></i> <?php echo $msg; ?></div>
<?php endforeach; endif; ?>

<section class="shopping-cart">
   <div class="cart-layout">

      <!-- ── LEFT: Items ── -->
      <div>
         <div class="cart-items-panel">

            <?php
               $grand_total = 0;
               $item_count  = 0;
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $has_items   = mysqli_num_rows($select_cart) > 0;
               $total_items = mysqli_num_rows($select_cart);
            ?>

            <div class="cart-panel-header">
               <h2><i class="fas fa-shopping-bag"></i> Keranjang Saya</h2>
               <span class="item-count"><?php echo $total_items; ?> item</span>
            </div>

            <?php if($has_items): ?>

               <?php while($fetch_cart = mysqli_fetch_assoc($select_cart)):
                  $sub_total    = $fetch_cart['quantity'] * $fetch_cart['price'];
                  $grand_total += $sub_total;
                  $item_count++;
               ?>
               <div class="cart-row">

                  <img class="item-img" src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="<?php echo $fetch_cart['name']; ?>">

                  <div class="item-info">
                     <div class="item-name"><?php echo $fetch_cart['name']; ?></div>
                     <div class="item-price">Rp<?php echo number_format($fetch_cart['price'], 0, ',', '.'); ?></div>
                     <div class="item-subtotal">Subtotal: <strong>Rp<?php echo number_format($sub_total, 0, ',', '.'); ?></strong></div>
                     <form action="" method="post" class="qty-form">
                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                        <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                        <button type="submit" name="update_cart" class="btn-update">Update</button>
                     </form>
                  </div>

                  <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>"
                     class="btn-remove"
                     onclick="return confirm('Hapus item ini dari keranjang?');">
                     <i class="fas fa-times"></i>
                  </a>

               </div>
               <?php endwhile; ?>

               <div class="cart-clear-row">
                  <a href="cart.php?delete_all"
                     class="btn-clear-all <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>"
                     onclick="return confirm('Hapus semua item dari keranjang?');">
                     <i class="fas fa-trash"></i> Hapus Semua
                  </a>
               </div>

            <?php else: ?>

               <div class="cart-empty">
                  <div class="empty-icon"><i class="fas fa-shopping-cart"></i></div>
                  <p>Keranjang kamu masih kosong.</p>
                  <a href="shop.php"><i class="fas fa-store" style="margin-right:.5rem;"></i> Mulai Belanja</a>
               </div>

            <?php endif; ?>

         </div>
      </div>

      <!-- ── RIGHT: Summary ── -->
      <div class="cart-summary">
         <div class="summary-header">
            <i class="fas fa-receipt"></i>
            <h2>Ringkasan Pesanan</h2>
         </div>

         <div class="summary-body">
            <div class="summary-row">
               <span class="label">Jumlah Item</span>
               <span class="value"><?php echo $item_count; ?> item</span>
            </div>
            <div class="summary-row">
               <span class="label">Subtotal</span>
               <span class="value">Rp<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
            </div>
            <div class="summary-row">
               <span class="label">Ongkos Kirim</span>
               <span class="value free"><i class="fas fa-truck" style="margin-right:.4rem;font-size:1.2rem;"></i>Gratis</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total">
               <span class="label">Total</span>
               <span class="value">Rp<?php echo number_format($grand_total, 0, ',', '.'); ?></span>
            </div>
         </div>

         <div class="summary-actions">
            <a href="checkout.php" class="btn-checkout <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">
               <i class="fas fa-lock"></i> Checkout Sekarang
            </a>
            <a href="shop.php" class="btn-continue">
               <i class="fas fa-arrow-left"></i> Lanjut Belanja
            </a>
         </div>

         <div class="secure-note">
            <i class="fas fa-shield-alt"></i> Transaksi aman & terenkripsi
         </div>
      </div>

   </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>