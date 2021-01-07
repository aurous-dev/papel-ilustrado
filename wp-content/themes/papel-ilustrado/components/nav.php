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
               <img src="#" alt="">
            </div>
            <div class="header__menu--icons">
               <a href="#">
                  <i class="far fa-heart icon__heart"></i>
               </a>
               <a href="#">
                  <i class="fas fa-shopping-cart icon__cart"></i>
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