<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package AccessPress Store
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


</article><!-- #post-## -->
<?php if (is_active_sidebar('promo-widget-1')): ?>
                <section id="promo-section1">
                    <div class="ak-container">
                        <div class="promo-wrap1">
                            <div class="promo-product1 clearfix">
                                <?php dynamic_sidebar('promo-widget-1'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
            
            
            <?php the_content(); ?>