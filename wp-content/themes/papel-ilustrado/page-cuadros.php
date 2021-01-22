<?php 

/* Template Name: Cuadros */

get_header();

?>
<?php get_template_part('components/single/page-title');?>
<section class="page-cuadros">
   <div class="container">
      <div class="section-filter row">
         <div class="section-filter__search">
            <i class="fas fa-filter"></i>
            <div>Filtrar</div>
         </div>
         <div class="section-filter__form">
            <select name="#" id="">
               <option value="default" selected> Ordenar alfabéticamente</option>
            </select>
         </div>
      </div>
      <div class="page-cuadros__grid">
         <?php get_template_part('components/group/card-picture');?>
      </div>
   </div>
   <div class="container">
      <a class="btn-more" href="#">Cargar Más</a>
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