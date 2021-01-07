<?php 

/* Template Name: Home Page */

get_header();

?>

<!-- FIRST SECTION  -->
<section class="container-fluid hero">
   <div class="container">
      <div class="hero__container">
         <div class="hero__principal"  style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero1.png');">
            <div class="hero__principal--desc" >
               <h2>Tenemos la composicion que estas buscando</h2>
               <?php get_template_part('components/single/principal-btn');?>
            </div>
         </div>
         <div class="row hero__card">
            <div class="col-4 hero__card--container" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero2.png');">
               <?php get_template_part('components/single/principal-btn');?>
            </div>
            <div class="col-4 hero__card--container" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero3.png');">
               <?php get_template_part('components/single/principal-btn');?>
            </div>
            <div class="col-4 hero__card--container" style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/hero4.png');">
               <?php get_template_part('components/single/principal-btn');?>
            </div>   
         </div>
      </div>
   </div>
</section>
<!-- FIRST SECTION  -->

<?php get_footer();?>