<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://postapanduri.ro
 * @since      2.0.3
 *
 * @package    PostaPanduri
 * @subpackage PostaPanduri/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PostaPanduri
 * @subpackage PostaPanduri/public
 * @author     Adrian Lado <adrian@plationline.eu>
 */

namespace Postapanduri\Inc\Front;

use PostaPanduri as NS;
use PostaPanduri\Inc\Libraries\LO;

class Pachetomate
{
    private $plugin_name;
    private $version;
    private $plugin_text_domain;

    public function __construct($plugin_name, $version, $plugin_text_domain)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
    }

    public function pachetomate_init()
    {
        add_rewrite_tag('%pachetomat%', '([0-9]+)', 'pachetomat=');
        add_rewrite_tag('%view%', '([a-z]+)', 'view=');
        add_rewrite_rule('^pachetomate-postapanduri/([a-z]+)/([0-9]+)-([^/]*)/?$', 'index.php?pachetomat=$matches[2]&view=$matches[1]', 'top');
        add_rewrite_rule('^pachetomate-postapanduri/([a-z]+)/?$', 'index.php?&view=$matches[1]', 'top');
        add_rewrite_rule('^pachetomate-postapanduri/([0-9]+)-([^/]*)/?$', 'index.php?pachetomat=$matches[1]', 'top');
    }

    public function pachetomate_query_vars($query_vars)
    {
        $query_vars[] = 'pachetomat';
        $query_vars[] = 'view';
        return $query_vars;
    }

    public function pachetomate_template_include($template)
    {
        global $wp;
        if (preg_match('#^pachetomate-postapanduri(.*)$#i', $wp->request) === 0) {
            return $template;
        }
        $dp_id = (int)get_query_var('pachetomat');

        $lo = new LO();
        if ($dp_id) {
            $pachetomat = $lo->get_delivery_point_by_id($dp_id);
            if (!empty($pachetomat)) {
                $this->rcp_get_template_part((get_query_var('view') == 'mobile' ? 'mobile/' : '') . 'pachetomat-postapanduri', ['pachetomat' => $pachetomat]);
                return;
            } else {
                wp_safe_redirect(site_url('pachetomate-postapanduri' . (get_query_var('view') == 'mobile' ? '/mobile' : '')));
            }
        } else {
            status_header(200);
        }
        wp_enqueue_style($this->plugin_name . '-select2', NS\PLUGIN_NAME_URL . 'assets/css/select2.min.css', array(), $this->version, 'all');
        wp_register_script($this->plugin_name . '-select2', NS\PLUGIN_NAME_URL . 'assets/js/select2.full.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-select2');
        $statistics = $lo->get_statistics();
        $lista_pachetomate = $lo->get_all_delivery_points_select_pachetomate();
        $select = '<select id="lista-pachetomate"><option></option>';
        if (!empty($lista_pachetomate)) {
            foreach ($lista_pachetomate as $j) {
                $pch = json_decode($j->pachetomate);
                $select .= '<optgroup label="' . $j->dp_judet . ', ' . $j->dp_oras . '">';
                foreach ($pch as $p) {
                    $select .= '<option data-url="' . esc_url(site_url('pachetomate-postapanduri/' . (get_query_var('view') == 'mobile' ? 'mobile/' : '') . $p->dp_id . '-' . sanitize_title(preg_replace("/\([^)]+\)/", "", $p->dp_denumire)))) . '" value="' . $p->dp_id . '">#' . $p->dp_id . ' - ' . esc_html(trim($p->dp_denumire)) . '</option>';
                }
                $select .= '</optgroup>';
            }
        }
        $select .= '</select>';

        $this->rcp_get_template_part((get_query_var('view') == 'mobile' ? 'mobile/' : '') . 'pachetomate-postapanduri', ['nr_pachetomate' => $statistics->nr_pachetomate, 'nr_localitati' => $statistics->nr_localitati, 'nr_statii_postale' => $statistics->nr_statii_postale, 'select' => $select]);
    }

    public function rcp_locate_template($template_names, $load = false, $require_once = true)
    {
        $located = false;
        foreach ((array)$template_names as $template_name) {
            if (empty($template_name)) {
                continue;
            }
            $template_name = ltrim($template_name, '/');
            if (file_exists(plugin_dir_path(__FILE__) . 'templates/') . $template_name) {
                $located = plugin_dir_path(__FILE__) . 'templates/' . $template_name;
                break;
            }
        }
        if ((true == $load) && !empty($located)) {
            load_template($located, $require_once);
        }
        return $located;
    }

    public function rcp_get_template_part($slug, $data, $name = null, $load = true)
    {
        foreach ($data as $key => $value) {
            set_query_var($key, $value);
        }
        do_action('get_template_part_' . $slug, $slug, $name);
        // Setup possible parts
        $templates = array();
        if (isset($name)) {
            $templates[] = $slug . '-' . $name . '.php';
        }

        $templates[] = $slug . '.php';

        // Return the part that is found
        return $this->rcp_locate_template($templates, $load, false);
    }

    public function postapanduri_create_sitemaps()
    {
        $lo = new LO();
        $pachetomate = $lo->get_all_delivery_points_location_by_judet('', 1, false);

        $sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $sitemap .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $images_sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $images_sitemap .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" \n xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">\n";

        if (!empty($pachetomate)) {
            foreach ($pachetomate as $pachetomat) {
                $sitemap .= "<url>\n" .
                    "<loc>" . esc_url(site_url('pachetomate-postapanduri/' . $pachetomat->dp_id . '-' . sanitize_title(preg_replace("/\([^)]+\)/", "", $pachetomat->dp_denumire)))) . "</loc>\n" .
                    "<lastmod>" . date('Y-m-d\TH:i:s\Z') . "</lastmod>\n" .
                    "<changefreq>weekly</changefreq>\n" .
                    "<priority>0.5</priority>\n" .
                    "</url>\n";

                $images_sitemap .= "<url>\n" .
                    "<loc>" . esc_url(site_url('pachetomate-postapanduri/' . $pachetomat->dp_id . '-' . sanitize_title(preg_replace("/\([^)]+\)/", "", $pachetomat->dp_denumire)))) . "</loc>\n" .
                    "<image:image>\n" .
                    "<image:loc>" . esc_url($pachetomat->img_pachetomat) . "</image:loc>\n" .
                    "<image:caption>" . $pachetomat->dp_indicatii . "</image:caption>\n" .
                    "<image:geo_location>" . ($pachetomat->dp_oras . ', ' . $pachetomat->dp_judet . ', ' . $pachetomat->dp_tara) . "</image:geo_location>\n" .
                    "<image:title>" . trim(preg_replace("/\([^)]+\)/", "", $pachetomat->dp_denumire)) . "</image:title>\n" .
                    "<image:license>https://livrarionline.ro/ro/termeni-si-conditii/</image:license>\n" .
                    "</image:image>\n" .
                    "</url>\n";
            }
        }
        $sitemap .= "</urlset>";
        $fp = fopen(ABSPATH . "sitemap-pachetomate-postapanduri.xml", 'w');
        fwrite($fp, $sitemap);
        fclose($fp);

        $images_sitemap .= "</urlset>";
        $fp = fopen(ABSPATH . "sitemap-images-pachetomate-postapanduri.xml", 'w');
        fwrite($fp, $images_sitemap);
        fclose($fp);

    }
}
