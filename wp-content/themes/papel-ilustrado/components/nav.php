<header class="container-fluid header">
   <div class="header__banner">
      <a href="#"> Seguimos despachando a todo Chile - Plazo de envio 15 dias habiles </a>
   </div>
   <div class="container">
      <div class="header__container">
         <div class="header__menu">
            <div class="burger-button" id="burger-menu">
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
            </div> 
            <div class="header__menu--search">
               <form action="">
                  <input type="text" placeholder="Buscar producto">
                  <a href="#">
                     <i class="fas fa-search"></i>
                  </a>
               </form>
            </div>
            <div class="header__menu--whatsapp">
               <a href="#">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/whatsapp.svg" alt="">
               </a>
            </div>
            <div class="header__menu--icons">
               <a class="icon__heart" href="#">
                  <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0041 2.09065C11.7641 0.0306541 15.0241 -0.809346 17.6641 0.990654C19.0641 1.95065 19.9441 3.57065 20.0041 5.27065C20.1341 9.15065 16.7041 12.2607 11.4541 17.0207L11.3441 17.1207C10.5841 17.8207 9.41412 17.8207 8.65412 17.1307L8.55412 17.0407L8.49372 16.9858C3.27775 12.2468 -0.135343 9.14575 0.00411947 5.28065C0.0641195 3.57065 0.94412 1.95065 2.34412 0.990654C4.98412 -0.819346 8.24412 0.0306541 10.0041 2.09065ZM10.0041 15.6507L10.1041 15.5507C14.8641 11.2407 18.0041 8.39065 18.0041 5.50065C18.0041 3.50065 16.5041 2.00065 14.5041 2.00065C12.9641 2.00065 11.4641 2.99065 10.9441 4.36065H9.07412C8.54412 2.99065 7.04412 2.00065 5.50412 2.00065C3.50412 2.00065 2.00412 3.50065 2.00412 5.50065C2.00412 8.39065 5.14412 11.2407 9.90412 15.5507L10.0041 15.6507Z" fill="#355957"/>
                  </svg>
                  <div class="icon-circle">1</div>
               </a>
               <a class="icon__cart" href="#">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6472 13.5H9.19717L8.09717 15.5H19.0972C19.6472 15.5 20.0972 15.95 20.0972 16.5C20.0972 17.05 19.6472 17.5 19.0972 17.5H8.09717C6.57717 17.5 5.61717 15.87 6.34717 14.53L7.69717 12.09L4.09717 4.5H3.09717C2.54717 4.5 2.09717 4.05 2.09717 3.5C2.09717 2.95 2.54717 2.5 3.09717 2.5H4.73717C5.11717 2.5 5.47717 2.72 5.63717 3.07L9.62717 11.5H16.6472L20.0372 5.37C20.2972 4.89 20.9072 4.72 21.3872 4.98C21.8672 5.25 22.0472 5.86 21.7772 6.34L18.3972 12.47C18.0572 13.09 17.3972 13.5 16.6472 13.5ZM8.09717 18.5C6.99717 18.5 6.10717 19.4 6.10717 20.5C6.10717 21.6 6.99717 22.5 8.09717 22.5C9.19717 22.5 10.0972 21.6 10.0972 20.5C10.0972 19.4 9.19717 18.5 8.09717 18.5ZM18.0972 18.5C16.9972 18.5 16.1072 19.4 16.1072 20.5C16.1072 21.6 16.9972 22.5 18.0972 22.5C19.1972 22.5 20.0972 21.6 20.0972 20.5C20.0972 19.4 19.1972 18.5 18.0972 18.5Z" fill="#355957"/>
                  </svg>
                  <div class="icon-circle">0</div>
               </a>
            </div>
            <div class="header__menu--sesions">
               <a href="#">Iniciar sesión / Registrarse</a>
            </div>
         </div>
         <div class="header__logo">
            <a href="#">
               <img src="<?php echo get_template_directory_uri(); ?>/img/svg/logo.svg" alt="">
            </a>
         </div>
         <nav class="header__nav">
            <?php wp_nav_menu(); ?>
         </nav>
      </div>
   </div>
</header>
