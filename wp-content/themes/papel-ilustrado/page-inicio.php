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
               <a href="#">Ver Composiciones</a>
            </div>
         </div>
         <div class="hero__card">
            
         </div>
      </div>
   </div>
</section>
<!-- FIRST SECTION  -->

<?php get_footer();?>