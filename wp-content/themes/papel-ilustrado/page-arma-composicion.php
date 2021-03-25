<?php 

/* Template Name: Arma composicion */

get_header();

?>
<?php get_template_part('components/single/page-title');?>

<!-- FIRTS SECTION -->
<section class="build-compostion__title">
   <div class="container">
      <h2>PASO 1: Selecciona su diagramación</h2>
      <div>elige la diagramación que quieras colocar en tu espacio favorito, tenemos muchas combinaciones.</div>
   </div>
</section>
<!-- FIRTS SECTION -->
<!-- SECOND SECTION -->
<section class="container-fluid build-composition__slider">
   <div class="container">
      <div class="slider-nav">
         <div class="build-composition__nav">
            <img src="<?php echo get_template_directory_uri(); ?>/img/png/medida.png" alt="">
         </div>
         <div class="build-composition__nav">
            <img src="<?php echo get_template_directory_uri(); ?>/img/png/medida.png" alt="">
         </div>
         <div class="build-composition__nav">
            <img src="<?php echo get_template_directory_uri(); ?>/img/png/medida.png" alt="">
         </div>
      </div>
      <div class="slider-for">
         <div class="build-composition__for">
            <div class="build-for__container">
               <div class="build__bg"  style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/medida.png');"></div>
               <a href="#" class="build__btn btn-principal" data-toggle="modal" data-target="#build-modal">Selecciona diagramación</a>
               <!-- <div class="buid__modal">
                  Modal
                  <div class="modal fade" id="build-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                 </button>
                              </div>
                              <div class="modal-body">
                                 Hola soy una
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  Modal
               </div> -->
            </div>
         </div>
         <div class="build-composition__for">
            <div class="build-for__container">
               <div class="build__bg"  style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/medida.png');"></div>
               <a href="#" class="build__btn btn-principal" data-toggle="modal" data-target="#build-modal">Selecciona diagramación</a>
               <!-- <div class="buid__modal">
                  Modal
                  <div class="modal fade" id="build-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                 </button>
                              </div>
                              <div class="modal-body">
                                 Hola soy una
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  Modal
               </div> -->
            </div>
         </div>
         <div class="build-composition__for">
            <div class="build-for__container">
               <div class="build__bg"  style="background-image: url('<?php echo get_template_directory_uri(); ?>/img/png/medida.png');"></div>
               <a href="#" class="build__btn btn-principal" data-toggle="modal" data-target="#build-modal">Selecciona diagramación</a>
               <!-- <div class="buid__modal">
                  Modal
                  <div class="modal fade" id="build-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                 </button>
                              </div>
                              <div class="modal-body">
                                 Hola soy una
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  Modal
               </div> -->
            </div>
         </div>
      </div>
   </div>
</section>
<!-- SECOND SECTION -->
<!-- THIRD SECTION -->
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
<!-- THIRD SECTION -->

<?php get_footer();?>