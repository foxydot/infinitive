<div class="my_meta_control">
<a style="float:right; margin:0 10px;" href="#" class="dodelete-docs button">Remove All</a>
 
	<p>Add documents to the library by entering in a title, 
	URL and selecting a level of access.</p>
 	<input id="post_id_reference" name="post_id_reference" type="hidden" value="<?php print $post->ID; ?>" />
	<?php while($mb->have_fields_and_multi('docs')): ?>
	<?php $mb->the_group_open(); ?>
 
		<?php $mb->the_field('title'); ?>
		<label>Title</label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
 
		<?php $mb->the_field('upload_file'); ?>
		<label>File</label>
		<p><input type="text" class="uploadfile" id="<?php $mb->the_name(); ?>" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
		<a href="#" class="uploadbtn button" onclick="return false;">Add File</a></p>
 
		<?php $mb->the_field('access'); ?>
		<p>
			<a href="#" class="dodelete button">Remove Document</a>
		</p>
 
	<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
 
	<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-docs button">Add Document</a></p>
</div>