
<?php
if(!$title) {
   $title = get_sub_field('titulo');
} else {
   $title = get_query_var('title');
}
// Para el texto de botón
if(!$button) {
   $button = get_sub_field('texto_boton');
} else {
   $button = get_query_var('button');
}
?>
<div class="btn-special">
   <h3><?php echo $title; ?></h3>
   <div class="btn-special__arrow">
      <div><?php echo $button; ?></div>
      <img src="<?php echo get_template_directory_uri(); ?>/img/svg/arrow.svg" alt="">
   </div>
</div>