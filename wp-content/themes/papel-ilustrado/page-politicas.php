<?php 

/* Template Name: Politica */

get_header();

?>
<div class="container-fluid policies">
   <?php get_template_part('components/single/title');?>
   <div class="container">
      <div class="row policies__container">
         <a href="#" class="policies__link col-6">
            <div class="policies__link--div">
               Santiago
            </div>
         </a>
         <a href="#" class="policies__link col-6">
            <div class="policies__link--div">
               Regiones
            </div>
         </a>
      </div>
   </div>
</div>

<?php get_footer();?>