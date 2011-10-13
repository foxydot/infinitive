<?php
class muwmeta extends WP_Widget_Meta {
	private $muwidgets = null;
	public function muwmeta() {
		$this->muwidgets = new muwidgets ( $this,'Meta' );
	}
	function widget($args, $instance) {
		$this->muwidgets->swap($instance);
		parent::widget ( $args, $instance );
		$this->muwidgets->swap_back();
	}
	function update($new_instance, $old_instance) {
		$instance = parent::update ( $new_instance, $instance );
		return $this->muwidgets->update($instance,$new_instance);
	}
	
	function form($instance) {
		$this->muwidgets->form($instance);
		parent::form($instance);
		$this->muwidgets->swap_back();
	}
}
