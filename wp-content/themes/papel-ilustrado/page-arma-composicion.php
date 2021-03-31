<?php 

/* Template Name: Arma composicion */

get_header();

?>
<?php get_template_part('components/single/page-title');?>

<!-- FIRTS SECTION -->
<!-- <section class="build-compostion__title">
   <div class="container">
      <h2>PASO 1: Selecciona su diagramación</h2>
      <div>elige la diagramación que quieras colocar en tu espacio favorito, tenemos muchas combinaciones.</div>
   </div>
</section> -->
<!-- FIRTS SECTION -->
<!-- SECOND SECTION -->
<section class="container-fluid build-composition__slider">
   <div id="composition-component" class="container">
      <section class="build-compostion__title">
         <div class="container">
            <h2>PASO 1: Selecciona su diagramación</h2>
            <div>elige la diagramación que quieras colocar en tu espacio favorito, tenemos muchas combinaciones.</div>
         </div>
      </section>
      <!-- Slider -->
      <div class="slider-nav">
         <button  class="build-composition__nav" v-for="(com, index) in compositions" :key="com.id" @click="setComposition(index)">
            <img :src="com.icono" alt="">
         </button>
      </div>

      <!-- Imagen Principal -->
      <div class="build-composition__modal">
         <div v-if='selectedComposition && selectedComposition.imagen' class="build__bg" :style="`background-image: url('${selectedComposition.imagen}');`"></div>
         <!-- Antes de seleccionar la composicion -->
         <div v-else class="build__bg build__bg-noselected"></div>

         <!-- lista selecciona -->
         <div class="build_lista" v-if='selectedComposition && selectedComposition.imagen'>
            <ul>
               <li v-for="(art, index) in selectedComposition.obras">
                  <button v-if="!selectedArtworks[index]" class="no-selected" @click="setDimensions(art, index)" data-toggle="modal" data-target="#build-modal">
                     <img src="#" alt="">
                     <div class="description">Selecciona una obra {{art}}</div>
                     <div class="number">{{index+1}}</div>
                  </button>
                  <div v-else class="selected">
                     <button @click="setDimensions(art, index)" data-toggle="modal" data-target="#build-modal">
                        <img src="#" alt="">
                     </button>
                     <div class="description">{{selectedArtworks[index].name}}</div>
                     <span class="selected-close" aria-hidden="true" @click="removeArtwork(index)">&times;</span>
                     <div class="description">Tamaño: {{art}}</div>
                     <div class="number">{{index+1}}</div>
                  </div>
               </li>
            </ul>
         </div>
         <!-- lista selecciona -->
         


         <!-- btn para abrir modal -->
         <button v-if="step === 0" class="build__btn btn-principal" data-toggle="modal" data-target="#build-modal">Selecciona diagramación</button>
         <!-- btn para abrir modal -->
         
         <!-- DIV de precio -->
         <div class="build-price" v-if='selectedComposition && selectedComposition.imagen'>
            <div class="build-price__number">
               <h2>Precio Total:</h2>
               <span>$39.900</span>
            </div>
            <button class="build-price__btn btn-principal" :disabled="!areAllArtsSelected">
               Agregar al carrito
            </button>
         </div>
         <!-- DIV de precio -->

         <!-- Modal -->
         <div class="modal fade" id="build-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <!-- Centrar la modal -->
               <div class="modal-dialog modal-dialog-centered build-modal__container" role="document">
                  <div class="modal-content">
                     <!-- Btn de cerrar-->
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <!-- Btn de cerrar-->

                     <!-- Contenido-->
                     <div class="modal-body">
                        <div class="build-compostion__title">
                           <h2>PASO 2: Selecciona las obras</h2>
                           <div>
                              Elige el orden de las obras que desees para tu composición, éstas se enumerarán de la primera a la 
                              última dependiendo de la cantidad de la diagramación
                           </div>
                        </div>

                        <div class="build__filter">
                           <div class="build__filter--icon">
                              <i class="fas fa-filter"></i>
                              filtrar por
                           </div>
                           <form action="">
                              <select name="categoria" id="categoria">
                                 <option value="" selected> Categoría </option>
                              </select>
                              <select name="color" id="color">
                                 <option value="" selected> Color </option>
                              </select>
                              <select name="tags" id="tags">
                                 <option value="" selected> Temas/tags </option>
                              </select>
                           </form>
                        </div>

                        <div class="row build__row">
                           <!-- Deberian ser btn como los marcos -->
                           <div v-for="product in filteredProducts" class="col-md-2 col-4 build__row--arts">
                              <button type="button" class="close" data-dismiss="modal" :aria-label="`Seleccionar ${product.name}`" @click="selectArtwork(product)">
                                 <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
                              </button>
                           </div>
                        </div>
                     </div>
                     <!-- Contenido-->
                  </div>
               </div>
               <!-- Centrar la modal -->
         </div>
         <!-- Modal -->
      </div>

      <!-- Para mobile -->
      <div v-if="step > 0" class="build-modal__container mobile">
         <!-- <div class="build-compostion__title">
            <h2>PASO 2: Selecciona las obras</h2>
            <div>
               Elige el orden de las obras que desees para tu composición, éstas se enumerarán de la primera a la 
               última dependiendo de la cantidad de la diagramación
            </div>
         </div> -->

         <div class="build__filter">
            <div class="build__filter--icon">
               <i class="fas fa-filter"></i>
               filtrar por
            </div>
            <form action="">
               <select name="categoria" id="categoria">
                  <option value="" selected> Categoría </option>
               </select>
               <select name="color" id="color">
                  <option value="" selected> Color </option>
               </select>
               <select name="tags" id="tags">
                  <option value="" selected> Temas/tags </option>
               </select>
            </form>
         </div>

         <div class="row build__row">
            <!-- Deberian ser btn como los marcos -->
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
            <div class="col-md-2 col-6 build__row--arts">
               <img src="<?php echo get_template_directory_uri(); ?>/img/png/flower.png" alt="">
            </div>
         </div>
         
         <div class="build-price">
            <!-- 
               Debe cambiar de mensaje y de clase cuando se seleccionen la cantidad de marco correcta 

               Tipo 1:
               clase => Red: cuando no lo han seleccionado todas las obras
               mensaje => Paso 2 selecciona las obras

               Tipo 2:
               clase => Green: cuando selecciono todos las Obras
               Mensaje => Si ya esta listo. Agregar al carrito
            
            -->

            <div class="build-price__msg green">
               <div class="msg">Si ya esta listo. Agregar al carrito</div>
            </div>
            <div class="build-price__container">
               <div class="build-price__number">
                  <h2>Precio Total:</h2>
                  <span>$39.900</span>
               </div>

               <!-- Añadir clase disabled para que agrege el bg correcto -->
               <button class="build-price__btn btn-principal">
                  Agregar al carrito
               </button>
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