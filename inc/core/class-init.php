<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://postapanduri.ro
 * @since      1.0.0
 *
 * @package    PostaPanduri
 * @subpackage PostaPanduri/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PostaPanduri
 * @subpackage PostaPanduri/includes
 * @author     Adrian Lado <adrian@plationline.eu>
 */

namespace PostaPanduri\Inc\Core;

use PostaPanduri as NS;
use PostaPanduri\Inc\Admin as Admin;
use PostaPanduri\Inc\Admin\SettingsPage as SettingsPage;
use PostaPanduri\Inc\Core\Internationalization_I18n as Internationalization_I18n;
use PostaPanduri\Inc\Core\WC_PostaPanduri as WC_PostaPanduri;
use PostaPanduri\Inc\Front as Front;


class Init
{
    protected $plugin_basename;
    protected $plugin_name;
    protected $version;
    protected $plugin_text_domain;
    protected $loader;

    public function __construct()
    {
        $this->plugin_name = NS\PLUGIN_NAME;
        $this->version = NS\PLUGIN_VERSION;
        $this->plugin_basename = NS\PLUGIN_BASENAME;
        $this->plugin_text_domain = NS\PLUGIN_TEXT_DOMAIN;

        $this->load_dependencies();
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->loader->add_action('admin_notices', $this, 'pp_missing_wc_notice');
            return;
        }
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->load_woocommerce_class();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - PostaPanduri_Loader. Orchestrates the hooks of the plugin.
     * - PostaPanduri_i18n. Defines internationalization functionality.
     * - PostaPanduri_Admin. Defines all hooks for the admin area.
     * - PostaPanduri_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the PostaPanduri_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Internationalization_I18n($this->plugin_text_domain);
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function load_woocommerce_class()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->loader->add_action('woocommerce_shipping_init', $this, 'postapanduri_init');
            $this->loader->add_action('init', $this, 'postapanduri_init_order_status');
        }
    }

    public function postapanduri_init_order_status()
    {
        if (\class_exists('WC_Shipping_Method')) {
            $pp = new WC_PostaPanduri();
            $pp::pp_register_shipment_status();
        }
    }

    public function postapanduri_init()
    {
        add_filter('woocommerce_shipping_methods', array($this, 'add_postapanduri'));
    }

    public function add_postapanduri($methods)
    {
        $methods['postapanduri'] = 'PostaPanduri\Inc\Core\WC_PostaPanduri';
        return $methods;
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $this->loader->add_action('admin_init', Activator::class, 'install_db');

        $plugin_admin = new Admin\Admin($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        if (is_admin()) {
            $postapanduri_settings_page = new SettingsPage($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());
        }

        $this->loader->add_action('wp_ajax_genereaza_awb', $plugin_admin, 'genereaza_awb');
        $this->loader->add_action('wp_ajax_cancel_awb', $plugin_admin, 'cancel_awb');
        $this->loader->add_action('wp_ajax_tracking_awb', $plugin_admin, 'tracking_awb');
        $this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'order_status_changed', 10, 3);

        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_meta_box_pp');

        add_action('before_woocommerce_init', function () {
            if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', $this->plugin_basename, true);
            }
        });
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Front\Front($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());
        $plugin_pagini_pachetomate = new Front\Pachetomate($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_ajax_ajax_get_judete', $plugin_public, 'ajax_get_judete');
        $this->loader->add_action('wp_ajax_nopriv_ajax_get_judete', $plugin_public, 'ajax_get_judete');

        $this->loader->add_action('wp_ajax_ajax_get_localitati', $plugin_public, 'ajax_get_localitati');
        $this->loader->add_action('wp_ajax_nopriv_ajax_get_localitati', $plugin_public, 'ajax_get_localitati');
        $this->loader->add_action('wp_ajax_ajax_load_map', $plugin_public, 'ajax_load_map');
        $this->loader->add_action('wp_ajax_nopriv_ajax_load_map', $plugin_public, 'ajax_load_map');
        $this->loader->add_action('wp_ajax_ajax_get_pachetomate', $plugin_public, 'ajax_get_pachetomate');
        $this->loader->add_action('wp_ajax_nopriv_ajax_get_pachetomate', $plugin_public, 'ajax_get_pachetomate');
        $this->loader->add_action('wp_ajax_ajax_get_pachetomat', $plugin_public, 'ajax_get_pachetomat');
        $this->loader->add_action('wp_ajax_nopriv_ajax_get_pachetomat', $plugin_public, 'ajax_get_pachetomat');

        $this->loader->add_action('woocommerce_after_shipping_rate', $plugin_public, 'pp_action_woocommerce_after_shipping_rate', 10, 2);
        $this->loader->add_action('woocommerce_checkout_update_order_review', $plugin_public, 'pp_action_woocommerce_checkout_update_order_review');
        $this->loader->add_action('woocommerce_before_shipping_calculator', $plugin_public, 'pp_action_woocommerce_checkout_update_order_review');
        $this->loader->add_action('woocommerce_checkout_update_order_review', $plugin_public, 'pp_clear_wc_shipping_rates_cache');
        $this->loader->add_action('woocommerce_view_order', $plugin_public, 'postapanduri_tracking_awb');

        $this->loader->add_filter('woocommerce_api_wc_postapanduri_issn', $plugin_public, 'process_issn');

        $this->loader->add_action('woocommerce_after_checkout_validation', $plugin_public, 'postapanduri_validate_order', 10);
        $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'pp_add_delivery_point_id', 10, 2);
        $this->loader->add_action('woocommerce_thankyou', $plugin_public, 'pp_action_woocommerce_thankyou_order_received_text', 10, 1);
        $this->loader->add_action('woocommerce_view_order', $plugin_public, 'pp_action_woocommerce_thankyou_order_received_text', 10, 1);
        $this->loader->add_action('before_woocommerce_pay', $plugin_public, 'pp_action_before_woocommerce_pay', 10, 2);

        $this->loader->add_action('wp_ajax_ajax_set_pachetomat_default', $plugin_public, 'ajax_set_pachetomat_default');
        $this->loader->add_action('wp_ajax_nopriv_ajax_set_pachetomat_default', $plugin_public, 'ajax_set_pachetomat_default');

        // show price for free shipping
        $this->loader->add_filter('woocommerce_cart_shipping_method_full_label', $plugin_public, 'pp_show_zero_price', 10, 2);

        $this->loader->add_filter("plugin_action_links_" . $this->plugin_basename, $this, 'pp_settings_link');

//		$this->loader->add_action('woocommerce_after_add_to_cart_form', $plugin_public, 'pp_product_display_smartlocker_info');
//		$this->loader->add_action('wp_head', $plugin_public, 'pp_add_javascript_for_cookie');

        // set default smartlocker lo
//		$this->loader->add_action('wp_ajax_ajax_set_pachetomat_default_lo', $plugin_public, 'ajax_set_pachetomat_default_lo');
//		$this->loader->add_action('wp_ajax_nopriv_ajax_set_pachetomat_default_lo', $plugin_public, 'ajax_set_pachetomat_default_lo');

        // pagini pachetomate
        $this->loader->add_action('init', $plugin_pagini_pachetomate, 'pachetomate_init', 0);
        $this->loader->add_filter("query_vars", $plugin_pagini_pachetomate, 'pachetomate_query_vars');
        $this->loader->add_action('template_include', $plugin_pagini_pachetomate, 'pachetomate_template_include');
        $this->loader->add_action('postapanduri_generate_sitemaps_event', $plugin_pagini_pachetomate, 'postapanduri_create_sitemaps');
    }

    public function pp_settings_link($links)
    {
        $settings_link = '<a href="' . \admin_url('admin.php?page=postapanduri-setari-generale') . '">' . __('Settings', 'postapanduri') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    PostaPanduri_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    public function get_plugin_text_domain()
    {
        return $this->plugin_text_domain;
    }

    public function pp_missing_wc_notice()
    {
        echo '<div class="error"><p><strong>' . __('PostaPanduri plugin requires WooCommerce to be installed and active', 'postapanduri') . '</strong></p></div>';
    }
}
