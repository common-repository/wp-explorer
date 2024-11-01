<?php

if(!defined('WP_ADMIN') OR !current_user_can('manage_options')) wp_die(__('You do not have sufficient permissions to access this page.'));

load_plugin_textdomain('wp-explorer', 'wp-content/plugins/wp-explorer');
if($_POST['do'] == 'update')
{
	require ('inc/functions_misc.php');
	check_admin_referer('update-wp-explorer-options');
	$wp_explorer_options = $_POST['wp_explorer_options'];
	$wp_explorer_default = wp_explorer_opmap();
	
	if ($wp_explorer_options) 
	{
		foreach ($wp_explorer_options as $key => $value) 
		{
			$wp_explorer_options[$key] = $_POST['revert'][$key] ? $wp_explorer_default[$key] : stripslashes_deep(trim($value));
		}
		update_option('wp_explorer_options', $wp_explorer_options);
		?><div id="message" class="updated fade"><p><?php _e('Options saved.', 'wp-explorer') ?></p></div><?php
	}
}

$wp_explorer_options = get_option('wp_explorer_options'); 
$wp_explorer_options_map[] = array('name' => 'tmp_main', 			'title' => __('Template: Main', 'wp-explorer'),  						'input' => 'textarea' );
$wp_explorer_options_map[] = array('name' => 'tmp_dirtbl', 			'title' => __('Template: Folders Table', 'wp-explorer'),  				'input' => 'textarea' );
$wp_explorer_options_map[] = array('name' => 'tmp_dirbit', 			'title' => __('Template: Folders Bit', 'wp-explorer'), 					'input' => 'textarea' );
$wp_explorer_options_map[] = array('name' => 'tmp_filetbl', 		'title' => __('Template: Files Table', 'wp-explorer'), 	 				'input' => 'textarea' );
$wp_explorer_options_map[] = array('name' => 'tmp_filebit', 		'title' => __('Template: Files Bit', 'wp-explorer'), 					'input' => 'textarea' );
$wp_explorer_options_map[] = array('name' => 'tmp_error', 			'title' => __('Template: Error', 'wp-explorer'), 	 					'input' => 'textarea' );
$wp_explorer_options_map[] = array('name' => 'exclude_files', 		'title' => __('Exclude Files From Listing', 'wp-explorer'),  			'input' => 'text' );
$wp_explorer_options_map[] = array('name' => 'exclude_folders', 	'title' => __('Exclude Folders From Listing', 'wp-explorer'), 			'input' => 'text' );
$wp_explorer_options_map[] = array('name' => 'exclude_extensions', 	'title' => __('Exclude Extensions From Listing', 'wp-explorer'), 		'input' => 'text' );
$wp_explorer_options_map[] = array('name' => 'enable_antileech', 	'title' => __('Enable htaccess anti-leech protection', 'wp-explorer'), 	'input' => 'radioyesno' );
$wp_explorer_options_map[] = array('name' => 'enable_fancylinks', 	'title' => __('Enable Pretty Links', 'wp-explorer'), 					'input' => 'radioyesno' );
?>

<div class="wrap"> 
    <h2><?php _e('WP Explorer Options', 'wp-explorer'); ?></h2>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"> 
    <?php wp_nonce_field('update-wp-explorer-options') ?>
    <table class="form-table">
    <?php 
        foreach($wp_explorer_options_map as $opt):
            $id 	= 	$opt['name'];
            $name 	= 	'wp_explorer_options['.$opt['name'].']';
            $value 	= 	attribute_escape($wp_explorer_options[$id]);
            ?>
            <tr valign="top">
                <th scope="row" nowrap="nowrap"><?php echo $opt['title']; ?>:</th>
                <td>
				<?php   
                if($opt['input'] == 'textarea'): ?>
                <textarea cols="80" rows="3" id="<?php echo $id; ?>" name="<?php echo $name; ?>"><?php echo $value; ?></textarea> <?php 
					if(substr($id, 0, 4) == 'tmp_'): ?>
					<br /><input type="checkbox" name="revert[<?php echo $id; ?>]"  value="1" /> <?php _e('Revert Template', 'wp-explorer');
					endif;
                elseif($opt['input'] == 'text'): ?>
                <input size="80" type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" /><br />
                <?php _e('Separate with commas', 'wp-explorer');
                elseif($opt['input'] == 'radioyesno'): ?>
                <input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="1" <?php echo ($value ? 'checked="checked"' : ''); ?>/><?php _e('Yes', 'wp-explorer'); ?> 
                <input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="0" <?php echo (!$value ? 'checked="checked"' : ''); ?>/><?php _e('No', 'wp-explorer'); ?> 
                <?php endif; ?>
                </td>
            </tr>
		<?php endforeach; ?>
        <tr>
            <td colspan="2" align="center">
            	<input type="hidden" name="do" value="update" />
                <input type="submit" name="submit" class="button" value="<?php _e('Save Changes', 'wp-explorer'); ?>" /> 
            </td>
        </tr> 
    </table>
    </form>
</div>