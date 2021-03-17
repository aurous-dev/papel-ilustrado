<?php 

/* Template Name: Contacto */

get_header();

?>
<div class="container-fluid contacto">
   <?php
   $title = get_the_title();
   set_query_var('title', $title);
   get_template_part('components/single/title');?>
   <div class="container">
      <div class="contacto__container">
         <div id="app">
            <formu></formu>
         </div>
      </div>
   </div>
</div>

<?php get_footer();?>