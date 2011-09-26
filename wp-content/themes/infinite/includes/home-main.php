
<div class="featured-header-area">
	<?php dynamic_sidebar( 'homepage-feature-area' ); ?>
</div>
<div id="container" class="content">
	<div id="content" role="main">
		<?php //Wide Widgets ?>
		
<?php if ( is_active_sidebar( 'homepage-wide-widget-area' ) ) : ?>
				<div id="wide" class="widget-area">
					<ul>
						<?php dynamic_sidebar( 'homepage-wide-widget-area' ); ?>
					</ul>
		<div class="clear"></div>
				</div><!-- #fourth .widget-area -->
<?php endif; ?>
		<div class="clear"></div>
		<?php //three footer widgets ?>
		<?php if ( is_active_sidebar( 'homepage-footer-widget-area' ) ) : ?>
				<div id="footer" class="widget-area">
					<ul>
						<?php dynamic_sidebar( 'homepage-footer-widget-area' ); ?>
					</ul>
		<div class="clear"></div>
				</div><!-- #fourth .widget-area -->
<?php endif; ?>
		<div class="clear"></div>
	</div>
</div>