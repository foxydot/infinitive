                <div class="wrap">
                <h2>{Full Plugin Name}</h2>
                <form method="post" id="{plugin_slug}_options">
                <?php wp_nonce_field('{plugin_slug}-update-options'); ?>
                    <table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 
                        <tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Option 1:', $this->localizationDomain); ?></th> 
                            <td><input name="{plugin_slug}_path" type="text" id="{plugin_slug}_path" size="45" value="<?php echo $this->options['{plugin_slug}_path'] ;?>"/>
                        </td> 
                        </tr>
                        <tr valign="top"> 
                            <th width="33%" scope="row"><?php _e('Option 2:', $this->localizationDomain); ?></th> 
                            <td><input name="{plugin_slug}_allowed_groups" type="text" id="{plugin_slug}_allowed_groups" value="<?php echo $this->options['{plugin_slug}_allowed_groups'] ;?>"/>
                            </td> 
                        </tr>
                        <tr valign="top"> 
                            <th><label for="{plugin_slug}_enabled"><?php _e('CheckBox #1:', $this->localizationDomain); ?></label></th><td><input type="checkbox" id="{plugin_slug}_enabled" name="{plugin_slug}_enabled" <?=($this->options['{plugin_slug}_enabled']==true)?'checked="checked"':''?>></td>
                        </tr>
                        <tr>
                            <th colspan=2><input type="submit" name="{plugin_slug}_save" value="Save" /></th>
                        </tr>
                    </table>
                </form>