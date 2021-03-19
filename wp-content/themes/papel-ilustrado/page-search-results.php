<?php 

/* Template Name: search-results */

get_header();

?>
hola
<?php get_template_part('components/single/search-title');?>
<section class="search-result">
   <div class="container">
      <div class="search-result__grid">
         <?php get_template_part('components/group/card-search');?>
      </div>
   </div>
</section>
<section class="section__news">
   <div class="container">
      <div class="section__news--title">
         <h2>Novedades</h2>
      </div>
      <div class="fourColumn__slider">
         <?php get_template_part('components/group/card-paiting');?>
      </div>
   </div>
</section>
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


<?php get_footer();?>