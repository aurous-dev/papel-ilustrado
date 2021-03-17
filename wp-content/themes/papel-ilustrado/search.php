<?php

/* Template Name: search-results */

get_header();

global $query_string;
$query_args = explode("&", $query_string);
$search_query = array('post_type' => 'product');

// WP Query
// var_dump($search_query);

foreach ($query_args as $key => $string) {
    $query_split = explode("=", $string);
    $search_query[$query_split[0]] = urldecode($query_split[1]);
} // foreach
$the_query = new WP_Query($search_query);
$total_results = $the_query->found_posts;
set_query_var('results', $total_results);
get_template_part('components/single/search-title'); ?>
<section class="search-result">
    <?php if ($the_query->have_posts()) : ?>
        <div class="container">
            <div class="search-result__grid">
                <?php
                $value = 1;
                while ($the_query->have_posts()) : $the_query->the_post(); ?>
                    <?php
                    $value++;
                    set_query_var('value', $value);
                    get_template_part('components/group/card-search'); ?>
                <?php
                endwhile; ?>

            </div>
        </div>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p>No hay resultados para tu búsqueda</p>
                </div>
            </div>
        </div>
    <?php
    endif;
    ?>
</section>
<section class="section__news">
    <div class="container">
        <div class="section__news--title">
            <h2>Novedades</h2>
        </div>
        <div class="fourColumn__slider">
            <?php get_template_part('components/group/card-paiting'); ?>
        </div>
    </div>
</section>
<section class="section__news">
    <div class="container">
        <div class="section__news--title">
            <h2>Novedades</h2>
        </div>
        <div class="fiveColumn__slider">
            <?php get_template_part('components/group/card-picture'); ?>
        </div>
    </div>
</section>


<?php get_footer(); ?>