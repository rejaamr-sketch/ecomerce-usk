<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      :root {
         --gold:        #c9a84c;
         --gold-light:  #f5e9c8;
         --gold-dark:   #a07830;
         --gold-subtle: #fdf6e3;
         --text-dark:   #1a1108;
         --text-mid:    #6b5e3e;
         --text-muted:  #9d8c6a;
         --bg:          #faf7f0;
         --surface:     #ffffff;
         --border:      rgba(201,168,76,.2);
         --radius:      14px;
         --shadow:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
         --shadow-md:   0 6px 20px rgba(0,0,0,.08);
      }

      /* ── Page shell ── */
      .about-page {
         background: var(--bg);
         min-height: 100vh;
         padding-bottom: 5rem;
      }

      /* ── Breadcrumb ── */
      .about-breadcrumb {
         background: var(--surface);
         border-bottom: 1px solid var(--border);
         padding: .8rem 2rem;
         font-size: 13px;
         color: var(--text-muted);
         display: flex;
         align-items: center;
         gap: .4rem;
      }
      .about-breadcrumb a {
         color: var(--gold-dark);
         text-decoration: none;
         font-weight: 500;
         transition: color .15s;
      }
      .about-breadcrumb a:hover {
         color: var(--gold);
         text-decoration: underline;
         text-underline-offset: 3px;
      }
      .about-breadcrumb .sep {
         color: var(--text-muted);
         font-size: 11px;
      }

      /* ── Main two-column layout ── */
      .about-wrap {
         max-width: 1100px;
         margin: 3.5rem auto;
         padding: 0 2rem;
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 5rem;
         align-items: center;
      }
      @media (max-width: 720px) {
         .about-wrap { grid-template-columns: 1fr; gap: 2.5rem; }
      }

      /* ── Image block ── */
      .about-img-wrap {
         border-radius: var(--radius);
         overflow: hidden;
         aspect-ratio: 4 / 3;
         background: var(--gold-light);
         box-shadow: var(--shadow-md);
         border: 1px solid var(--border);
         position: relative;
      }
      .about-img-wrap::after {
         content: '';
         position: absolute;
         inset: 0;
         border-radius: var(--radius);
         box-shadow: inset 0 0 0 1px rgba(201,168,76,.15);
      }
      .about-img-wrap img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         display: block;
         transition: transform .4s ease;
      }
      .about-img-wrap:hover img {
         transform: scale(1.03);
      }

      /* ── Content block ── */
      .about-content {
         display: flex;
         flex-direction: column;
         gap: .2rem;
      }

      .about-tag {
         display: inline-flex;
         align-items: center;
         gap: .4rem;
         font-size: 11px;
         font-weight: 600;
         letter-spacing: 1px;
         text-transform: uppercase;
         color: var(--gold-dark);
         background: var(--gold-light);
         padding: 5px 14px;
         border-radius: 999px;
         margin-bottom: .8rem;
         width: fit-content;
         border: 1px solid rgba(201,168,76,.3);
      }

      .about-title {
         font-size: 26px;
         font-weight: 600;
         color: var(--text-dark);
         line-height: 1.35;
         margin-bottom: 1rem;
         letter-spacing: -.02em;
      }

      .about-divider {
         width: 40px;
         height: 3px;
         background: var(--gold);
         border-radius: 999px;
         margin-bottom: 1.2rem;
      }

      .about-text {
         font-size: 15px;
         color: var(--text-mid);
         line-height: 1.8;
         margin-bottom: 1.8rem;
      }

      .about-btn {
         display: inline-flex;
         align-items: center;
         gap: .6rem;
         padding: .75rem 1.6rem;
         background: var(--gold);
         color: #fff;
         border-radius: 10px;
         font-size: 14px;
         font-weight: 500;
         text-decoration: none;
         transition: background .15s, transform .15s, box-shadow .15s;
         width: fit-content;
         box-shadow: 0 2px 8px rgba(201,168,76,.35);
      }
      .about-btn:hover {
         background: var(--gold-dark);
         transform: translateY(-1px);
         box-shadow: 0 4px 14px rgba(201,168,76,.4);
      }
      .about-btn i { font-size: 13px; }

      /* ── Stats row ── */
      .about-stats {
         max-width: 1100px;
         margin: 0 auto 3rem;
         padding: 0 2rem;
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 1.2rem;
      }
      @media (max-width: 520px) {
         .about-stats { grid-template-columns: 1fr; }
      }

      .stat-card {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: var(--radius);
         padding: 1.8rem 1.6rem;
         box-shadow: var(--shadow);
         position: relative;
         overflow: hidden;
         transition: box-shadow .2s, transform .2s;
      }
      .stat-card:hover {
         box-shadow: var(--shadow-md);
         transform: translateY(-2px);
      }
      .stat-card::before {
         content: '';
         position: absolute;
         top: 0; left: 0; right: 0;
         height: 3px;
         background: var(--gold);
      }

      .stat-icon {
         width: 36px;
         height: 36px;
         background: var(--gold-subtle);
         border-radius: 9px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: var(--gold-dark);
         font-size: 15px;
         margin-bottom: 1rem;
         border: 1px solid var(--border);
      }

      .stat-num {
         font-size: 28px;
         font-weight: 600;
         color: var(--gold-dark);
         margin-bottom: 4px;
         letter-spacing: -.02em;
         line-height: 1;
      }
      .stat-label {
         font-size: 13px;
         color: var(--text-muted);
         font-weight: 400;
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="about-page">

   <!-- Breadcrumb -->
   <div class="about-breadcrumb">
      <a href="home.php"><i class="fas fa-home" style="font-size:11px"></i> Home</a>
      <span class="sep">›</span>
      <span>About</span>
   </div>

   <!-- Main section -->
   <section class="about">
      <div class="about-wrap">

         <div class="about-img-wrap">
            <img src="images/buku.jpg" alt="about us">
         </div>

         <div class="about-content">
            <span class="about-tag">
               <i class="fas fa-book-open" style="font-size:10px"></i>
               Tentang Kami
            </span>
            <h3 class="about-title">Kenapa memilih e-commerce buku?</h3>
            <div class="about-divider"></div>
            <p class="about-text">Selain tutorialnya cukup banyak di youtube, kami memilih projek ini karena tidak terlalu sulit untuk dibuat dan juga sudah pernah membuatnya di tugas sebelumnya walau tidak sekompleks yang sekarang. Kami memilih projek yang mudah juga karena agar bisa membagi waktu untuk belajar. Dan jangan lupa beri masukan :></p>
            <a href="contact.php" class="about-btn">
               <i class="fas fa-envelope"></i> Contact us
            </a>
         </div>

      </div>
   </section>

   <!-- Stats row -->
   <div class="about-stats">
      <div class="stat-card">
         <div class="stat-icon"><i class="fas fa-books"></i></div>
         <div class="stat-num">500+</div>
         <div class="stat-label">Koleksi buku tersedia</div>
      </div>
      <div class="stat-card">
         <div class="stat-icon"><i class="fas fa-users"></i></div>
         <div class="stat-num">1.2k</div>
         <div class="stat-label">Pelanggan aktif</div>
      </div>
      <div class="stat-card">
         <div class="stat-icon"><i class="fas fa-headset"></i></div>
         <div class="stat-num">24/7</div>
         <div class="stat-label">Layanan pelanggan</div>
      </div>
   </div>

   <!-- Authors section (kept as-is for future use) -->
   <section class="authors"></section>

</div>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>