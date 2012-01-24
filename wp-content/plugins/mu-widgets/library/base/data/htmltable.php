<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_data_htmltable extends bv45v_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function render($name,$columns,$data)
	{
		$return = "";
		$this->view->columns = $columns;
		$this->view->name = $name;
		$this->view->data = $data;
		$return .= $this->render_script ( 'htmltable/header.phtml' ,false);
		$return .= $this->render_script ( 'htmltable/body.phtml' ,false);
		$return .= $this->render_script ( 'htmltable/footer.phtml' ,false);
		return $return;
	}
}