<?php 

/* Template Name: Cuadros */

get_header();

?>
<section class="container-fluid page-title">
   <div class="container">
      <a href="#" class="page-title__tag">
         inicio / <span class="page-active"> Cuadros </span>
      </a>
      <h2>
         Cuadros
      </h2>
   </div>
</section>
<section class="page-cuadros">
   <div class="container">
      <div class="section-filter">
         <div class="section-filter__search">
            <div>Filtrar</div>
            <i></i>
         </div>
         <div class="section-filter__form">
            <input type="text">
         </div>
      </div>
      <div class="page-cuadros__grid">
         <?php get_template_part('components/group/card-picture');?>
      </div>
   </div>
</section>

<?php get_footer();?>