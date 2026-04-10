<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

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

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>

   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* ── Global Font ── */
      body {
         font-family: 'Poppins', sans-serif;
      }

      /* ── Page ── */
      .shop-page {
         background: #FCFBFA;
         min-height: 100vh;
         padding-bottom: 4rem;
      }

      /* ── Breadcrumb ── */
      .shop-breadcrumb {
         background: #fff;
         border-bottom: 1px solid rgba(212, 175, 55, 0.15);
         padding: 1rem 1.5rem;
         font-size: 13px;
         color: #888;
      }
      .shop-breadcrumb a {
         color: #D4AF37;
         text-decoration: none;
         font-weight: 500;
         transition: color 0.2s;
      }
      .shop-breadcrumb a:hover { 
         color: #B5952F; 
      }

      /* ── Wrapper ── */
      .shop-wrap {
         max-width: 1200px;
         margin: 3rem auto;
         padding: 0 1.5rem;
      }

      /* ── Header row ── */
      .shop-header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         margin-bottom: 2rem;
         padding-bottom: 1rem;
         border-bottom: 2px solid rgba(212, 175, 55, 0.2);
      }
      .shop-title {
         font-size: 24px;
         font-weight: 600;
         color: #2C2C2C;
         margin: 0;
      }
      .shop-count {
         font-size: 14px;
         font-weight: 500;
         color: #D4AF37;
         background: rgba(212, 175, 55, 0.1);
         padding: 4px 12px;
         border-radius: 20px;
      }

      /* ── Product grid ── */
      .product-grid {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
         gap: 1.5rem;
      }

      /* ── Product card ── */
      .product-card {
         background: #fff;
         border: 1px solid transparent;
         border-radius: 16px;
         box-shadow: 0 8px 24px rgba(0,0,0,0.04);
         overflow: hidden;
         display: flex;
         flex-direction: column;
         transition: all 0.3s ease;
      }
      .product-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 12px 30px rgba(212, 175, 55, 0.15);
         border-color: rgba(212, 175, 55, 0.3);
      }

      /* Image */
      .product-img {
         aspect-ratio: 1 / 1;
         overflow: hidden;
         background: #F9F9F9;
         position: relative;
      }
      .product-img img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         display: block;
         transition: transform 0.5s ease;
      }
      .product-card:hover .product-img img {
         transform: scale(1.08);
      }

      /* ── Stock badge (NEW) ── */
      .stock-badge {
         position: absolute;
         bottom: 10px;
         left: 10px;
         font-size: 11px;
         font-weight: 600;
         padding: 3px 10px;
         border-radius: 20px;
         display: flex;
         align-items: center;
         gap: 4px;
         box-shadow: 0 2px 6px rgba(0,0,0,0.10);
         pointer-events: none;
      }
      .stock-badge.in-stock  { background: #d1fae5; color: #065f46; }
      .stock-badge.low-stock { background: #fef3c7; color: #92400e; }
      .stock-badge.out-stock { background: #fee2e2; color: #991b1b; }

      /* Body */
      .product-body {
         padding: 1.25rem 1.25rem 0.5rem;
         flex: 1;
         display: flex;
         flex-direction: column;
         gap: 0.5rem;
      }
      .product-name {
         font-size: 16px;
         font-weight: 600;
         color: #2C2C2C;
         line-height: 1.4;
      }
      .product-price {
         font-size: 18px;
         font-weight: 700;
         color: #D4AF37;
      }

      /* ── Stock info row (NEW) ── */
      .product-stock-row {
         font-size: 12px;
         font-weight: 500;
         color: #888;
         display: flex;
         align-items: center;
         gap: 5px;
      }
      .product-stock-row i { font-size: 11px; }
      .product-stock-row.low  { color: #92400e; }
      .product-stock-row.out  { color: #991b1b; }
      .product-stock-row.ok   { color: #065f46; }

      /* Footer */
      .product-footer {
         display: flex;
         align-items: center;
         gap: 0.75rem;
         padding: 1rem 1.25rem 1.25rem;
      }

      /* Quantity stepper */
      .qty-wrap {
         display: flex;
         align-items: center;
         border: 1px solid #EAEAEA;
         border-radius: 8px;
         overflow: hidden;
         height: 40px;
         background: #FAFAFA;
      }
      .qty-btn {
         width: 32px;
         height: 40px;
         background: none;
         border: none;
         cursor: pointer;
         font-size: 16px;
         color: #555;
         transition: all 0.2s;
         display: flex;
         align-items: center;
         justify-content: center;
      }
      .qty-btn:hover { 
         background: rgba(212, 175, 55, 0.1); 
         color: #D4AF37;
      }

      .product-qty {
         width: 40px;
         height: 40px;
         border: none;
         border-left: 1px solid #EAEAEA;
         border-right: 1px solid #EAEAEA;
         text-align: center;
         font-size: 14px;
         font-weight: 600;
         color: #2C2C2C;
         background: #fff;
         outline: none;
         -moz-appearance: textfield;
      }
      .product-qty::-webkit-inner-spin-button,
      .product-qty::-webkit-outer-spin-button { -webkit-appearance: none; }

      /* Add to cart button */
      .add-btn {
         flex: 1;
         height: 40px;
         background: linear-gradient(135deg, #D4AF37 0%, #B5952F 100%);
         color: #fff;
         border: none;
         border-radius: 8px;
         font-size: 13px;
         font-weight: 600;
         cursor: pointer;
         letter-spacing: 0.5px;
         text-transform: uppercase;
         transition: all 0.3s ease;
         font-family: inherit;
         box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
      }
      .add-btn:hover { 
         background: linear-gradient(135deg, #C5A059 0%, #A68726 100%);
         box-shadow: 0 6px 14px rgba(212, 175, 55, 0.4);
         transform: translateY(-2px);
      }
      .add-btn:active { 
         transform: scale(0.96); 
      }

      /* Disabled button saat stok habis */
      .add-btn:disabled {
         background: linear-gradient(135deg, #ccc 0%, #aaa 100%);
         box-shadow: none;
         cursor: not-allowed;
         transform: none;
      }

      /* ── Empty state ── */
      .shop-empty {
         text-align: center;
         padding: 5rem 0;
         color: #888;
         font-size: 16px;
         grid-column: 1 / -1;
         background: #fff;
         border-radius: 16px;
         border: 1px dashed rgba(212, 175, 55, 0.4);
      }
      .shop-empty i {
         display: block;
         font-size: 48px;
         margin-bottom: 1rem;
         color: #D4AF37;
         opacity: 0.5;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="shop-page">

   <div class="shop-breadcrumb">
      <a href="home.php">Home</a> &rsaquo; Shop
   </div>

   <section class="products">
      <div class="shop-wrap">

         <?php
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            $total = mysqli_num_rows($select_products);
         ?>

         <div class="shop-header">
            <h2 class="shop-title">Produk Terbaru</h2>
            <span class="shop-count"><?php echo $total; ?> Produk</span>
         </div>

         <div class="product-grid">
            <?php
               if($total > 0){
                  while($fetch_products = mysqli_fetch_assoc($select_products)){

                     // Tentukan status stok
                     $stok = isset($fetch_products['stock']) ? (int)$fetch_products['stock'] : 0;
                     if($stok <= 0){
                        $badge_class  = 'out-stock';
                        $badge_label  = 'Stok Habis';
                        $badge_icon   = 'fa-times-circle';
                        $row_class    = 'out';
                        $row_label    = 'Stok habis';
                        $btn_disabled = 'disabled';
                     } elseif($stok <= 5){
                        $badge_class  = 'low-stock';
                        $badge_label  = 'Sisa '.$stok;
                        $badge_icon   = 'fa-exclamation-circle';
                        $row_class    = 'low';
                        $row_label    = 'Sisa '.$stok.' pcs';
                        $btn_disabled = '';
                     } else {
                        $badge_class  = 'in-stock';
                        $badge_label  = 'Tersedia';
                        $badge_icon   = 'fa-check-circle';
                        $row_class    = 'ok';
                        $row_label    = $stok.' pcs tersedia';
                        $btn_disabled = '';
                     }
            ?>
            <form action="" method="post" class="product-card">

               <div class="product-img">
                  <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="<?php echo $fetch_products['name']; ?>">
                  <!-- Badge stok di atas gambar -->
                  <span class="stock-badge <?php echo $badge_class; ?>">
                     <i class="fas <?php echo $badge_icon; ?>"></i>
                     <?php echo $badge_label; ?>
                  </span>
               </div>

               <div class="product-body">
                  <div class="product-name"><?php echo $fetch_products['name']; ?></div>
                  <div class="product-price">Rp<?php echo number_format($fetch_products['price'], 0, ',', '.'); ?></div>
                  <!-- Info stok di bawah harga -->
                  <div class="product-stock-row <?php echo $row_class; ?>">
                     <i class="fas fa-cubes"></i>
                     Stok: <?php echo $row_label; ?>
                  </div>
               </div>

               <div class="product-footer">
                  <div class="qty-wrap">
                     <button type="button" class="qty-btn" onclick="adjQty(this,-1)" <?php echo $btn_disabled; ?>>&#8722;</button>
                     <input type="number" name="product_quantity" value="1" min="1" <?php echo $stok > 0 ? 'max="'.$stok.'"' : 'max="0"'; ?> class="product-qty" <?php echo $btn_disabled; ?>>
                     <button type="button" class="qty-btn" onclick="adjQty(this,1)" <?php echo $btn_disabled; ?>>&#43;</button>
                  </div>
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                  <button type="submit" name="add_to_cart" class="add-btn" <?php echo $btn_disabled; ?>>
                     <i class="fas <?php echo $stok > 0 ? 'fa-shopping-cart' : 'fa-ban'; ?>" style="margin-right:6px;"></i>
                     <?php echo $stok > 0 ? 'Beli' : 'Habis'; ?>
                  </button>
               </div>

            </form>
            <?php
                  }
               }else{
                  echo '<div class="shop-empty"><i class="fas fa-box-open"></i>Belum ada produk yang ditambahkan</div>';
               }
            ?>
         </div>

      </div>
   </section>

</div>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
<script>
   function adjQty(btn, delta) {
      var inp = btn.parentElement.querySelector('.product-qty');
      var max = parseInt(inp.getAttribute('max')) || 9999;
      inp.value = Math.min(max, Math.max(1, (parseInt(inp.value) || 1) + delta));
   }
</script>

</body>
</html>