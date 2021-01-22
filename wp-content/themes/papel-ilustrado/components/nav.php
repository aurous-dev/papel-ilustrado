<header class="container-fluid header">
   <div class="header__banner">
      <div>Seguimos despachando a todo Chile - Plazo de envio 15 dias habiles </div>
   </div>
   <div class="container">
      <div class="header__container">
         <div class="header__menu">
            <!-- <div class="burger-button" id="burger-menu">
               <div class="menu-li">
                  <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                     <g fill="none" fill-rule="evenodd">
                           <path d="M13,26.5 L88,26.5"/>
                           <path d="M13,50.5 L88,50.5"/>
                           <path d="M13,50.5 L88,50.5"/>
                           <path d="M13,74.5 L88,74.5"/>
                     </g>
                  </svg>  
               </div>
            </div>  -->
            <div class="header__menu--search">
               <input type="text" placeholder="Buscar producto">
               <i class="fas fa-search"></i>
            </div>
            <div class="header__menu--whatsapp">
               <a href="#">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/whatsapp.svg" alt="">
               </a>
            </div>
            <div class="header__menu--icons">
               <a href="#">
                  <i class="far fa-heart icon__heart"></i>
                  <div class="icon__heart-circle">1</div>
               </a>
               <a href="<?php echo wc_get_cart_url(); ?>">
                  <i class="fas fa-shopping-cart icon__cart"></i>
                  <div class="icon__cart-circle"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></div>
               </a>
            </div>
            <div class="header__menu--sesions">
               <a href="#">Iniciar sesión / Registrarse</a>
            </div>
         </div>
         <div class="header__logo">
            <img src="<?php echo get_template_directory_uri(); ?>/img/png/logo.png" alt="">
         </div>
         <nav class="header__nav">
            <?php wp_nav_menu(); ?>
         </nav>
      </div>
   </div>
</header>
