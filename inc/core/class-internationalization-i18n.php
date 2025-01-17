<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://postapanduri.ro
 * @since      1.0.0
 *
 * @package    PostaPanduri
 * @subpackage PostaPanduri/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    PostaPanduri
 * @subpackage PostaPanduri/includes
 * @author     Adrian Lado <adrian@plationline.eu>
 */

namespace Postapanduri\Inc\Core;

class Internationalization_I18n
{

    /**
     * The text domain of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $text_domain The text domain of the plugin.
     */
    private $text_domain;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_text_domain The text domain of this plugin.
     * @since    1.0.0
     *
     */
    public function __construct($plugin_text_domain)
    {
        $this->text_domain = $plugin_text_domain;
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            $this->text_domain,
            false,
            $this->text_domain . '/languages/'
        );
    }
}
