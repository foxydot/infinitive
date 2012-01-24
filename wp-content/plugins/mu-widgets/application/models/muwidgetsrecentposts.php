<?php
class muwidgetsrecentposts extends WP_Widget_Recent_Posts {
	private $muwidgets = null;
	public function muwidgetsrecentposts() {
		$this->muwidgets = new muwidgets_widgets ( $this,'Recent Posts' );
	}
	function widget($args, $instance) {
		$this->muwidgets->swap($instance);
		parent::widget ( $args, $instance );
		$this->muwidgets->swap_back();
	}
	function update($new_instance, $instance) {
		$instance = parent::update ( $new_instance, $instance );
		return $this->muwidgets->update($instance,$new_instance);
	}
	
	function form($instance) {
		$this->muwidgets->form($instance);
		parent::form($instance);
		$this->muwidgets->swap_back();
	}
}
