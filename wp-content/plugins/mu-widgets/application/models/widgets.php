<?php
class muwidgets_widgets extends bv46v_Base {
	private $widget = null;
	public function __construct(&$widget, $name) {
		$this->widget = $widget;
		$description = sprintf ( "Just like the WordPress widget '%s' but you select it to run as though it is on another site on the MU installation .", $name );
		$id = sprintf ( "s%s", strtolower ( str_replace ( ' ', '', $name ) ) );
		$title = sprintf ( "Site %s", $name );
		$widget_ops = array ('description' => $description );
		$this->widget->WP_Widget ( $id, $title, $widget_ops );
	}
	public function form($instance) {
		global $current_blog;
		$blogs = new wv46v_data_table ( 'blogs' );
		$sql = "SELECT * FROM %s ORDER BY domain, path";
		$sql = sprintf ( $sql, $blogs->name () );
		$blogs = $blogs->execute ( $sql );
		$blog_id = $current_blog->blog_id;
		if (! empty ( $instance ['Blog'] )) {
			$blog_id = $instance ['Blog'];
		}
		?>
<p><label
	for="<?php
		echo $this->widget->get_field_id ( 'Blog' );
		?>">Site</label> <select
	name="<?php
		echo $this->widget->get_field_name ( 'Blog' );
		?>"
	id="<?php
		echo $this->widget->get_field_id ( 'Blog' );
		?>"
	class="widefat">
		<?php
		foreach ( $blogs as $blog ) :
			?>
		<option value="<?php
			echo $blog ['blog_id']?>"
		<?php
			selected ( $blog_id, $blog ['blog_id'] );
			?>><?php
			echo $blog ['domain'] . $blog ['path'] . '{' . get_blog_option ( $blog ['blog_id'], 'blogname' ) . '}'?></option>
		<?php
		endforeach
		?>
	</select> <br />
<small>Click 'Save' to update option settings</small></p>
<?php
		$this->swap ( $instance );
	}
	private $blog = null;
	public function swap($instance) {
		if (isset ( $instance ['Blog'] )) {
			$this->do_swap ( $instance ['Blog'] );
		}
	}
	public function swap_back() {
		$this->do_swap ();
	}
	public function update($instance, $new_instance) {
		$instance ['Blog'] = $new_instance ['Blog'];
		return $instance;
	}
	private $_old_id = null;
	public function do_swap($id = null,$validate=false) {
		if(is_multisite())
		{
			if (null !== $id) {
				if($id!='')
				{
					switch_to_blog($id,$validate);
				}
			} else {
				restore_current_blog();
			}
		}
	}
}
