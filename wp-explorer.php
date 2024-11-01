<?php
/*
Plugin Name: WP Explorer
Plugin URI: http://www.t3-design.com/
Description: A simple file indexing plugin with custom rules
Version: 0.5
Author: Chris T.
Author URI: http://www.t3-design.com/
*/

add_action('init', 'wp_explorer_getvar');
function wp_explorer_getvar($var = '')
{
   if (!in_array($var, array('REQUEST_URI', 'PATH_INFO'))) $var = 'REQUEST_URI';
   $req = $_SERVER[$var];
   if (preg_match('!^(.+/)browse/?(.*)?$!', $req, $match) && (url_to_postid($req) == 0)) 
   {
       $_GET['browse'] = $match[2];
       $req = $match[1].'?browse='.$match[2];
       $_SERVER[$var] = $req;
   }
   if (($var != 'PATH_INFO') && isset($_SERVER['PATH_INFO'])) 
   {
       wp_explorer_getvar('PATH_INFO');
   }
} 

add_action('admin_menu', 'wp_explorer_menu');
function wp_explorer_menu() 
{
	if(function_exists('add_menu_page')) 
	{
		add_menu_page('WP Explorer', 'WP Explorer', 'manage_options', 'wp-explorer/wp-explorer-options.php') ;
	}

}

add_action('activate_wp-explorer/wp-explorer.php', 'wp_explorer_install');
function wp_explorer_install() 
{
	require ('inc/functions_misc.php');
	add_option('wp_explorer_options', wp_explorer_opmap(), '', 'no');
}
	
add_action('deactivate_wp-explorer/wp-explorer.php', 'wp_explorer_uninstall');	
function wp_explorer_uninstall()
{
	delete_option('wp_explorer_options');
}

add_filter('single_post_title',  wp_explorer_location,  99999, 1);
function wp_explorer_location()
{
	$arg		=	func_get_args();
	$nargs 		= 	func_num_args();
	$folders 	= 	array_map("urldecode", array_values(array_filter(explode('/', $_GET['browse']))));
	if($nargs == 1)
	{
		$title = $arg[0];
		return (!count($folders) ? $title : $title.' &raquo; '.implode(' &raquo; ', $folders));
	}
	elseif($nargs == 2)
	{
		$url 	= 	$arg[0];
		$pretty = 	$arg[1];
		$loc[] = __('Root', 'wp-explorer');
	}
	else { die("I don't Know You");}

	foreach($folders AS $folder)
	{
		$tmp 	.= 	$folder;
		$tlink 	= 	str_replace('%browse%', $tmp, $url);
		$tlink 	=	$pretty ? str_replace(array('/?browse=', '?browse=', '&browse='), '/browse/', $tlink) : $tlink;
		$loc[] 	= 	'<a href="'.$tlink.'">'.$folder.'</a>';
		$tmp 	.= 	'/';
	}
	return implode((count($loc) > 1 ? ' &raquo; ' : ''), $loc);
}

add_shortcode('wp_explorer', 'wp_explorer_caption');
function wp_explorer_caption( $atts, $content = null ) 
{
	if(trim($content) == null) return;
	load_plugin_textdomain('wp-explorer', 'wp-content/plugins/wp-explorer');
	require ('inc/functions_misc.php');
	
	$cfg 		= 	get_option('wp_explorer_options'); 
	$no_folders = 	explode(',', $cfg['exclude_folders']);
	$no_files 	= 	explode(',', $cfg['exclude_files']);
	$no_ext 	= 	explode(',', $cfg['exclude_extensions']);
	array_walk($no_folders, 'trim');
	array_walk($no_files, 'trim');
	array_walk($no_ext, 'trim');

	$main_dir 	= 	trim(trim($content), '/');
	$condom 	= 	$main_dir.'/.htaccess';
	$siteurl 	= 	get_bloginfo('siteurl');
	$url 		= 	get_permalink();
	
	if($cfg['enable_antileech'])
	{
		if(!file_exists($condom))
		{
			$file = @fopen($condom, 'w');
			if($file) 
			{	
				$patterns = array('http://', 'https://', 'www.', 'WWW.');	
				$cofipr = "RewriteEngine on\n";
				$cofipr .= "RewriteCond %{HTTP_REFERER} !^http://(www\.)?".str_replace($patterns, '', $siteurl)."/(/)?.*$ [NC]\n";
				$cofipr .= "RewriteRule .*\.*$  http://".str_replace($patterns, '', $url)." [R,NC]\n";
				$cofipr .= "Options -Indexes";
	
				fwrite($file, $cofipr);
				fclose($file);
				$leech_protection = __('Leech protection: ON', 'wp-explorer');
			}	
			else
			{
				$leech_protection = __('Leech protection: Failed', 'wp-explorer');
			}
		}
		else
		{
			$leech_protection = __('Leech protection: ON', 'wp-explorer');
		}
	}
	elseif(!$cfg['enable_antileech'])
	{
		if(file_exists($condom)) unlink($condom);
		$leech_protection = __('Leech protection: OFF', 'wp-explorer');
	}
	
	if($_GET['browse'] != '')
	{
		$_GET['browse'] = str_replace('<', '&lt;', trim($_GET['browse']));
		$f = $main_dir.'/'.urldecode($_GET['browse']);
	}

	if(is_dir($f)) 
	{
		$patterns = array(".", "/");
		$real = str_replace($patterns, "", $main_dir);
		$check = str_replace($patterns, "", $f);
		$re = strlen($real);
		if(substr($check, 0, $re)!= $real) $f = $main_dir; 
		if(substr($f,0,2) == ".." || substr($f,0,1) == "/" || $f == "./" || stristr($f, '../')) $f = $main_dir; 
	}
	else
	{
		$f = $main_dir;
	}

	$files = array();
	if(is_dir($f) && $handle = opendir($f)) 
	{
		$phrase['location'] = 	__('You are here:', 'wp-explorer');
		$basedirurl 		= 	add_query_arg('browse', '%browse%', $url);
		$location 			= 	wp_explorer_location($basedirurl, $cfg['enable_fancylinks']);
	
		while(false !==($file = readdir($handle))) 
		{ 
			if($file != '..' && $file != '.')
			{
				$filesize = @filesize($f.'/'.$file);
				$files[] = array("name" => $file, "size" => size_format($filesize, 2), "date" => gmdate("d F Y",filemtime($f.'/'.$file)));	
			}
		}
		closedir($handle);
		sort($files);
		
		$dir_jj = $file_ii = 0;
		foreach($files as $file)
		{
			$file['address'] = $f.'/'.$file['name'];
			if(is_dir($file['address']) && !in_array($file['name'], $no_folders))
			{
				$dir_jj++;
				$file['icon']	=	'folder.gif';
				$file['url']	= 	urlencode($f.'/'.$file['name']);
				$file['url'] 	= 	str_replace('%2F', '/', $file['url']);
				$file['url'] 	= 	str_replace($main_dir.'/', '', $file['url']);
				$file['url']	=	str_replace('%browse%', $file['url'], $basedirurl);
				$file['url'] 	= 	$cfg['enable_fancylinks'] ? str_replace(array('/?browse=', '?browse=', '&browse='), '/browse/', $file['url']) : $file['url'];
				$file['class'] 	= 	$dir_jj % 2 == 0 ? 'even' : 'odd';
				eval('$dirbit .= "' . wp_explorer_preptmp($cfg['tmp_dirbit']) . '";');
			}
			elseif(is_file($file['address']) && !in_array($file['name'], $no_files))
			{
				$file['ext'] = strtolower(substr(strrchr($file['name'], '.'), 1));
				if(in_array($file['ext'], $no_ext)) continue;
				
				$file_ii++;
				$filename			=	explode("_", $file['name']);
				$version 			= 	str_replace('.'.$file['ext'], '', end($filename));
				$version			=	is_numeric($version) ? $version : '';
				$file['version']	=	empty($version) ? '&nbsp;' : $version;
				$file['icon'] 		= 	substr(strrchr($file['name'], '.'), 1).'.gif';
				$file['icon'] 		=	file_exists('wp-content/plugins/wp-explorer/icons/'.$file['icon']) ? $file['icon'] : "unknown.png";
				$file['name']		=	wp_explorer_sanitize($file['name'], $file['ext'], $version);
				$file['url']		= 	$file['address'];
				$file['class'] 		= 	$file_ii % 2 == 0 ? 'even' : 'odd';
				eval('$filebit .= "' . wp_explorer_preptmp($cfg['tmp_filebit']) . '";');
			}			
		}
		$phrase['name'] = __('Name', 'wp-explorer');
		$phrase['date'] = __('Last Modification Date', 'wp-explorer');
	
		if($dirbit)
		{
			eval('$display_dirs .= "' . wp_explorer_preptmp($cfg['tmp_dirtbl']) . '";');
		}
		if($filebit)
		{
			$phrase['size'] 	= __('Size', 'wp-explorer');
			$phrase['version'] 	= __('Version', 'wp-explorer');
			eval('$display_files .= "' . wp_explorer_preptmp($cfg['tmp_filetbl']) . '";');
		}
		$folder_stats = sprintf(__('There are %1$s folders and %2$s files here.', 'wp-explorer'), $dir_jj, $file_ii);
		eval('$html .= "' . wp_explorer_preptmp($cfg['tmp_main']) . '";');
	}
	else
	{
		$error_message = __("ERROR: I have no right to access this folder or this folder doesn't exist.", 'wp-explorer');
		eval('$html .= "' . wp_explorer_preptmp($cfg['tmp_error']) . '";');
	}
	return $html;
}

?>