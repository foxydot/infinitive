
<div class="featured-header-area">
	<?php dynamic_sidebar( 'main-feature-area' ); ?>
</div>
<div id="container" class="content">
	<div id="content" role="main">
		<?php //Wide Widgets ?>
		<div id="wide" class="widget-area">
			<ul>
				<?php print infinitive_highlights(4,'all', array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'main')), 'number_posts' => 12)); ?>			
			</ul>
		<div class="clear"></div>
		</div><!-- #fourth .widget-area -->		
		<div class="clear"></div>
		<?php //three footer widgets ?>
		<?php if ( is_active_sidebar( 'main-footer-widget-area' ) ) : ?>
				<div id="footer" class="widget-area">
					<ul>
						<?php dynamic_sidebar( 'main-footer-widget-area' ); ?>
					</ul>
		<div class="clear"></div>
				</div><!-- #fourth .widget-area -->
<?php endif; ?>
		<div class="clear"></div>
	</div>
</div>