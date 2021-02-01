<?php 

/* Template Name: simple product */

get_header();

?>
<section class="'container-fluid simple-product">
   <div class="container">
      <div class="page-index">
         <div>
            inicio/ cuadros / Minimalista/ <a href="#"> Xilografia minimal</a>
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
                  </div>
                  <div class="special">
                     <div class="hear-icon">
                     </div>
                     <div class="size-icon">
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