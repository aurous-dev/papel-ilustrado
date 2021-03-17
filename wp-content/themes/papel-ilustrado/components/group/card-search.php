<?php
$value = get_query_var('value');
?>
<a href="<?php the_permalink();?>" class="cards">
   <div class="card__container" data-aos-delay="<?php echo $value ;?>00" data-aos="fade-up" data-aos-duration="1000" >
      <?php get_template_part('components/single/card-imgSearch');?>
      <?php get_template_part('components/single/card-info');?>
   </div>
</a>