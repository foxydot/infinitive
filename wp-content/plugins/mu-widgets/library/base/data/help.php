<?php
class bv44v_data_help extends bv44v_base {
	protected $_tag;
	public function __construct(&$application, $tag) {
		parent::__construct ( $application );
		if(!isset($application->help_meta->$tag))
		{
			$tag = '_empty';
		}
		$this->_tag = $tag;
	}
	private function _help()
	{
		$tag = $this->_tag;
		return $this->application()->help_meta->$tag;
	}
	private function item($name)
	{
		if(isset($this->_help()->$name))
		{
			return $this->_help()->$name;
		}
		else
		{
			return $this->application()->help_meta->_empty->$name;
		}
		return '';
	}
	public function url()
	{
		return $this->item('url');
	}
	public function title()
	{
		return $this->item('title');
	}
	public function text()
	{
		return $this->item('text');
	}
	public function css()
	{
		return $this->item('css');
	}
	public function render($link_text='')
	{
		$url = $this->url();
		if($url=='#')
		{
			$return = $link_text;
		}
		else
		{
			$title = $this->title();
			$css = $this->css();
			if($title!='')
			{
				$title = "title='{$title}'";
			}
			if($css!='')
			{
				$css = "class='{$css}'";
			}
			$return = "<a href='{$url}' {$css} {$title}>{$link_text}</a>";
		}
		return $return;
	}
}