<style>
/* ── Footer ── */
.ftr {
   background: #0f0e0b;
   color: #aaa;
   padding: 3rem 1.5rem 0;
}
.ftr-grid {
   max-width: 1100px;
   margin: 0 auto;
   display: grid;
   grid-template-columns: repeat(4, 1fr);
   gap: 2rem;
}
@media (max-width: 768px) {
   .ftr-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 420px) {
   .ftr-grid { grid-template-columns: 1fr; }
}
.ftr-col h3 {
   font-size: 11px;
   font-weight: 500;
   color: #DAA520;
   letter-spacing: 1px;
   text-transform: uppercase;
   margin-bottom: 1rem;
   padding-bottom: .6rem;
   border-bottom: 0.5px solid rgba(218,165,32,.2);
}
.ftr-col a {
   display: flex;
   align-items: center;
   gap: .5rem;
   font-size: 13px;
   color: #777;
   text-decoration: none;
   margin-bottom: .55rem;
   transition: color .15s;
}
.ftr-col a:hover { color: #DAA520; }
.ftr-col a i, .ftr-col p i {
   font-size: 12px;
   color: #B8860B;
   width: 14px;
   text-align: center;
}
.ftr-col p {
   display: flex;
   align-items: center;
   gap: .55rem;
   font-size: 13px;
   color: #777;
   margin-bottom: .6rem;
}
.ftr-bottom {
   max-width: 1100px;
   margin: 2.5rem auto 0;
   padding: 1rem 0;
   border-top: 0.5px solid rgba(218,165,32,.15);
   display: flex;
   justify-content: space-between;
   align-items: center;
}
.ftr-credit {
   font-size: 12px;
   color: #444;
}
.ftr-credit span { color: #DAA520; }
.ftr-brand {
   font-size: 14px;
   font-weight: 500;
   color: #555;
   letter-spacing: -.3px;
}
.ftr-brand span { color: #DAA520; }
</style>

<footer class="ftr">

   <div class="ftr-grid">

      <div class="ftr-col">
         <h3>Quick links</h3>
         <a href="home.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Home</a>
         <a href="about.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> About</a>
         <a href="shop.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Shop</a>
         <a href="contact.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Contact</a>
      </div>

      <div class="ftr-col">
         <h3>Extra links</h3>
         <a href="login.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Login</a>
         <a href="register.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Register</a>
         <a href="cart.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Cart</a>
         <a href="orders.php"><i class="fas fa-chevron-right" style="font-size:9px"></i> Orders</a>
      </div>

      <div class="ftr-col">
         <h3>Contact info</h3>
         <p><i class="fas fa-phone"></i> +6285779747881</p>
         <p><i class="fas fa-envelope"></i> rejaamr@gmail.com</p>
         <p><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</p>
      </div>

      <div class="ftr-col">
         <h3>Follow us</h3>
         <a href="https://x.com/jaejaaaaak" target="_blank"><i class="fab fa-twitter"></i> Twitter</a>
         <a href="https://www.instagram.com/ejaxnd/" target="_blank"><i class="fab fa-instagram"></i> Instagram</a>
      </div>

   </div>

   <div class="ftr-bottom">
      <p class="ftr-credit">&copy; copyright @ <?php echo date('Y'); ?> by <span>Kelompok</span></p>
      <p class="ftr-brand">Read<span>World.</span></p>
   </div>

</footer>