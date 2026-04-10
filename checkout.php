<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

$payment_proof = '';
$proof_error   = '';

if(isset($_POST['order_btn'])){

   $name    = mysqli_real_escape_string($conn, $_POST['name']);
   $number  = $_POST['number'];
   $email   = mysqli_real_escape_string($conn, $_POST['email']);
   $method  = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn,
      'No. '.$_POST['flat'].', '.$_POST['street'].', '.
      $_POST['city'].', '.$_POST['country'].' - '.$_POST['pin_code']
   );
   $placed_on = date('d-M-Y');

   if($method === 'QRIS'){
      if(isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0){
         $allowed = ['image/jpeg','image/png','image/webp','application/pdf'];
         $ftype   = $_FILES['payment_proof']['type'];
         $fsize   = $_FILES['payment_proof']['size'];
         if(!in_array($ftype, $allowed)){
            $proof_error = 'Format file tidak didukung. Gunakan JPG, PNG, WEBP, atau PDF.';
         } elseif($fsize > 5000000){
            $proof_error = 'Ukuran file terlalu besar (maks. 5MB).';
         } else {
            if(!is_dir('payment_proof')) mkdir('payment_proof', 0755, true);
            $fname = time().'_'.basename($_FILES['payment_proof']['name']);
            move_uploaded_file($_FILES['payment_proof']['tmp_name'], 'payment_proof/'.$fname);
            $payment_proof = $fname;
         }
      } else {
         $proof_error = 'Harap upload bukti pembayaran QRIS.';
      }
   }

   if(empty($proof_error)){
      $cart_total      = 0;
      $cart_products[] = '';

      $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($cart_query) > 0){
         while($cart_item = mysqli_fetch_assoc($cart_query)){
            $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
            $cart_total += ($cart_item['price'] * $cart_item['quantity']);
         }
      }
      $total_products = implode(', ', $cart_products);

      $order_query = mysqli_query($conn,
         "SELECT * FROM `orders` WHERE name='$name' AND number='$number' AND email='$email'
          AND method='$method' AND address='$address'
          AND total_products='$total_products' AND total_price='$cart_total'"
      ) or die('query failed');

      if($cart_total == 0){
         $message[] = 'your cart is empty';
      } else {
         if(mysqli_num_rows($order_query) > 0){
            $message[] = 'order already placed!';
         } else {
            $proof_col = mysqli_real_escape_string($conn, $payment_proof);
            mysqli_query($conn,
               "INSERT INTO `orders`(user_id,name,number,email,method,address,total_products,total_price,placed_on)
                VALUES('$user_id','$name','$number','$email','$method','$address','$total_products','$cart_total','$placed_on')"
            ) or die('query failed');
            $message[] = 'order placed successfully!';
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         }
      }
   } else {
      $message[] = $proof_error;
   }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">

   <style>
   :root{
      --g50:#FFFDF5; --g100:#FFF8E1; --g200:#FFE9A0;
      --g300:#F5D060; --g400:#E8B800; --g500:#C9960C;
      --g600:#A67C00; --g700:#7A5800; --g800:#4E3800;
      --dark:#1A1500; --white:#fff;
      --border:rgba(200,160,0,.20);
      --shadow:0 6px 28px rgba(180,140,0,.13);
      --radius:1.4rem; --radius-sm:.8rem;
      --ff-display:'Playfair Display',serif;
      --ff-body:'Poppins',sans-serif;
      --ease:.25s cubic-bezier(.4,0,.2,1);
   }

   body{ background:var(--g50); font-family:var(--ff-body); }

   /* Heading */
   .heading{
      background:linear-gradient(135deg,var(--g700),var(--g500));
      padding:3.5rem 2rem; text-align:center;
   }
   .heading h3{
      font-family:var(--ff-display); font-size:3.4rem;
      color:var(--g200); letter-spacing:.06em; text-transform:capitalize;
   }
   .heading p,.heading p a{ color:var(--g200); font-size:1.4rem; opacity:.85; }
   .heading p a:hover{ opacity:1; text-decoration:underline; }

   /* Alerts */
   .checkout-alerts{ max-width:110rem; margin:2.5rem auto 0; padding:0 2rem; }
   .alert{
      display:flex; align-items:center; gap:1.2rem;
      padding:1.5rem 2rem; border-radius:var(--radius-sm);
      font-size:1.5rem; margin-bottom:1rem;
      animation:fadeDown .35s ease;
   }
   @keyframes fadeDown{ from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
   .alert-success{ background:#e8f5e9; border-left:4px solid #43a047; color:#1b5e20; }
   .alert-error  { background:var(--g100); border-left:4px solid var(--g400); color:var(--g800); }

   /* Page grid */
   .checkout-page{
      display:grid; grid-template-columns:1fr 38rem; gap:3rem;
      max-width:110rem; margin:3rem auto 6rem; padding:0 2rem;
      align-items:start;
   }

   /* Cards */
   .co-card{
      background:var(--white); border:1px solid var(--border);
      border-radius:var(--radius); box-shadow:var(--shadow);
      overflow:hidden; margin-bottom:2.8rem;
   }
   .co-card:last-child{ margin-bottom:0; }
   .co-card-head{
      display:flex; align-items:center; gap:1.2rem;
      padding:1.8rem 2.4rem;
      background:linear-gradient(135deg,var(--g50),var(--g100));
      border-bottom:1px solid var(--border);
   }
   .co-card-head .ch-icon{
      width:3.8rem; height:3.8rem; border-radius:.7rem;
      background:var(--g400); color:#fff;
      display:flex; align-items:center; justify-content:center;
      font-size:1.6rem; flex-shrink:0;
   }
   .co-card-head h3{
      font-family:var(--ff-display); font-size:1.9rem;
      color:var(--g800); letter-spacing:.02em;
   }
   .co-card-body{ padding:2.4rem; }

   /* Form grid */
   .co-grid{ display:grid; grid-template-columns:1fr 1fr; gap:1.6rem; }
   .co-grid .span2{ grid-column:1/-1; }

   .inputBox2{ display:flex; flex-direction:column; gap:.6rem; }
   .inputBox2 span{
      font-size:1.2rem; font-weight:600; color:var(--g700);
      text-transform:uppercase; letter-spacing:.05em;
   }
   .input-wrap2{ position:relative; }
   .input-wrap2 .ii{
      position:absolute; left:1.4rem; top:50%; transform:translateY(-50%);
      color:var(--g400); font-size:1.5rem; pointer-events:none;
   }
   .inputBox2 input,
   .inputBox2 select{
      width:100%; font-family:var(--ff-body); font-size:1.5rem; color:var(--dark);
      background:var(--g50); border:1.5px solid var(--border);
      border-radius:var(--radius-sm);
      padding:1.2rem 1.4rem 1.2rem 4rem;
      outline:none; appearance:none;
      transition:border-color var(--ease),box-shadow var(--ease),background var(--ease);
   }
   .inputBox2 input:focus,
   .inputBox2 select:focus{
      border-color:var(--g400); background:#fff;
      box-shadow:0 0 0 3px rgba(232,184,0,.14);
   }
   .inputBox2 input::placeholder{ color:#C0A860; opacity:.75; }

   /* Payment cards */
   .pay-grid{ display:grid; grid-template-columns:1fr 1fr; gap:1.4rem; margin-bottom:2rem; }
   .pay-opt{ position:relative; }
   .pay-opt input[type=radio]{ position:absolute; opacity:0; width:0; height:0; }
   .pay-lbl{
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      gap:.9rem; padding:2rem 1.2rem;
      border:2px solid var(--border); border-radius:var(--radius-sm);
      background:var(--g50); cursor:pointer; text-align:center;
      transition:all var(--ease);
   }
   .pay-lbl:hover{ border-color:var(--g300); background:var(--g100); }
   .pay-opt input:checked + .pay-lbl{
      border-color:var(--g400);
      background:linear-gradient(135deg,var(--g100),#fff9df);
      box-shadow:0 0 0 3px rgba(232,184,0,.15);
   }
   .pay-lbl .p-icon{
      width:4.8rem; height:4.8rem; border-radius:.7rem;
      display:flex; align-items:center; justify-content:center; font-size:2rem;
   }
   .ico-qris{ background:#e8f5e9; color:#2e7d32; }
   .ico-cod { background:#e3f2fd; color:#1565c0; }
   .pay-lbl .p-name{ font-size:1.5rem; font-weight:700; color:var(--g800); }
   .pay-lbl .p-sub { font-size:1.2rem; color:var(--g600); }
   .pay-tick{
      display:none; position:absolute; top:1rem; right:1rem;
      width:2rem; height:2rem; border-radius:50%;
      background:var(--g400); color:#fff;
      align-items:center; justify-content:center; font-size:1rem;
   }
   .pay-opt input:checked ~ .pay-tick{ display:flex; }

   /* QRIS panel */
   .qris-panel{ display:none; animation:fadeDown .3s ease; }
   .qris-panel.show{ display:block; }
   .qris-info{
      background:linear-gradient(135deg,#f0fff4,#e8fdf0);
      border:2px dashed rgba(46,125,50,.25); border-radius:var(--radius-sm);
      padding:2.5rem; text-align:center; margin-bottom:2rem;
   }
   .qris-info img{
      max-width:17rem; border-radius:.8rem;
      box-shadow:0 4px 18px rgba(0,0,0,.10); margin-bottom:1.2rem;
   }
   .qris-placeholder{
      width:17rem; height:17rem; margin:0 auto 1.2rem;
      border-radius:.8rem; background:#f1fff4;
      border:2px dashed #a5d6a7;
      display:flex; flex-direction:column;
      align-items:center; justify-content:center; gap:.8rem;
   }
   .qris-placeholder i{ font-size:5rem; color:#4caf50; }
   .qris-placeholder span{ font-size:1.3rem; color:#388e3c; font-weight:600; }
   .qris-info p{ font-size:1.4rem; color:#2e7d32; font-weight:500; margin-bottom:.3rem; }
   .qris-info small{ font-size:1.2rem; color:#4caf50; }
   .qris-id{
      display:inline-block; margin-top:1rem;
      background:#fff; border:1.5px solid rgba(46,125,50,.2);
      border-radius:var(--radius-sm); padding:.7rem 2rem;
      font-size:1.5rem; font-weight:700; color:#1b5e20; letter-spacing:.1em;
   }

   /* Upload zone */
   .upload-zone{
      border:2px dashed var(--border); border-radius:var(--radius-sm);
      padding:2.5rem; text-align:center; cursor:pointer;
      background:var(--g50); position:relative;
      transition:all var(--ease);
   }
   .upload-zone:hover,.upload-zone.drag{ border-color:var(--g400); background:var(--g100); }
   .upload-zone input[type=file]{
      position:absolute; inset:0; width:100%; height:100%;
      opacity:0; cursor:pointer;
   }
   .upload-zone .uz-icon{ font-size:3.2rem; color:var(--g300); margin-bottom:.8rem; }
   .upload-zone p{ font-size:1.4rem; color:var(--g600); font-weight:500; }
   .upload-zone small{ font-size:1.2rem; color:#c0a860; }
   #proof-preview{ display:none; margin-top:1.4rem; text-align:center; }
   #proof-preview img{ max-height:15rem; border-radius:var(--radius-sm); box-shadow:var(--shadow); }
   #proof-preview p{ font-size:1.3rem; color:var(--g600); margin-top:.5rem; }

   /* COD panel */
   .cod-panel{ display:none; animation:fadeDown .3s ease; }
   .cod-panel.show{ display:block; }
   .cod-info{
      background:linear-gradient(135deg,#e3f2fd,#eff7ff);
      border:2px dashed rgba(21,101,192,.25); border-radius:var(--radius-sm);
      padding:2.2rem; display:flex; align-items:flex-start; gap:1.4rem;
   }
   .cod-info i{ font-size:2.6rem; color:#1565c0; flex-shrink:0; margin-top:.2rem; }
   .cod-info h4{ font-size:1.6rem; font-weight:700; color:#0d47a1; margin-bottom:.5rem; }
   .cod-info p{ font-size:1.4rem; color:#1565c0; line-height:1.7; }

   /* Submit button */
   .btn-order{
      width:100%; margin-top:1.8rem; padding:1.6rem;
      background:linear-gradient(135deg,var(--g500),var(--g400));
      color:var(--g800); font-family:var(--ff-body);
      font-size:1.6rem; font-weight:700;
      border:none; border-radius:var(--radius-sm);
      cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.9rem;
      box-shadow:0 6px 22px rgba(180,140,0,.25);
      transition:all var(--ease);
   }
   .btn-order:hover{ background:linear-gradient(135deg,var(--g600),var(--g500)); transform:translateY(-2px); }
   .btn-order:active{ transform:translateY(0); }

   /* Order summary (right column) */
   .summary-sticky{ position:sticky; top:10rem; }
   .display-order-new{
      background:var(--white); border:1px solid var(--border);
      border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
   }
   .doh{
      padding:1.8rem 2.4rem;
      background:linear-gradient(135deg,var(--g50),var(--g100));
      border-bottom:1px solid var(--border);
      display:flex; align-items:center; gap:1rem;
   }
   .doh .ch-icon{ width:3.8rem; height:3.8rem; border-radius:.7rem; background:var(--g400); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.6rem; }
   .doh h3{ font-family:var(--ff-display); font-size:1.9rem; color:var(--g800); }
   .dob{ padding:2.4rem; }
   .dob p{
      display:flex; justify-content:space-between; align-items:center;
      font-size:1.45rem; color:var(--dark);
      padding:1rem 0; border-bottom:1px solid var(--g100);
   }
   .dob p:last-of-type{ border-bottom:none; }
   .dob p span{ font-weight:600; color:var(--g600); }
   .dob .empty{ color:var(--g500); font-style:italic; border:none; }
   .grand-total-new{
      margin-top:1.6rem; padding:1.6rem 2rem;
      background:linear-gradient(135deg,var(--g100),var(--g50));
      border-radius:var(--radius-sm); border:1px solid var(--border);
      display:flex; justify-content:space-between; align-items:center;
   }
   .grand-total-new span:first-child{ font-size:1.5rem; font-weight:600; color:var(--g700); }
   .grand-total-new span:last-child{ font-family:var(--ff-display); font-size:2.6rem; font-weight:700; color:var(--g600); }

   /* Responsive */
   @media(max-width:900px){
      .checkout-page{ grid-template-columns:1fr; }
      .summary-sticky{ position:static; }
   }
   @media(max-width:580px){
      .co-grid{ grid-template-columns:1fr; }
      .pay-grid{ grid-template-columns:1fr 1fr; }
      .co-card-body{ padding:1.8rem; }
   }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
   <h3>Checkout</h3>
   <p><a href="home.php">home</a> / checkout</p>
</div>

<!-- Alerts -->
<div class="checkout-alerts">
<?php if(isset($message)) foreach($message as $msg): ?>
   <div class="alert <?php echo (stripos($msg,'success') !== false || stripos($msg,'berhasil') !== false) ? 'alert-success' : 'alert-error'; ?>">
      <i class="fas fa-<?php echo (stripos($msg,'success') !== false || stripos($msg,'berhasil') !== false) ? 'check-circle' : 'exclamation-circle'; ?>"></i>
      <?php echo $msg; ?>
   </div>
<?php endforeach; ?>
</div>

<!-- MAIN GRID -->
<div class="checkout-page">

   <!-- LEFT: Form -->
   <div>
      <form action="" method="post" enctype="multipart/form-data" id="coForm">

         <!-- Personal Info -->
         <div class="co-card">
            <div class="co-card-head">
               <div class="ch-icon"><i class="fas fa-user"></i></div>
               <h3>Masukan Data Anda</h3>
            </div>
            <div class="co-card-body">
               <div class="co-grid">
                  <div class="inputBox2">
                     <span>Nama Lengkap</span>
                     <div class="input-wrap2">
                        <i class="fas fa-user ii"></i>
                        <input type="text" name="name" required placeholder="Masukan nama lengkap anda">
                     </div>
                  </div>
                  <div class="inputBox2">
                     <span>Nomor Anda</span>
                     <div class="input-wrap2">
                        <i class="fas fa-phone ii"></i>
                        <input type="number" name="number" required placeholder="Masukan nomor anda">
                     </div>
                  </div>
                  <div class="inputBox2 span2">
                     <span>Email</span>
                     <div class="input-wrap2">
                        <i class="fas fa-envelope ii"></i>
                        <input type="email" name="email" required placeholder="Masukan alamat email anda">
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Shipping Address -->
         <div class="co-card">
            <div class="co-card-head">
               <div class="ch-icon"><i class="fas fa-map-marker-alt"></i></div>
               <h3>Alamat Pengiriman</h3>
            </div>
            <div class="co-card-body">
               <div class="co-grid">
                  <div class="inputBox2">
                     <span>House Number</span>
                     <div class="input-wrap2">
                        <i class="fas fa-home ii"></i>
                        <input type="number" min="0" name="flat" required placeholder="Nomor rumah">
                     </div>
                  </div>
                  <div class="inputBox2">
                     <span>Street Address</span>
                     <div class="input-wrap2">
                        <i class="fas fa-road ii"></i>
                        <input type="text" name="street" required placeholder="Nama jalan">
                     </div>
                  </div>
                  <div class="inputBox2">
                     <span>City</span>
                     <div class="input-wrap2">
                        <i class="fas fa-city ii"></i>
                        <input type="text" name="city" required placeholder="Kota">
                     </div>
                  </div>
                  <div class="inputBox2">
                     <span>State</span>
                     <div class="input-wrap2">
                        <i class="fas fa-map ii"></i>
                        <input type="text" name="state" required placeholder="Provinsi">
                     </div>
                  </div>
                  <div class="inputBox2">
                     <span>Country</span>
                     <div class="input-wrap2">
                        <i class="fas fa-globe ii"></i>
                        <input type="text" name="country" required placeholder="Indonesia">
                     </div>
                  </div>
                  <div class="inputBox2">
                     <span>Pin Code</span>
                     <div class="input-wrap2">
                        <i class="fas fa-hashtag ii"></i>
                        <input type="number" min="0" name="pin_code" required placeholder="Kode pos">
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Payment Method -->
         <div class="co-card">
            <div class="co-card-head">
               <div class="ch-icon"><i class="fas fa-wallet"></i></div>
               <h3>Pilih Metode Pembayaran</h3>
            </div>
            <div class="co-card-body">

               <!-- Hidden select keeps original name="method" for PHP -->
               <select name="method" id="methodSelect" style="display:none;">
                  <option value="QRIS">QRIS</option>
                  <option value="cash on delivery">cash on delivery</option>
               </select>

               <div class="pay-grid">
                  <!-- QRIS -->
                  <div class="pay-opt">
                     <input type="radio" name="pay_ui" id="ui_qris" value="QRIS"
                            onchange="switchPay('QRIS')" checked>
                     <label for="ui_qris" class="pay-lbl">
                        <div class="p-icon ico-qris"><i class="fas fa-qrcode"></i></div>
                        <span class="p-name">QRIS</span>
                        <span class="p-sub">Scan &amp; bayar instan</span>
                     </label>
                     <div class="pay-tick"><i class="fas fa-check"></i></div>
                  </div>
                  <!-- COD -->
                  <div class="pay-opt">
                     <input type="radio" name="pay_ui" id="ui_cod" value="cash on delivery"
                            onchange="switchPay('cash on delivery')">
                     <label for="ui_cod" class="pay-lbl">
                        <div class="p-icon ico-cod"><i class="fas fa-money-bill-wave"></i></div>
                        <span class="p-name">COD</span>
                        <span class="p-sub">Bayar saat terima</span>
                     </label>
                     <div class="pay-tick"><i class="fas fa-check"></i></div>
                  </div>
               </div>

               <!-- QRIS Panel -->
               <div class="qris-panel show" id="qrisPanel">
                  <div class="qris-info">
                     <!-- Ganti src dengan path QR code toko Anda -->
                     <img src="images/Qris.jpeg" alt="QRIS"
                          onerror="this.style.display='none';document.getElementById('qrisPH').style.display='flex';">
                     <div class="qris-placeholder" id="qrisPH" style="display:none;">
                        <i class="fas fa-qrcode"></i>
                        <span>QR Code Toko</span>
                     </div>
                     <p><i class="fas fa-shield-alt"></i> Pembayaran aman &amp; terverifikasi</p>
                     <small>Scan dengan m-banking atau e-wallet apapun</small>
                     <div class="qris-id">QRIS &middot; TOKO BUKU</div>
                  </div>

                  <div class="inputBox2" style="margin-bottom:0;">
                     <span>
                        <i class="fas fa-upload" style="color:var(--g400);margin-right:.4rem;"></i>
                        Upload Bukti Pembayaran *
                     </span>
                     <div class="upload-zone" id="uploadZone">
                        <input type="file" name="payment_proof" id="proofFile"
                               accept="image/jpeg,image/png,image/webp,application/pdf"
                               onchange="previewProof(this)">
                        <div class="uz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p>Klik atau seret file ke sini</p>
                        <small>JPG, PNG, WEBP, PDF &middot; Maks. 5MB</small>
                     </div>
                     <div id="proof-preview">
                        <img id="previewImg" src="" alt="Preview">
                        <p id="previewName"></p>
                     </div>
                  </div>
               </div>

               <!-- COD Panel -->
               <div class="cod-panel" id="codPanel">
                  <div class="cod-info">
                     <i class="fas fa-motorcycle"></i>
                     <div>
                        <h4>Cash on Delivery (COD)</h4>
                        <p>Pembayaran dilakukan secara tunai saat pesanan tiba di alamat Anda. Siapkan uang pas sesuai total pesanan.</p>
                     </div>
                  </div>
               </div>

            </div>
         </div>

         <button type="submit" name="order_btn" class="btn-order">
            <i class="fas fa-lock"></i> Buat Pesanan Sekarang
         </button>

      </form>
   </div>

   <!-- RIGHT: Order Summary -->
   <div class="summary-sticky">
      <div class="display-order-new">
         <div class="doh">
            <div class="ch-icon"><i class="fas fa-receipt"></i></div>
            <h3>Ringkasan Pesanan</h3>
         </div>
         <div class="dob">
            <?php
               $grand_total = 0;
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               if(mysqli_num_rows($select_cart) > 0){
                  while($fetch_cart = mysqli_fetch_assoc($select_cart)){
                     $total_price  = ($fetch_cart['price'] * $fetch_cart['quantity']);
                     $grand_total += $total_price;
                     echo '<p>'.htmlspecialchars($fetch_cart['name'])
                        .'<span>Rp'.$fetch_cart['price'].' x '.$fetch_cart['quantity'].'</span></p>';
                  }
               }else{
                  echo '<p class="empty">your cart is empty</p>';
               }
            ?>
            <div class="grand-total-new">
               <span>grand total</span>
               <span>Rp<?php echo $grand_total; ?></span>
            </div>
         </div>
      </div>
   </div>

</div><!-- /.checkout-page -->

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
<script>
function switchPay(val){
   document.getElementById('methodSelect').value = val;
   var qp = document.getElementById('qrisPanel');
   var cp = document.getElementById('codPanel');
   var pf = document.getElementById('proofFile');
   if(val === 'QRIS'){
      qp.classList.add('show'); cp.classList.remove('show');
      pf.setAttribute('required','required');
   } else {
      cp.classList.add('show'); qp.classList.remove('show');
      pf.removeAttribute('required');
      document.getElementById('proof-preview').style.display = 'none';
   }
}

function previewProof(input){
   var wrap = document.getElementById('proof-preview');
   var img  = document.getElementById('previewImg');
   var nm   = document.getElementById('previewName');
   if(input.files && input.files[0]){
      var f = input.files[0];
      nm.textContent = f.name + '  (' + (f.size/1024).toFixed(1) + ' KB)';
      if(f.type.startsWith('image/')){
         var r = new FileReader();
         r.onload = function(e){ img.src = e.target.result; img.style.display = 'block'; };
         r.readAsDataURL(f);
      } else {
         img.style.display = 'none';
      }
      wrap.style.display = 'block';
      var uz = document.getElementById('uploadZone');
      uz.style.borderColor = 'var(--g400)';
      uz.style.background  = 'var(--g100)';
   }
}

/* Drag & Drop */
(function(){
   var uz = document.getElementById('uploadZone');
   if(!uz) return;
   uz.addEventListener('dragover', function(e){ e.preventDefault(); uz.classList.add('drag'); });
   uz.addEventListener('dragleave', function(){ uz.classList.remove('drag'); });
   uz.addEventListener('drop', function(e){
      e.preventDefault(); uz.classList.remove('drag');
      if(e.dataTransfer.files.length){
         document.getElementById('proofFile').files = e.dataTransfer.files;
         previewProof(document.getElementById('proofFile'));
      }
   });
})();

document.getElementById('coForm').addEventListener('submit', function(e){
   var method = document.getElementById('methodSelect').value;
   if(method === 'QRIS'){
      var pf = document.getElementById('proofFile');
      if(!pf.files || pf.files.length === 0){
         e.preventDefault();
         alert('Harap upload bukti pembayaran QRIS terlebih dahulu!');
      }
   }
});
</script>
</body>
</html>