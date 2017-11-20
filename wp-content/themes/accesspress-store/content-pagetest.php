<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package AccessPress Store
 */
?>

    <div class="entry-content">
        <?php if (has_post_thumbnail()): ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('accesspress-blog-big-thumbnail'); ?>
            </div>
        <?php endif; ?>
        
        
        <div class="content-inner clearfix">
            
            <div class="site-header headertwo">
                <?php the_content(); ?>
            </div>
            
            
            
            
            
            
            
            
            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links">' . __('Pages:', 'accesspress-store'),
                'after' => '</div>',
            ));
            ?>
            
           
            
            
        </div>
        
        
    </div><!-- .entry-content -->

<?php if (is_active_sidebar('promo-widget-3')): ?>
    <section id="promo-section3">
        <div class="ak-container">
            <div class="promo-wrap3">
                <div class="promo-product2">
                    <?php dynamic_sidebar('promo-widget-3'); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
 	
</article><!-- #post-## -->



