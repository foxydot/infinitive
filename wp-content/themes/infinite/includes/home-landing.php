<?php get_sidebar('breadcrumbs'); ?>
<div class="landing-header-area">
	<?php if(get_option('infinite_landing_link')!=""){ ?>
	<a href="<?php echo get_option('infinite_landing_link'); ?>" class="landing-link"></a>
	<?php }?>
	<?php get_sidebar('logo'); ?>
	<?php get_sidebar('nav'); ?>
</div>
<div id="container" class="content">
	<div id="content" role="main">
		<?php //three footer widgets ?>
		<?php if ( is_active_sidebar( 'homepage-footer-widget-area' ) ) : ?>
				<div id="landing-footer" class="widget-area">
					<ul>
						<?php dynamic_sidebar( 'homepage-footer-widget-area' ); ?>
					</ul>
		<div class="clear"></div>
				</div><!-- #fourth .widget-area -->
<?php endif; ?>
		<div class="clear"></div>
	</div>
</div>
