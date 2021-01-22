<header class="container-fluid header">
   <?php if(get_field('global_bar_cta', 'option')) :?>
   <div class="header__banner">
      <div><?php the_field('global_bar_cta', 'option');?></div>
   </div>
   <?php endif;?>
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
               <a class="icon__heart" href="#">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/heart.svg" alt="">
                  <div class="icon__heart-circle">1</div>
               </a>
               <a class="icon__cart" href="<?php echo wc_get_cart_url(); ?>">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/cart.svg" alt="">
                  <div class="icon__cart-circle"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></div>
               </a>
            </div>
            <div class="header__menu--sesions">
               <a href="#">Iniciar sesión / Registrarse</a>
            </div>
         </div>
         <div class="header__logo">
            <a href="<?php echo get_home_url();?>">
               <!-- <img src="<?php echo get_template_directory_uri(); ?>/img/svg/logo.svg" alt=""> -->
               <img src="<?php the_field('logo', 'option');?>" alt="<?php echo get_bloginfo('name');?>">
            </a>
         </div>
         <nav class="header__nav">
            <?php wp_nav_menu(); ?>
         </nav>
      </div>
   </div>
</header>
