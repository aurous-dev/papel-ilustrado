<header class="container-fluid header">
   <?php if (get_field('global_bar_cta', 'option')) : ?>
      <div class="header__banner">
         <div><?php the_field('global_bar_cta', 'option'); ?></div>
      </div>
   <?php endif; ?>
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
               <a href="#" class='search-mobile'>
                  <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M14.4375 12.75H13.5487L13.2338 12.4462C14.3363 11.1637 15 9.49875 15 7.6875C15 3.64875 11.7262 0.375 7.6875 0.375C3.64875 0.375 0.375 3.64875 0.375 7.6875C0.375 11.7262 3.64875 15 7.6875 15C9.49875 15 11.1637 14.3363 12.4462 13.2338L12.75 13.5487V14.4375L18.375 20.0513L20.0513 18.375L14.4375 12.75ZM7.6875 12.75C4.88625 12.75 2.625 10.4888 2.625 7.6875C2.625 4.88625 4.88625 2.625 7.6875 2.625C10.4888 2.625 12.75 4.88625 12.75 7.6875C12.75 10.4888 10.4888 12.75 7.6875 12.75Z" fill="#355957"/>
                  </svg>
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M1.82183 0.655292C1.58975 0.655353 1.36295 0.724628 1.17044 0.854261C0.977926 0.983894 0.828449 1.16799 0.741115 1.38302C0.653782 1.59805 0.632561 1.83425 0.680166 2.0614C0.727771 2.28855 0.842038 2.49635 1.00836 2.65822L8.35016 10L1.00836 17.3418C0.896389 17.4493 0.806996 17.5781 0.745413 17.7206C0.683831 17.863 0.651298 18.0164 0.649719 18.1716C0.648139 18.3268 0.677546 18.4808 0.736217 18.6245C0.794888 18.7682 0.881643 18.8988 0.991401 19.0085C1.10116 19.1183 1.23172 19.205 1.37542 19.2637C1.51913 19.3224 1.6731 19.3518 1.82831 19.3502C1.98353 19.3486 2.13686 19.3161 2.27935 19.2545C2.42183 19.1929 2.5506 19.1035 2.6581 18.9916L9.9999 11.6498L17.3417 18.9916C17.4492 19.1035 17.578 19.1929 17.7204 19.2545C17.8629 19.3161 18.0163 19.3486 18.1715 19.3502C18.3267 19.3518 18.4807 19.3224 18.6244 19.2637C18.7681 19.205 18.8986 19.1183 19.0084 19.0085C19.1182 18.8988 19.2049 18.7682 19.2636 18.6245C19.3223 18.4808 19.3517 18.3268 19.3501 18.1716C19.3485 18.0164 19.316 17.863 19.2544 17.7206C19.1928 17.5781 19.1034 17.4493 18.9914 17.3418L11.6496 10L18.9914 2.65822C19.16 2.49435 19.2752 2.28337 19.3218 2.05293C19.3684 1.82249 19.3443 1.58334 19.2527 1.36682C19.1611 1.15031 19.0062 0.966523 18.8083 0.839556C18.6104 0.712588 18.3788 0.648362 18.1438 0.655292C17.8407 0.664324 17.553 0.790999 17.3417 1.00848L9.9999 8.35028L2.6581 1.00848C2.54938 0.896722 2.41936 0.807887 2.27572 0.747224C2.13209 0.686561 1.97775 0.655301 1.82183 0.655292Z" fill="#355957"/>
                  </svg>
               </a>
               <form role="search" method="get" action="<?php echo home_url('/');?>">
                  <input type="text" name="s" placeholder="Buscar producto" value="<?php echo get_search_query() ;?>">
                  
                  <!-- ESTE ES EL BOTÓN QUE EJECUTA EL FORM -->
                  <input type="submit" value="Buscar">
                  <!-- ESTE ES EL BOTÓN QUE EJECUTA EL FORM -->

                  <!-- Este A no debe ir, la lupa debe ser el fondo del botón -->
                  <a href="#">
                     <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.4375 12.75H13.5487L13.2338 12.4462C14.3363 11.1637 15 9.49875 15 7.6875C15 3.64875 11.7262 0.375 7.6875 0.375C3.64875 0.375 0.375 3.64875 0.375 7.6875C0.375 11.7262 3.64875 15 7.6875 15C9.49875 15 11.1637 14.3363 12.4462 13.2338L12.75 13.5487V14.4375L18.375 20.0513L20.0513 18.375L14.4375 12.75ZM7.6875 12.75C4.88625 12.75 2.625 10.4888 2.625 7.6875C2.625 4.88625 4.88625 2.625 7.6875 2.625C10.4888 2.625 12.75 4.88625 12.75 7.6875C12.75 10.4888 10.4888 12.75 7.6875 12.75Z" fill="#355957"/>
                     </svg>
                  </a>
                  <!-- Este A no debe ir, la lupa debe ser el fondo del botón -->
               </form>
            </div>
            <?php if (get_field('contacto_whatsapp', 'option')) : ?>
               <div class="header__menu--whatsapp">
                  <a href="https://wa.me/56<?php the_field('contacto_whatsapp', 'option'); ?>">
                     <img src="<?php echo get_template_directory_uri(); ?>/img/svg/whatsapp.svg" alt="">
                  </a>
               </div>
            <?php endif; ?>
            <div class="header__menu--icons">
               <a class="icon__heart" href="#">
                  <svg width="21" height="24" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0041 2.09065C11.7641 0.0306541 15.0241 -0.809346 17.6641 0.990654C19.0641 1.95065 19.9441 3.57065 20.0041 5.27065C20.1341 9.15065 16.7041 12.2607 11.4541 17.0207L11.3441 17.1207C10.5841 17.8207 9.41412 17.8207 8.65412 17.1307L8.55412 17.0407L8.49372 16.9858C3.27775 12.2468 -0.135343 9.14575 0.00411947 5.28065C0.0641195 3.57065 0.94412 1.95065 2.34412 0.990654C4.98412 -0.819346 8.24412 0.0306541 10.0041 2.09065ZM10.0041 15.6507L10.1041 15.5507C14.8641 11.2407 18.0041 8.39065 18.0041 5.50065C18.0041 3.50065 16.5041 2.00065 14.5041 2.00065C12.9641 2.00065 11.4641 2.99065 10.9441 4.36065H9.07412C8.54412 2.99065 7.04412 2.00065 5.50412 2.00065C3.50412 2.00065 2.00412 3.50065 2.00412 5.50065C2.00412 8.39065 5.14412 11.2407 9.90412 15.5507L10.0041 15.6507Z" fill="#355957" />
                  </svg>
                  <div class="icon-circle">1</div>
               </a>
               <a class="icon__cart" href="<?php echo wc_get_cart_url(); ?>">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path fill-rule="evenodd" clip-rule="evenodd" d="M16.6472 13.5H9.19717L8.09717 15.5H19.0972C19.6472 15.5 20.0972 15.95 20.0972 16.5C20.0972 17.05 19.6472 17.5 19.0972 17.5H8.09717C6.57717 17.5 5.61717 15.87 6.34717 14.53L7.69717 12.09L4.09717 4.5H3.09717C2.54717 4.5 2.09717 4.05 2.09717 3.5C2.09717 2.95 2.54717 2.5 3.09717 2.5H4.73717C5.11717 2.5 5.47717 2.72 5.63717 3.07L9.62717 11.5H16.6472L20.0372 5.37C20.2972 4.89 20.9072 4.72 21.3872 4.98C21.8672 5.25 22.0472 5.86 21.7772 6.34L18.3972 12.47C18.0572 13.09 17.3972 13.5 16.6472 13.5ZM8.09717 18.5C6.99717 18.5 6.10717 19.4 6.10717 20.5C6.10717 21.6 6.99717 22.5 8.09717 22.5C9.19717 22.5 10.0972 21.6 10.0972 20.5C10.0972 19.4 9.19717 18.5 8.09717 18.5ZM18.0972 18.5C16.9972 18.5 16.1072 19.4 16.1072 20.5C16.1072 21.6 16.9972 22.5 18.0972 22.5C19.1972 22.5 20.0972 21.6 20.0972 20.5C20.0972 19.4 19.1972 18.5 18.0972 18.5Z" fill="#355957" />
                  </svg>
                  <div class="icon-circle"><?php echo sprintf(_n('%d', '%d', WC()->cart->get_cart_contents_count()), WC()->cart->get_cart_contents_count()); ?></div>
               </a>
            </div>
            <?php
            global $current_user;
            $login = $current_user->user_login; //get current user id 
            $firstname = $current_user->user_firstname; //get current user id 
            if (is_user_logged_in()) : ?>
               <div class="header__menu--sesions">
                  <a href="/my-account">Hola, <b>
                     <?php
                        if($firstname) {
                           echo $firstname;
                        } else {
                           echo $login;
                        }
                     ;?></b>
                  </a>
               </div>
            <?php else : ?>
               <div class="header__menu--sesions">
                  <a href="/my-account">Iniciar sesión / Registrarse</a>
               </div>
            <?php endif; ?>
         </div>
         <div class="header__logo">
            <a href="<?php echo get_home_url(); ?>">
               <img src="<?php the_field('logo', 'option'); ?>" alt="<?php echo get_bloginfo('name'); ?>">
            </a>
         </div>
         <nav class="header__nav">
            <div class="menu-principal-container">
               <?php if (have_rows('menu_items', 'option')) : ?>
                  <ul id="menu-principal" class="menu">
                     <?php while (have_rows('menu_items', 'option')) : the_row(); ?>
                        <?php if (get_sub_field('tipo_de_item') == 'sub') {
                           $child = 'menu-item-has-children';
                        } else {
                           $child = '';
                        }; ?>
                        <li id="menu-item-22<?php echo get_row_index(); ?>" class="menu-item menu-item-type-custom menu-item-object-custom <?php echo $child; ?> menu-item-22<?php echo get_row_index(); ?>">
                           <a href="<?php the_sub_field('url'); ?>"><?php the_sub_field('titulo'); ?></a>
                           <?php if (get_sub_field('tipo_de_item') == 'sub') : ?>
                              <ul class="sub-menu">
                                 <?php if (have_rows('banner')) : ?>
                                    <?php while (have_rows('banner')) : the_row(); ?>
                                       <?php if (get_sub_field('mostrar_banner') == 'active') : ?>
                                          <div id="banner_cuadros">
                                             <a href="<?php the_sub_field('url_banner'); ?>">
                                                <img src="<?php the_sub_field('imagen_banner'); ?>">
                                             </a>
                                          </div>
                                       <?php endif; ?>
                                    <?php endwhile; ?>
                                 <?php endif; ?>
                                 <?php while (have_rows('sub-items')) : the_row(); ?>
                                    <li id="menu-item-22<?php echo get_row_index(); ?>" class="menu-item menu-item-type-custom menu-item-object-custom <?php echo $child; ?> menu-item-22<?php echo get_row_index(); ?>">
                                       <a href="<?php the_sub_field('url'); ?>"><?php the_sub_field('titulo'); ?></a>
                                       <?php if (have_rows('sub-items-sub')) : ?>
                                          <ul class="sub-menu">
                                             <?php while (have_rows('sub-items-sub')) : the_row(); ?>
                                                <li id="menu-item-24<?php echo get_row_index(); ?>" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-24<?php echo get_row_index(); ?>"><a href="<?php the_sub_field('url'); ?>"><?php the_sub_field('titulo'); ?></a></li>
                                             <?php endwhile; ?>
                                          </ul>
                                       <?php endif; ?>
                                    </li>
                                 <?php endwhile; ?>
                              </ul>
                           <?php endif; ?>
                        </li>
                     <?php endwhile; ?>
                  </ul>
               <?php endif; ?>
            <div class="header__nav--rrss">
               <a href="#">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/facebook.svg" alt="">
               </a>
               <a href="#">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/instagram.svg" alt="">
               </a>
               <a href="#">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/svg/whatsapp.svg" alt="">
               </a>
            </div>
         </nav>
      </div>
   </div>
</header>