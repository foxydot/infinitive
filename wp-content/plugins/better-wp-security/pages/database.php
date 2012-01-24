<?php
	global $wpdb, $BWPS;
	
	if (is_multisite()) {
		$dpre = substr($wpdb->prefix,0,strpos($wpdb->prefix,'_') + 1);
	} else {
		$dpre =  $wpdb->prefix;
	}
	
	if (isset($_POST['BWPS_database_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_database_save')) {
			die('Security error!');
		}
		
		 $checkPreExists = $dpre;
		
		while ($checkPreExists) {
			$prelength = rand(3,5);
			$newPrefix = substr(md5(rand()), rand(0, (32 - $prelength)), $prelength) . '_';
			$checkPreExists = $wpdb->get_results('SHOW TABLES LIKE "' . $newPrefix . '%";', ARRAY_N);
		}
		
		$tables = $wpdb->get_results('SHOW TABLES LIKE "' . $dpre . '%"', ARRAY_N);
					
		if ($tables) {
			$tablesCopied = array();
						
			foreach ($tables as $table) {
				$table = substr($table[0], strlen($dpre), strlen($table[0]));
							
				$sql = 'CREATE TABLE `' . $newPrefix . $table . '` LIKE `' . $dpre . $table . '`;';
					
				$createTable = $wpdb->query($sql);
			
				if ($createTable === false) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
			
					$errorHandler->add("2", __("Could not create table.", 'better-wp-security'));
				} else {
					
					$sql = 'INSERT INTO `' . $newPrefix . $table . '` SELECT * FROM `' . $dpre . $table . '`;';
								
					$popTable = $wpdb->query($sql);
								
					if ($popTable === false) {
						if (!$errorHandler) {
							$errorHandler = new WP_Error();
						}
			
						$errorHandler->add("2", __("Could not copy table.", 'better-wp-security'));
					} else {
							$tablesCopied[] = $table;
					}
				}
			}
				
			if (count($tablesCopied) == count($tables)) {
			
				$upOpts = true;
				if (is_multisite()) {
					$blogs = get_blog_list( 0, 'all' ); //need to find a non-deprecated alternative
					if( is_array( $blogs ) ) {
						foreach( $blogs as $details ) {
							$results = $wpdb->query('UPDATE `' . $newPrefix . $details['blog_id'] . '_options` SET option_name = "' . $newPrefix . $details['blog_id'] . '_user_roles" WHERE option_name = "' . $dpre . $details['blog_id'] . '_user_roles" LIMIT 1;');
							if ($results === false) {
								$upOpts = false;
							}
						}
					}
				} else {
					$upOpts = $wpdb->query('UPDATE `' . $newPrefix . 'options` SET option_name = "' . $newPrefix . 'user_roles" WHERE option_name = "' . $dpre . 'user_roles" LIMIT 1;');
				}
							
				if ($upOpts === false && !is_multisite()) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
			
					$errorHandler->add("2", __("Could not update prefix refences in options table.", 'better-wp-security'));
				}
					
				$rows = $wpdb->get_results('SELECT * FROM `' . $newPrefix . 'usermeta`');
				$upMeta = true;
				foreach ($rows as $row) {
					if (substr($row->meta_key,0,strlen($dpre)) == $dpre) {
						$pos = $newPrefix . substr($row->meta_key, strlen($dpre), strlen($row->meta_key));
						$result = $wpdb->query('UPDATE `' . $newPrefix . 'usermeta` SET meta_key="' . $pos . '" WHERE meta_key= "' . $row->meta_key . '" LIMIT 1;');
					}
					if ($result == false) {
						$upMeta = false;
					}
				}

				if ($upMetar === FALSE) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
			
					$errorHandler->add("2", __("Could not update prefix refences in usermeta table.", 'better-wp-security'));
				}
			}
		}
		
		$tables = $wpdb->get_results('SHOW TABLES LIKE "' . $dpre . '%"', ARRAY_N);
					
		if ($tables) {
			$tablesDropped = array();
						
			foreach ($tables as $table) {
				$table = $table[0];
							
				$dropTable = $wpdb->query('DROP TABLE `' . $table . '`;');
							
				if ($dropTable === false) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
					$errorHandler->add("2", __("Could not drop table.", 'better-wp-security'));
				} else {
					$tablesDropped[] = $table;
				}
			}
		}
		
		$conf_f = $BWPS->getConfig();

		chmod($conf_f, 0755);
		$handle = @fopen($conf_f, "r+");
		if ($handle) {
			while (!feof($handle)) {
				$lines[] = fgets($handle, 4096);
			}
			fclose($handle);
			$handle = @fopen($conf_f, "w+");
			foreach ($lines as $line) {
				if (strpos($line, $dpre)) {
					$line = str_replace($dpre, $newPrefix, $line);
				}
				fwrite($handle, $line);
			}
			fclose($handle);
		}
        	
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>' . __('Database Prefix Changed.', 'better-wp-security') . '</p></div>';
		}
	}
	
	function checkTablePre(){
		global $wpdb;
		
		if ($dpre == 'wp_') {
			return true;
		}else{
			echo false;
		}
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Database Prefix', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<?php if ($BWPS->can_write($BWPS->getConfig())) { ?>
			<?php 
				if ((checkTablePre() && !isset($_POST['BWPS_database_save'])) || (!checkTablePre() && isset($_POST['BWPS_database_save']) && isset($errorHandler))) {
					$bgcolor = "#ffebeb";
				} else {
					$bgcolor = "#fff";
				}
			?>
			<div class="postbox-container" style="width:70%">	
				<div class="postbox opened" style="background-color: <?php echo $bgcolor; ?>;">
					<h3><?php _e('Database Prefix', 'better-wp-security'); ?></h3>	
					<div class="inside">
					<p><?php _e('Use the form below to change the table prefix for your Wordpress Database.', 'better-wp-security'); ?></p>
					<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e('WARNING: BACKUP YOUR DATABASE BEFORE USING THIS TOOL!', 'better-wp-security'); ?></p>
						<?php if ((checkTablePre() && !isset($_POST['BWPS_database_save'])) || (!checkTablePre() && isset($_POST['BWPS_database_save']) && isset($errorHandler))) { ?>
							<p><strong><?php _e('Your database is using the default table prefix', 'better-wp-security'); ?> <em>wp_</em>. <?php _e('You should change this.', 'better-wp-security'); ?></strong></p>
						<?php } else { ?>
							<?php 
								if (isset($_POST['BWPS_database_save']) && !isset($errorHandler)) {
									$pre = $newPrefix;
								} else {
									$pre = $dpre;
								}
							?>
							<p><?php _e('Your current database table prefix is', 'better-wp-security'); ?> <strong><em><?php echo $pre; ?></em></strong></p>
						<?php } ?>
						<form method="post">
							<?php wp_nonce_field('BWPS_database_save','wp_nonce') ?>
							<p><?php _e('Press the button below to generate a random database prefix value and update all of your tables accordingly.', 'better-wp-security'); ?></p>
							<p class="submit"><input type="submit" name="BWPS_database_save" value="<?php _e('Change Database Table Prefix', 'better-wp-security'); ?>"></p>
						</form>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="postbox-container" style="width:70%">	
				<div class="postbox opened" style="background-color: #ffebeb;">
					<h3><?php _e('Database Prefix', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<h4><?php _e('Warning: Better WP Security cannot write to your <em>wp-config.php</em> file. You must fix this error before continuing.','better-wp-security'); ?></h4>
					</div>
				</div>
			</div>
		<?php } ?>		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>