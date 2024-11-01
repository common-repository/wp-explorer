<?php

function wp_explorer_opmap()
{
	$options = array();
	$options['tmp_filebit'] = '<tr>
    <td class="$file[class]"><img src="$siteurl/wp-content/plugins/wp-explorer/icons/$file[icon]" alt="" /> <a href="$siteurl/$file[url]">$file[name]</a></td>
    <td class="$file[class]" align="center">$file[size]</td>
    <td class="$file[class]" align="center">$file[version]</td>
    <td class="$file[class]" align="center">$file[date]</td>
</tr>';
	$options['tmp_dirbit'] = '<tr>
    <td class="$file[class]"><img src="$siteurl/wp-content/plugins/wp-explorer/icons/$file[icon]" alt="" /> <a href="$file[url]">$file[name]</a></td>
    <td class="$file[class]" align="center">$file[date]</td>
</tr>';
	
	$options['tmp_filetbl'] = '<table class="widefat">
<thead>
	<tr>
		<td class="thead">$phrase[name]</td>
		<td class="thead" align="center">$phrase[size]</td>
		<td class="thead" align="center">$phrase[version]</td>
		<td class="thead" align="center">$phrase[date]</td>
	</tr>
</thead>
$filebit
</table>';

	$options['tmp_dirtbl'] = '<table class="widefat">
<thead>
	<tr>
		<td class="thead">$phrase[name]</td>
		<td class="thead" align="center">$phrase[date]</td>
	</tr>
</thead>
$dirbit
</table>';
	$options['tmp_main'] = '$display_dirs
$display_files
<div style="color:#739E48; font-size:10px; font-weight:bold; text-align:right; padding:2px 1px 6px 0px;">$phrase[location] $location | $leech_protection  | $folder_stats</div>';
	$options['tmp_error'] = '<p class="error">$error_message</p>';

	$options['exclude_files'] = '.htaccess,.htpasswd,index.html,index.htm,index.php,index.asp';
	$options['exclude_folders'] = 'folder1,folder2';
	$options['exclude_extensions'] = 'php,php3,php4';
	$options['enable_fancylinks'] = 0;
	$options['enable_antileech'] = 0;
	return $options;
}

function wp_explorer_preptmp($code)
{
	$code = addslashes($code);
	$code = str_replace("\\'", "'", $code);
	return $code;
}

function wp_explorer_sanitize($file, $ext, $version)
{
	$file = basename($file, $ext);
	$file = str_replace($version, '', $file);
	$file = preg_replace( array('/\./', '/\-/', '/\_/' ), ' ', $file);
	$file = ucwords(strtolower($file));
	return $file;
}

?>