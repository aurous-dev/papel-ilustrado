<?php 

/* Template Name: Home Page */

get_header();

?>

<!-- FIRST SECTION  -->
<section class="container-fluid hero">
   <div class="container">
      <div class="hero__container">
         <div class="hero__slider">
            <?php get_template_part('components/group/hero-slider');?>
         </div>
         <div class="row hero__card" >
            <div class="col-md-4 col-12 hero__card--container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero2.png');">
               <?php get_template_part('components/single/principal-btn');?>
            </div>
            <div class="col-md-4 col-12 hero__card--container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero3.png');">
               <?php get_template_part('components/single/principal-btn');?>
            </div>
            <div class="col-md-4 col-12 hero__card--container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="900" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero4.png');">
               <?php get_template_part('components/single/principal-btn');?>
            </div>   
         </div>
      </div>
   </div>
</section>
<!-- FIRST SECTION  -->
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
               <div class="big-card__container">
                  <div class="big-card__container--img" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/picture3.png');"></div>
                  <?php get_template_part('components/single/card-special');?>
               </div>
            </div>
         </a>
         <a href="#" class="big-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <div>
               <div class="big-card__container">
                  <div class="big-card__container--img" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/picture3.png');"></div>
                  <?php get_template_part('components/single/card-special');?>
               </div>
            </div>
         </a>
         <a href="#" class="big-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <div class="big-card__container">
               <div class="big-card__container--img" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/picture3.png');"></div>
               <?php get_template_part('components/single/card-special');?>
            </div>
         </a>
      </div>
   </div>
</section>
<!-- THIRD SECTION  -->
<!-- FOURTH SECTION  -->
<section class="container-fluid picture">
   <?php get_template_part('components/single/title');?>
   <div class="container">
      <div class="picture__slider">
      <?php get_template_part('components/group/card-picture');?>
      </div>
   </div>
</section>
<!-- FOURTH SECTION  -->
<!-- FIIFTH SECTION  -->
<section class="instagram">
   <?php get_template_part('components/single/title-Instagram');?>
   <div class="container">
      <div class="instagram__slider">
         <?php get_template_part('components/group/card-instagram');?>
         <?php get_template_part('components/group/card-instagram');?>
         <?php get_template_part('components/group/card-instagram');?>
         <?php get_template_part('components/group/card-instagram');?>
      </div>
   </div>
</section>
<!-- FIIFTH SECTION  -->

<?php get_footer();?>