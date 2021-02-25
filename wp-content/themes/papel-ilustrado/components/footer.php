<footer class="container-fluid" id="footer">
   <div class="container">
      <div class="footer">
         <div class="footer__logo">
            <a href="<?php echo get_home_url(); ?>">
               <img src="<?php the_field('logo_footer', 'option'); ?>" alt="">
            </a>
            <div>
               <?php the_field('logo_copyright', 'option'); ?>
            </div>
         </div>
         <div class="footer__info">
            <div class="footer__info--nav">
               <?php if (have_rows('footer_menu', 'option')) : ?>
                  <ul>
                     <?php while (have_rows('footer_menu', 'option')) : the_row(); ?>
                        <li>
                           <a href="<?php the_sub_field('url_item'); ?>">
                              <?php the_sub_field('texto_item'); ?>
                           </a>
                        </li>
                     <?php endwhile; ?>
                  </ul>
               <?php endif; ?>
            </div>
            <div class="footer__info--subs">
               <h3>Suscribete a nuesto newsletter</h3>
               <form action="">
                  <input type="text" placeholder="Escribre tu correo">
                  <a href="#" class="btn-send"> Enviar </a>
               </form>
            </div>
         </div>
         <?php if (have_rows('footer_widgets', 'option')) : ?>
            <?php while (have_rows('footer_widgets', 'option')) : the_row(); ?>
               <div class="footer__direction">
                  <?php if (have_rows('widget_direccion')) : ?>
                     <?php while (have_rows('widget_direccion')) : the_row(); ?>
                        <div class="footer__direction--address">
                           <?php the_sub_field('icon'); ?>
                           <div>
                              <?php the_sub_field('texto'); ?>
                           </div>
                        </div>
                     <?php endwhile; ?>
                  <?php endif; ?>
                  <?php if (have_rows('widget_horarios')) : ?>
                     <?php while (have_rows('widget_horarios')) : the_row(); ?>
                        <div class="footer__direction--time">
                           <?php the_sub_field('icon'); ?>
                           <div>
                              <?php the_sub_field('texto'); ?>
                           </div>
                        </div>
                     <?php endwhile; ?>
                  <?php endif; ?>
                  <div class="footer__direction--contact">
                     <?php if (have_rows('widget_contactos')) : ?>
                        <?php while (have_rows('widget_contactos')) : the_row(); ?>
                           <div class="footer__direction--contact-email">
                              <?php the_sub_field('icon'); ?>
                              <div>
                                 <a href="<?php the_sub_field('url_destino'); ?>">
                                    <?php the_sub_field('texto'); ?>
                                 </a>
                              </div>
                           </div>
                        <?php endwhile; ?>
                     <?php endif; ?>
                  </div>
                  <?php if (have_rows('rrss', 'option')) : ?>
                     <div class="footer__direction--rrss">
                        <?php while (have_rows('rrss', 'option')) : the_row(); ?>
                           <a href="<?php the_sub_field('url_red_social'); ?>">
                              <?php the_sub_field('icono'); ?>
                           </a>
                        <?php endwhile; ?>
                     </div>
                  <?php endif; ?>
               </div>
            <?php endwhile; ?>
         <?php endif; ?>
      </div>
   </div>
</footer>