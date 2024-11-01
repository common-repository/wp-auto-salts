<?php
/**
 * Plugin Name: WP Auto Salts
 * Plugin URI:  http://wordpress.org/plugins/wp-auto-salts/
 * Description: Renews the security keys and salts in wp-config.php on a weekly schedule.
 * Version:     0.9
 * Author:      Ezra Verheijen
 * Author URI:  http://profiles.wordpress.org/ezraverheijen/
 * License:     GPL v3
 * 
 * Copyright (c) 2014, Ezra Verheijen
 * 
 * WP Auto Salts is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * WP Auto Salts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have recieved a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses>.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // exit if accessed directly
}

if ( ! class_exists( 'WP_Auto_Salts' ) ) {
    
    class WP_Auto_Salts {

        /**
         * Holds the absolute filesystem path to wp-config.php.
         * 
         * @var string
         */
        private $wp_config;

        /**
         * Class constructor.
         * 
         * - Determines the full filesystem path to wp-config.php.
         * - Adds the weekly cron to the existing schedules.
         * - Schedules a hook which will be executed by the WordPress actions core on the weekly interval.
         * - Attaches the auto salts functionality to the weekly hook.
         * - Registers activation hook which will add new security keys and salts to wp-config.php for the first time.
         * - Registers plugin deactivation hook which will remove everything from the weekly schedule.
         */
        function __construct() {

            $this->wp_config = $this->get_wp_config_path();

            add_filter( 'cron_schedules', array( $this, 'add_weekly_cron' ) );

            add_action( 'wp', array( $this, 'setup_salts_update_schedule' ) );
            add_action( 'wp_auto_salts', array( $this, 'add_salts_to_wp_config' ) );

            register_activation_hook( __FILE__, array( $this, 'add_salts_to_wp_config' ) );
            register_deactivation_hook( __FILE__, array( $this, 'remove_weekly_cron' ) );

        }

        /**
         * Get the absolute filesystem path to wp-config.php.
         * 
         * @return string|boolean Full filesystem path if determined, false otherwise.
         */
        function get_wp_config_path() {

            $paths = array(
                ABSPATH . 'wp-config.php',
                dirname( ABSPATH ) . '/wp-config.php'
            );

            foreach ( $paths as $path ) {
                if ( file_exists( $path ) ) {
                    return $path;
                }
            }

            return false;

        }

        /**
         * Adds once weekly to the existing schedules.
         * 
         * @param  array $schedules Existing schedules.
         * @return array Added weekly schedule.
         */
        function add_weekly_cron( $schedules ) {
            
            $schedules['weekly'] = array(
                'interval' => 604800,
                'display'  => __( 'Once Weekly' )
            );
            
            return $schedules;

         }

        /**
         * On an early action hook, check if the hook is scheduled - if not, schedule it.
         */
        function setup_salts_update_schedule() {

            if ( ! wp_next_scheduled( 'wp_auto_salts' ) ) {
                wp_schedule_event( time(), 'weekly', 'wp_auto_salts' );
            }

        }

        /**
         * Check if it's possible to write to wp-config.php.
         * 
         * @return boolean True if the file is writeable, otherwise false.
         */
        function can_write_to_wp_config() {

            if ( is_writable( $this->wp_config ) ) {
                return true;
            }

            return false;

        }

        /**
         * Get new security keys and salts from the online WordPress secret-key generator.
         * 
         * @return string Unique security keys and salts.
         */
        function get_salts() {

            return trim( preg_replace( '/\s\s+/', ' ', file_get_contents( 'https://api.wordpress.org/secret-key/1.1/salt' ) ) );

        }
        
        /**
         * Add new unique security keys and salts to wp-config.php.
         * 
         * @return null Return null if wp-config.php doesn't exist, is not writeable or if file contents can't be retrieved.
         */
        function add_salts_to_wp_config() {

            if ( false === $this->wp_config || ! $this->can_write_to_wp_config() ) {
                return null;
            }

            $config_contents = @file_get_contents( $this->wp_config );
            if ( false === $config_contents ) {
                return null;
            }

            $old_prefix = '/**#@+';

            $new_prefix = '# BEGIN WP Auto Salts';
            $new_suffix = '# END WP Auto Salts';

            if ( false !== strpos( $config_contents, $old_prefix ) ) {
                $config_contents = str_replace( array( $old_prefix, '/**#@-*' ), array( $new_prefix, $new_suffix ), $config_contents );
                $config_contents = str_replace( $new_suffix . '/', $new_suffix, $config_contents );
            }

            if ( false !== strpos( $config_contents, $new_prefix ) ) {
                $config_contents = preg_replace(
                    '/\\' . $new_prefix . '(.*?)\\' . $new_suffix . '/s',
                    $new_prefix . PHP_EOL . $this->get_salts() . PHP_EOL . $new_suffix,
                    $config_contents
                );
            }

            @file_put_contents( $this->wp_config, $config_contents, LOCK_EX );

        }

        /**
         * On deactivation, remove all functions from the scheduled action hook.
         */
        function remove_weekly_cron() {

            wp_clear_scheduled_hook( 'wp_auto_salts' );

        }

    }

    new WP_Auto_Salts();
}
