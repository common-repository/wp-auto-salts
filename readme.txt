=== WP Auto Salts ===
Contributors: ezraverheijen
Donate link: 
Tags: salts, keys, security, authentication, wp-config.php, wp config
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 0.9
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Renews the security keys and salts in wp-config.php on a weekly schedule.

== Description ==

Lightweight plugin to add some extra security to WordPress.
WP Auto Salts will renew the security salts and keys in wp-config.php on a weekly interval.

NOTE: Make sure the keys and salts in your wp-config.php are between the original markers `/**#@+` and `/**#@-*/` or between `# BEGIN WP Auto Salts` and `# END WP Auto Salts` (on new lines) or WP Auto Salts won't work.

Please keep in mind that WP Auto Salts is still beta software.
If you have any issues using WP Auto Salts, find a bug or have an idea to make the plugin even better then please [help to improve WP Auto Salts](https://github.com/ezraverheijen/wp-auto-salts).
If you don’t report it, I can’t fix it!

== Installation ==

1. Upload the `wp-auto-salts` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. No need to configure, it just works!

== Frequently Asked Questions ==

= After installing WP Auto Salts, I immediately got logged out =

Thats's normal behaviour and actually a good thing. WP Auto Salts will try to renew the security keys and salts on activation of the plugin.
This will immediately invalidate all existing cookies and force all users to have to log in again.

= I installed WP Auto Salts, but nothing changes in wp-config.php =

WP Auto Salts looks for the beginning `/**#@+` and ending `/**#@-*/` in your wp-config.php and replaces everything between it.

`/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'your unique phrase here');
define('SECURE_AUTH_KEY', 'your unique phrase here');
define('LOGGED_IN_KEY', 'your unique phrase here');
define('NONCE_KEY', 'your unique phrase here');
define('AUTH_SALT', 'your unique phrase here');
define('SECURE_AUTH_SALT', 'your unique phrase here');
define('LOGGED_IN_SALT', 'your unique phrase here');
define('NONCE_SALT', 'your unique phrase here');

/**#@-*/`

If these markers are not there, WP Auto Salts does not work.

You can also make WP Auto Salts work by putting the keys and salts between `# BEGIN WP Auto Salts` and `# END WP Auto Salts` :

`# BEGIN WP Auto Salts
define('SECURE_AUTH_KEY', 'your unique phrase here');
define('LOGGED_IN_KEY', 'your unique phrase here');
define('NONCE_KEY', 'your unique phrase here');
define('AUTH_SALT', 'your unique phrase here');
define('SECURE_AUTH_SALT', 'your unique phrase here');
define('LOGGED_IN_SALT', 'your unique phrase here');
define('NONCE_SALT', 'your unique phrase here');
# END WP Auto Salts`

= I installed WP Auto Salts and checked that the markers are there, but still nothing changes in wp-config.php =

It is not possible for WP Auto Salts to write to wp-config.php. This can be caused by having a wrong 'file owner' on the server.
Typically, all files should be owned by your user (ftp) account on your web server, and should be writable by that account. On shared hosts, files should never be owned by the webserver process itself (sometimes this is www, or apache, or nobody user). 
Please (contact your hosting company and have them) reset the file owner.

== Screenshots ==

1. wp-config.php before
2. wp-config.php after

== Changelog ==

= 0.9 =
* Beta release
