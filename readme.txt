=== Plugin Name ===
Contributors: Tefra
Donate link: http://www.t3-design.com/donate/
Tags: files, browse, explorer
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 0.5

WP Explorer is an easy way to list directories and files on your server.

== Description ==

WP Explorer is an easy way to list directories and files on your server. Through a simple shortcode you can transform your posts and pages to file browsers.

Features
- Easy Configuration.
- Easy modification since you have control over templates from the options page.
- Exclude files, folders, extensions from listing.
- Protection from listing directories outside the specified one.
- Hot-linking protection through htaccess.
- Pretty links.
- 100% Ready for translations.
- Ability to include file version through a specific filename format.

Directories listing information
- Name
- Last Modification Date

Files listing information
- Name
- Size
- Last Modification Date
- Extension icon
- Version *

Keep in mind the version must be numeric 1.0a for example won't work. To include a version to your files use this format (name)_(version).(ext) e.g. guestbook_100.rar, test_wp_oti_nani_100.txt, email.change_1.0.zip

= What's New=

Version 0.5
-----------
- Fixed Bug with spaces in folder names.
- Fixed Bug with wrong check when hotlinking protection is disabled.

Version 0.4
-----------
First Initial Release


== Installation ==

1. Upload the folder wp-explorer to the /wp-content/plugins/`directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Check the settings from the settings submenu WP Explorer.
4. Create posts, pages with the shortcode [wp_explorer]dir/path[/wp_explorer]

e.g. [wp_explorer]wp-content/folder_name[/wp_explorer]

Don't use an ending slash

== Frequently Asked Questions ==

= I get this "ERROR: I have no right to access this folder or this folder doesn't exist"=

When this message appears means, either the folder needs a higher chmod to allow reading or that the giver dir path is wrong.

= I get a php error eval something =

Check your templates, check for any missing bracket [ ],
Right $phrase[size]
Wrong $phrase['size']
Wrong $phrase[size

Try to revert the templates from the options page and contact me if you still can't see where you messed up.

= The hotlink protections redirects to an old post/page =

If you deleted the page or the post that included a working wp explorer shortcode and you had anti-leech protection on and you created a new page post witht the same dir path in the shortcoce then you need to remove the htaccess file from the directory and let it regenerate.

To do so there are two ways:
- Turn off anti-leech and open the page witht the wp explorer shortcode and then turn the anti-leech on again. This forces the .htaccess file to be deleted and regenerated.
- Delete it manually from your ftp.

== Screenshots ==
I have something better than screenshots, a live demo http://www.t3-design.com/ldu-repo/