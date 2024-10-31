<?php
/**
 * PostaPanduri Uninstall
 *
 * Uninstalling PostaPanduri deletes cron events.
 *
 * @package PostaPanduri\Uninstaller
 * @version 1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

wp_clear_scheduled_hook('postapanduri_generate_sitemaps_event');
