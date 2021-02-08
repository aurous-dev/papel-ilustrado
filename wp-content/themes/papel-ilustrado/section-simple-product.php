<?php 

/* Template Name: simple product */

get_header();

?>
<section class="'container-fluid simple-product">
   <div class="container">
      <div class="page-index">
         <div>
            Inicio/ Cuadros / Minimalista/ <a href="#"> Xilografia minimal</a>
         </div>
      </div>
      <div class="row simple-product__container">
         <div class="col-md-6">
            <div class="slider-for">
               <div class="big-img">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
               </div>
               <div class="big-img">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/png/hero3.png" alt="">
               </div>
               <div class="big-img">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/png/hero4.png" alt="">
               </div>
            </div>
            <div class="slider-nav">
               <div class="small-img">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
               </div>
               <div class="small-img">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/png/hero3.png" alt="">
               </div>
               <div class="small-img">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/png/hero4.png" alt="">
               </div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="simple-product__title">
               <h2>Xilografía minimal</h2>
               <div>Cuadro impreso en xilografía de flora y fauna. Este cuadro este cuadro es parte de la serie Xilografía minimal</div>
            </div>
            <div class="simple-product__price">
               <div>Desde <span>$20.000</span></div>
            </div>
            <div class="simple-product__size">
               <div class="simple-product__size--form">
                  <div class="form__size">
                     <div class="form__title">
                        Tamaño
                     </div>
                     <div class="form__select">
                        <select name="#" id="#">
                           <option value="desfualt">30X30 CM</option>
                        </select>
                     </div>
                  </div>
                  <div class="form__size">
                     <div class="form__title">
                        Marco
                     </div>
                     <div class="form__select">
                        <select name="#" id="#">
                           <option value="desfualt"> Negro </option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="simple-product__size--btn">
                  <div class="available">
                     <div class="check">
                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M4.03113 7.27597L2.44071 5.68555C2.35508 5.59973 2.23883 5.5515 2.11759 5.5515C1.99635 5.5515 1.88009 5.59973 1.79446 5.68555C1.61571 5.8643 1.61571 6.15305 1.79446 6.3318L3.7103 8.24764C3.88905 8.42639 4.1778 8.42639 4.35655 8.24764L9.20571 3.39847C9.38446 3.21972 9.38446 2.93097 9.20571 2.75222C9.12008 2.6664 9.00383 2.61816 8.88259 2.61816C8.76135 2.61816 8.64509 2.6664 8.55946 2.75222L4.03113 7.27597Z" fill="white"/>
                        </svg>
                     </div>
                     <div>Disponible</div>
                  </div>
                  <div class="special">
                     <div class="heart-icon">
                        <a href="#">
                           <svg width="21" height="20" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M12.3497 17.3072C11.5897 17.9972 10.4197 17.9972 9.65967 17.2972L9.54966 17.1972C4.29966 12.4472 0.869665 9.33723 0.999665 5.45723C1.05966 3.75723 1.92966 2.12723 3.33966 1.16723C5.97966 -0.632771 9.23967 0.207229 10.9997 2.26723C12.7597 0.207229 16.0197 -0.642771 18.6597 1.16723C20.0697 2.12723 20.9397 3.75723 20.9997 5.45723C21.1397 9.33723 17.6997 12.4472 12.4497 17.2172L12.3497 17.3072Z" fill="#F49A7E"/>
                           </svg>
                           Guardar
                        </a>
                     </div>
                     <div class="size-icon">
                        <a href="#">
                           <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M14 0H12V2H14V0ZM22 8H20V10H22V8ZM22 12H20V14H22V12ZM22 16H20V18C21 18 22 17 22 16ZM20 4H22V6H20V4ZM20 2V0C21 0 22 1 22 2H20ZM0 4H2V6H0V4ZM18 0H16V2H18V0ZM16 16H18V18H16V16ZM0 2C0 1 1 0 2 0V2H0ZM8 0H10V2H8V0ZM6 0H4V2H6V0ZM0 8V16C0 17.1 0.9 18 2 18H14V10C14 8.9 13.1 8 12 8H0ZM4.12 13.28L2.63 15.19C2.37 15.52 2.61 16 3.02 16.01H11C11.41 16.01 11.65 15.54 11.4 15.21L9.18 12.25C8.99 11.98 8.59 11.98 8.39 12.24L6.29 14.94L4.9 13.27C4.69 13.02 4.32 13.03 4.12 13.28Z" fill="#2A3D42"/>
                           </svg>
                           Guía de tamaños
                        </a>
                     </div>
                     <div class="marco-icon">
                        <a href="#">
                           Probar Marco
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="simple-product__btn">
               <a href="#" class="add"> Añadir al Carro</a>
               <a href="#" class="buy"> Comprar la serie</a>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- SECOND SECTION  -->
<section class="container-fluid paiting">
   <?php get_template_part('components/single/title');?>
   <div class="container">
      <div class="paiting__slider">
      <?php get_template_part('components/group/card-paiting');?>
      </div>
   </div>
</section>
<!-- SECOND SECTION  -->
<!-- THIRD SECTION  -->
<section class="container-fluid composition">
   <?php get_template_part('components/single/title');?>
   <div class="container">
      <div class="composition__slider">
         <a href="#" class="big-card" data-aos="fade-up" data-aos-duration="1000">
            <div>
               <div class="big-card__container" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/picture3.png');">
                  <?php get_template_part('components/single/card-special');?>
               </div>
            </div>
         </a>
         <a href="#" class="big-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <div>
               <div class="big-card__container" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/picture3.png');">
                  <?php get_template_part('components/single/card-special');?>
               </div>
            </div>
         </a>
         <a href="#" class="big-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <div class="big-card__container" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/picture3.png');">
               <?php get_template_part('components/single/card-special');?>
            </div>
         </a>
      </div>
   </div>
</section>
<!-- THIRD SECTION  -->
<!-- FOURTH SECTION  -->
<section class="section__news">
   <div class="container">
      <div class="section__news--title">
         <h2>Novedades</h2>
      </div>
      <div class="fiveColumn__slider">
         <?php get_template_part('components/group/card-picture');?>
      </div>
   </div>
</section>
<!-- FOURTH SECTION  -->

<?php get_footer();?>