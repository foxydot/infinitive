<?php
class muwlinks extends WP_Widget_Links {
	private $muwidgets = null;
	public function muwlinks() {
		$this->muwidgets = new muwidgets ( $this,'Links' );
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
