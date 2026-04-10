<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['send'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $number = $_POST['number'];
   $msg = mysqli_real_escape_string($conn, $_POST['message']);

   $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('query failed');

   if(mysqli_num_rows($select_message) > 0){
      $message[] = 'message sent already!';
   }else{
      mysqli_query($conn, "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')") or die('query failed');
      $message[] = 'message sent successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* ── Page ── */
      .contact-page {
         background: #f7f5f0;
         min-height: 100vh;
         padding-bottom: 4rem;
      }

      /* ── Breadcrumb ── */
      .contact-breadcrumb {
         background: #fff;
         border-bottom: 0.5px solid rgba(0,0,0,.08);
         padding: .7rem 1.5rem;
         font-size: 12px;
         color: #aaa;
      }
      .contact-breadcrumb a {
         color: #B8860B;
         text-decoration: none;
      }
      .contact-breadcrumb a:hover { text-decoration: underline; }

      /* ── Alert ── */
      .gold-alert {
         background: #FFFBE6;
         border: 0.5px solid rgba(218,165,32,.4);
         border-radius: 8px;
         padding: .75rem 1rem;
         font-size: 13px;
         color: #856404;
         margin-bottom: 1rem;
         display: flex;
         align-items: center;
         gap: .5rem;
      }
      .gold-alert i { color: #B8860B; font-size: 13px; }

      /* ── Two-column layout ── */
      .contact-wrap {
         max-width: 960px;
         margin: 2.5rem auto;
         padding: 0 1.25rem;
         display: grid;
         grid-template-columns: 1fr 1.6fr;
         gap: 1.25rem;
         align-items: start;
      }
      @media (max-width: 680px) {
         .contact-wrap { grid-template-columns: 1fr; }
      }

      /* ── Gold accent bar (shared) ── */
      .contact-info-card::before,
      .contact-form-card::before {
         content: '';
         position: absolute;
         top: 0; left: 0; right: 0;
         height: 3px;
         background: linear-gradient(90deg, #B8860B, #DAA520, #FFD700, #DAA520, #B8860B);
      }

      /* ── Info card ── */
      .contact-info-card {
         background: #fff;
         border: 0.5px solid rgba(184,134,11,.2);
         border-radius: 14px;
         padding: 1.75rem;
         position: relative;
         overflow: hidden;
      }
      .contact-info-card h2 {
         font-size: 17px;
         font-weight: 500;
         color: #1a1a1a;
         margin-bottom: .3rem;
      }
      .contact-info-card .sub {
         font-size: 13px;
         color: #999;
         line-height: 1.65;
         margin-bottom: 1.5rem;
      }
      .info-row {
         display: flex;
         align-items: flex-start;
         gap: .85rem;
         margin-bottom: 1.1rem;
      }
      .info-icon {
         width: 36px;
         height: 36px;
         border-radius: 9px;
         background: #FFF8E1;
         border: 0.5px solid rgba(184,134,11,.2);
         display: flex;
         align-items: center;
         justify-content: center;
         flex-shrink: 0;
      }
      .info-icon i { font-size: 14px; color: #B8860B; }
      .info-label {
         font-size: 11px;
         color: #B8860B;
         font-weight: 500;
         letter-spacing: .5px;
         text-transform: uppercase;
      }
      .info-val { font-size: 13px; color: #444; margin-top: 2px; }

      /* Socials */
      .contact-socials {
         display: flex;
         gap: .6rem;
         margin-top: 1.4rem;
         padding-top: 1.2rem;
         border-top: 0.5px solid rgba(184,134,11,.15);
      }
      .soc-btn {
         width: 35px;
         height: 35px;
         border-radius: 9px;
         background: #FFF8E1;
         border: 0.5px solid rgba(184,134,11,.2);
         display: flex;
         align-items: center;
         justify-content: center;
         text-decoration: none;
         transition: background .15s;
      }
      .soc-btn:hover { background: #FFE082; }
      .soc-btn i { font-size: 14px; color: #B8860B; }

      /* ── Form card ── */
      .contact-form-card {
         background: #fff;
         border: 0.5px solid rgba(184,134,11,.2);
         border-radius: 14px;
         padding: 1.75rem;
         position: relative;
         overflow: hidden;
      }
      .contact-form-card .form-title {
         font-size: 15px;
         font-weight: 500;
         color: #1a1a1a;
         margin-bottom: 1.2rem;
      }
      .contact-form-grid {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: .85rem;
      }
      @media (max-width: 480px) {
         .contact-form-grid { grid-template-columns: 1fr; }
      }
      .cf-group { display: flex; flex-direction: column; gap: 5px; }
      .cf-group.full { grid-column: 1 / -1; }

      .cf-group label {
         font-size: 11.5px;
         font-weight: 500;
         color: #B8860B;
         letter-spacing: .3px;
         text-transform: uppercase;
      }
      .cf-group input,
      .cf-group textarea {
         border: 0.5px solid rgba(184,134,11,.3);
         border-radius: 8px;
         font-size: 13px;
         color: #1a1a1a;
         outline: none;
         font-family: inherit;
         background: #FFFDF5;
         width: 100%;
         transition: border-color .15s, box-shadow .15s;
      }
      .cf-group input {
         height: 40px;
         padding: 0 12px;
      }
      .cf-group textarea {
         padding: 10px 12px;
         resize: vertical;
         min-height: 115px;
      }
      .cf-group input:focus,
      .cf-group textarea:focus {
         border-color: #DAA520;
         box-shadow: 0 0 0 3px rgba(218,165,32,.12);
         background: #fff;
      }
      .cf-group input::placeholder,
      .cf-group textarea::placeholder { color: #ccc; }

      /* Submit */
      .contact-submit {
         width: 100%;
         height: 44px;
         background: linear-gradient(135deg, #B8860B, #DAA520);
         color: #fff;
         border: none;
         border-radius: 9px;
         font-size: 14px;
         font-weight: 500;
         cursor: pointer;
         font-family: inherit;
         letter-spacing: .2px;
         transition: opacity .15s, transform .1s;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         gap: .5rem;
      }
      .contact-submit:hover { opacity: .9; }
      .contact-submit:active { transform: scale(.99); }
      .contact-submit i { font-size: 13px; }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="contact-page">

   <div class="contact-breadcrumb">
      <a href="home.php">home</a> &rsaquo; contact
   </div>

   <section class="contact">
      <div class="contact-wrap">

         <!-- Info card -->
         <div class="contact-info-card">
            <h2>Hubungi Kami</h2>
            <p class="sub">Ada pertanyaan atau masukan? Kami senang mendengarnya.</p>

            <div class="info-row">
               <div class="info-icon"><i class="fas fa-phone"></i></div>
               <div>
                  <div class="info-label">Telepon</div>
                  <div class="info-val">+6285779747881</div>
               </div>
            </div>
            <div class="info-row">
               <div class="info-icon"><i class="fas fa-envelope"></i></div>
               <div>
                  <div class="info-label">Email</div>
                  <div class="info-val">rejaamr@gmail.com</div>
               </div>
            </div>
            <div class="info-row">
               <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
               <div>
                  <div class="info-label">Lokasi</div>
                  <div class="info-val">Jakarta, Indonesia</div>
               </div>
            </div>

            <div class="contact-socials">
               <a href="https://x.com/jaejaaaaak" target="_blank" class="soc-btn"><i class="fab fa-twitter"></i></a>
               <a href="https://www.instagram.com/ejaxnd/" target="_blank" class="soc-btn"><i class="fab fa-instagram"></i></a>
            </div>
         </div>

         <!-- Form card -->
         <div class="contact-form-card">
            <p class="form-title">Kirim Pesan</p>

            <?php
            if(isset($message)){
               foreach($message as $msg_item){
                  echo '
                  <div class="gold-alert">
                     <i class="fas fa-info-circle"></i>
                     '.htmlspecialchars($msg_item).'
                  </div>';
               }
            }
            ?>

            <form action="" method="post">
               <div class="contact-form-grid">

                  <div class="cf-group">
                     <label for="c-name">Nama</label>
                     <input type="text" id="c-name" name="name" required placeholder="Nama lengkap anda">
                  </div>
                  <div class="cf-group">
                     <label for="c-number">Nomor</label>
                     <input type="number" id="c-number" name="number" required placeholder="08xx xxxx xxxx">
                  </div>
                  <div class="cf-group full">
                     <label for="c-email">Email</label>
                     <input type="email" id="c-email" name="email" required placeholder="email@contoh.com">
                  </div>
                  <div class="cf-group full">
                     <label for="c-msg">Pesan</label>
                     <textarea id="c-msg" name="message" required placeholder="Tulis pesan anda di sini..."></textarea>
                  </div>
                  <div class="cf-group full">
                     <button type="submit" name="send" class="contact-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                     </button>
                  </div>

               </div>
            </form>
         </div>

      </div>
   </section>

</div>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>