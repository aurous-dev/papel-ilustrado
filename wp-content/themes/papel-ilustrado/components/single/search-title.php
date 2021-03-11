<?php
$results = get_query_var('results');
?>
<div class="container-fluid search-title">
   <div class="container">
         <h2>Resultado de búsqueda</h2>
         <div class="search-title__results">
            Hay <span class="number">
               <?php echo $results; ?>
            </span> resultados relacionados con "<span class="word"><?php echo get_search_query(); ?></span>"
         </div>
   </div>
</div>