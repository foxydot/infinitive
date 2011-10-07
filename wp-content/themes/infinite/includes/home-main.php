
<div class="featured-header-area">
	<?php dynamic_sidebar( 'main-feature-area' ); ?>
</div>
<div id="container" class="content">
	<div id="content" role="main">
		<?php //Wide Widgets ?>
		<div id="wide" class="widget-area">
			<ul>
				<?php print infinitive_highlights(1,array(1), array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'main')), 'number_posts' => 1)); ?>			
				<?php print infinitive_highlights(1,array(4), array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'main')), 'number_posts' => 1)); ?>			
				<?php print infinitive_highlights(1,array(3), array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'main')), 'number_posts' => 1)); ?>			
				<?php print infinitive_highlights(1,array(2), array('post_type' => 'page', 'tax_query' => array(array('taxonomy' => 'msd_special', 'field' => 'slug', 'terms' => 'main')), 'number_posts' => 1)); ?>			
			</ul>
			<ul>
				<li class="solutions-link infinitive">Infinitive <a href="<?php print get_site_url(1); ?>/infinitive-solutions">Learn More ></a></li>
				<li class="solutions-link analytics">Infinitive Analytics <a href="<?php print get_site_url(4); ?>">Learn More ></a></li>
				<li class="solutions-link insight">Infinitive Insight <a href="<?php print get_site_url(3); ?>">Learn More ></a></li>
				<li class="solutions-link federal">Infinitive Federal <a href="<?php print get_site_url(2); ?>">Learn More ></a></li>
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