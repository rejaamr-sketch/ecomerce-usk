<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home - Premium Book Store</title>

   <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
   <style>
      :root {
         --bg-color: #0d0d0d;
         --gold-primary: #c9a84c;
         --gold-light: #e8c96d;
         --gold-dark: #a07830;
         --gold-subtle: #f5e6c0;
         --surface: #161616;
         --surface-2: #1e1e1e;
         --text-primary: #f5f0e8;
         --text-muted: #9a8f7a;
         --white: #ffffff;
         --border: rgba(201, 168, 76, 0.2);
      }

      * {
         margin: 0; padding: 0;
         box-sizing: border-box;
         font-family: 'Plus Jakarta Sans', sans-serif;
         text-decoration: none;
      }

      body {
         background-color: var(--bg-color);
         color: var(--text-primary);
      }

      /* --- HERO SECTION --- */
      .hero {
         height: 90vh;
         position: relative;
         background: url('images/hero-books.jpg') center/cover no-repeat;
         display: flex;
         align-items: center;
         justify-content: center;
         text-align: center;
         padding: 0 20px;
         overflow: hidden;
      }

      .hero::before {
         content: '';
         position: absolute;
         inset: 0;
         background: linear-gradient(
            to bottom,
            rgba(0,0,0,0.75) 0%,
            rgba(13,13,13,0.6) 50%,
            rgba(13,13,13,1) 100%
         );
         z-index: 0;
      }

      .hero-content {
         position: relative;
         z-index: 1;
         max-width: 820px;
      }

      .hero-badge {
         display: inline-block;
         border: 1px solid var(--gold-primary);
         color: var(--gold-light);
         font-size: 0.8rem;
         font-weight: 600;
         letter-spacing: 3px;
         text-transform: uppercase;
         padding: 8px 22px;
         border-radius: 40px;
         margin-bottom: 28px;
         background: rgba(201, 168, 76, 0.08);
      }

      .hero-content h1 {
         font-size: clamp(2.6rem, 6vw, 4.8rem);
         font-weight: 800;
         margin-bottom: 22px;
         line-height: 1.1;
         letter-spacing: -1px;
         color: var(--white);
      }

      .hero-content h1 span {
         color: var(--gold-light);
      }

      .hero-content p {
         font-size: 1.15rem;
         margin-bottom: 44px;
         color: var(--text-muted);
         max-width: 560px;
         margin-left: auto;
         margin-right: auto;
         line-height: 1.7;
      }

      .btn-main {
         background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
         color: #0d0d0d;
         padding: 18px 48px;
         border-radius: 12px;
         font-weight: 800;
         font-size: 1rem;
         letter-spacing: 0.5px;
         transition: all 0.3s ease;
         display: inline-block;
         border: 1px solid var(--gold-light);
         box-shadow: 0 0 40px rgba(201, 168, 76, 0.2);
      }

      .btn-main:hover {
         background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
         transform: translateY(-4px);
         box-shadow: 0 12px 40px rgba(201, 168, 76, 0.35);
      }

      /* --- DIVIDER LINE --- */
      .section-divider {
         width: 60px;
         height: 2px;
         background: linear-gradient(90deg, var(--gold-primary), transparent);
         margin: 0 auto 16px;
      }

      /* --- FEATURES SECTION --- */
      .features-wrapper {
         padding: 100px 7%;
      }

      .features-label {
         text-align: center;
         color: var(--gold-primary);
         font-size: 0.78rem;
         font-weight: 700;
         letter-spacing: 3px;
         text-transform: uppercase;
         margin-bottom: 14px;
      }

      .features-title {
         text-align: center;
         font-size: clamp(1.8rem, 3vw, 2.5rem);
         font-weight: 800;
         color: var(--text-primary);
         margin-bottom: 60px;
      }

      .features {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
         gap: 24px;
      }

      .feature-box {
         background: var(--surface);
         border: 1px solid var(--border);
         padding: 48px 36px;
         border-radius: 20px;
         text-align: center;
         transition: 0.35s ease;
         position: relative;
         overflow: hidden;
      }

      .feature-box::before {
         content: '';
         position: absolute;
         top: 0; left: 0; right: 0;
         height: 2px;
         background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
         opacity: 0;
         transition: opacity 0.35s;
      }

      .feature-box:hover {
         transform: translateY(-8px);
         border-color: rgba(201, 168, 76, 0.4);
         background: var(--surface-2);
      }

      .feature-box:hover::before {
         opacity: 1;
      }

      .feature-icon {
         width: 72px;
         height: 72px;
         border-radius: 20px;
         background: rgba(201, 168, 76, 0.1);
         border: 1px solid rgba(201, 168, 76, 0.25);
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 28px;
      }

      .feature-icon i {
         font-size: 1.8rem;
         color: var(--gold-primary);
      }

      .feature-box h3 {
         margin-bottom: 14px;
         font-size: 1.25rem;
         font-weight: 700;
         color: var(--text-primary);
      }

      .feature-box p {
         color: var(--text-muted);
         font-size: 0.95rem;
         line-height: 1.7;
      }

      /* --- CTA SECTION --- */
      .cta-banner {
         margin: 0 7% 100px;
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: 28px;
         padding: 72px 60px;
         display: flex;
         align-items: center;
         justify-content: space-between;
         gap: 40px;
         position: relative;
         overflow: hidden;
      }

      .cta-banner::after {
         content: '';
         position: absolute;
         top: -80px; right: -80px;
         width: 320px; height: 320px;
         border-radius: 50%;
         background: radial-gradient(circle, rgba(201, 168, 76, 0.12) 0%, transparent 70%);
         pointer-events: none;
      }

      .cta-text h2 { 
         font-size: clamp(1.8rem, 3.5vw, 2.6rem); 
         font-weight: 800;
         margin-bottom: 12px;
         color: var(--text-primary);
      }

      .cta-text h2 span {
         color: var(--gold-light);
      }

      .cta-text p {
         color: var(--text-muted);
         font-size: 1rem;
         line-height: 1.6;
      }

      .btn-gold {
         background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
         color: #0d0d0d;
         padding: 18px 40px;
         border-radius: 14px;
         font-weight: 800;
         font-size: 1rem;
         letter-spacing: 0.3px;
         transition: 0.3s;
         white-space: nowrap;
         flex-shrink: 0;
         border: 1px solid var(--gold-light);
         display: inline-block;
      }

      .btn-gold:hover {
         background: linear-gradient(135deg, var(--gold-light), var(--gold-primary));
         transform: scale(1.04);
         box-shadow: 0 8px 30px rgba(201, 168, 76, 0.3);
      }

      @media (max-width: 768px) {
         .cta-banner { 
            flex-direction: column; 
            text-align: center; 
            padding: 52px 28px;
         }
         .hero { height: 80vh; }
         .features-wrapper { padding: 70px 5%; }
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
   <section class="hero">
      <div class="hero-content">
         <div class="hero-badge">✦ Premium Book Store</div>
         <h1>Satu Halaman Lagi, <br> <span>Satu Petualangan Baru.</span></h1>
         <p>Bawa pulang cerita dari penulis-penulis terbaik dunia. Koleksi original kami siap menemani waktu luang Anda.</p>
         <a href="shop.php" class="btn-main">Mulai Belanja Sekarang</a>
      </div>
   </section>

   <section class="features-wrapper">
      <p class="features-label">✦ Keunggulan Kami</p>
      <h2 class="features-title">Pengalaman Belanja Terbaik</h2>
      <div class="features">
         <div class="feature-box">
            <div class="feature-icon">
               <i class="fas fa-truck-fast"></i>
            </div>
            <h3>Pengiriman Prioritas</h3>
            <p>Paket Anda dikemas dengan aman dan dikirim dengan layanan tercepat hingga ke depan pintu.</p>
         </div>
         <div class="feature-box">
            <div class="feature-icon">
               <i class="fas fa-check-circle"></i>
            </div>
            <h3>100% Original</h3>
            <p>Kami menjamin keaslian setiap buku. Dukung penulis favorit Anda dengan membeli produk resmi.</p>
         </div>
         <div class="feature-box">
            <div class="feature-icon">
               <i class="fas fa-heart"></i>
            </div>
            <h3>Komunitas Pembaca</h3>
            <p>Bergabunglah dengan ribuan pecinta buku lainnya yang telah menemukan inspirasi di toko kami.</p>
         </div>
      </div>
   </section>

   <section class="cta-banner">
      <div class="cta-text">
         <h2>Jadi Bagian dari <span>Kami</span></h2>
         <p>Dapatkan informasi buku terbaru dan promo menarik lainnya.</p>
      </div>
      <a href="register.php" class="btn-gold">Daftar Akun Gratis</a>
   </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>