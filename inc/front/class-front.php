<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://postapanduri.ro
 * @since      1.0.0
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
use PostaPanduri\Inc\Core\WC_PostaPanduri;
use PostaPanduri\Inc\Libraries\LO;

class Front
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

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in PostaPanduri_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The PostaPanduri_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, NS\PLUGIN_NAME_URL . 'inc/front/css/postapanduri-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-jquery-confirm', NS\PLUGIN_NAME_URL . 'assets/css/jquery-confirm.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-select2', NS\PLUGIN_NAME_URL . 'assets/css/select2.min.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_register_script($this->plugin_name . '-jquery-confirm', NS\PLUGIN_NAME_URL . 'assets/js/jquery-confirm.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-jquery-confirm');

        wp_register_script($this->plugin_name . '-select2', NS\PLUGIN_NAME_URL . 'assets/js/select2.full.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-select2');

        $ajax_params = array('ajaxurl' => admin_url('admin-ajax.php'));

        if (!isset(get_option('postapanduri_setari_generale')['maps_api']) || (isset(get_option('postapanduri_setari_generale')['maps_api'])) && get_option('postapanduri_setari_generale')['maps_api'] == 'gmaps') {
            // daca am GMAPS selectat
            wp_enqueue_script('postapanduri_script_public', NS\PLUGIN_NAME_URL . 'inc/front/js/postapanduri-public-gmaps.js', array(), null, true);
            $gmaps_api_key = !empty(get_option('postapanduri_setari_generale')['gmaps_api_key']) ? get_option('postapanduri_setari_generale')['gmaps_api_key'] : '';
            $this->rcp_get_template_part('plugin-postapanduri-gmaps', array('gmaps_api_key' => $gmaps_api_key));
            wp_register_script("postapanduri_ajax_script", NS\PLUGIN_NAME_URL . 'inc/front/js/postapanduri-public-gmaps-ajax.js', array('jquery', 'wp-i18n'), $this->version, true);
        }

        if (isset(get_option('postapanduri_setari_generale')['maps_api']) && get_option('postapanduri_setari_generale')['maps_api'] == 'mapbox') {
            // daca am MAPBOX selectat
            wp_enqueue_script('postapanduri_script_public', 'https://api.mapbox.com/mapbox-gl-js/v2.0.0/mapbox-gl.js', array(), '2.0.0', true);
            wp_enqueue_style('postapanduri_mapbox', 'https://api.mapbox.com/mapbox-gl-js/v2.0.0/mapbox-gl.css', array(), '2.0.0', 'all');

            $mapbox_api_key = !empty(get_option('postapanduri_setari_generale')['mapbox_api_key']) ? get_option('postapanduri_setari_generale')['mapbox_api_key'] : '';
            wp_register_script("postapanduri_ajax_script", NS\PLUGIN_NAME_URL . 'inc/front/js/postapanduri-public-mapbox-ajax.js', array('jquery', 'wp-i18n'), $this->version, true);
            $ajax_params['mapbox_api_key'] = $mapbox_api_key;
        }

        wp_set_script_translations('postapanduri_ajax_script', 'postapanduri');
        wp_localize_script('postapanduri_ajax_script', 'ppa', $ajax_params);
        wp_enqueue_script('postapanduri_ajax_script');
        wp_enqueue_script('postapanduri_script');
        wp_enqueue_script('postapanduri_script_public');
        wp_set_script_translations('postapanduri_script_public', 'postapanduri');
    }

    public function rcp_locate_template($template_names, $load = false, $require_once = true)
    {
        $located = false;
        foreach ((array)$template_names as $template_name) {
            if (empty($template_name)) {
                continue;
            }
            $template_name = ltrim($template_name, '/');
            if (file_exists(plugin_dir_path(__FILE__) . 'partials/') . $template_name) {
                $located = plugin_dir_path(__FILE__) . 'partials/' . $template_name;
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

    public function ajax_set_pachetomat_default_lo()
    {
        $dulapid = sanitize_text_field($_POST['dulapid']);
        if (empty($dulapid)) {
            echo json_encode(array('status' => 'error', 'message' => 'Smartlocker ID not sent'));
            wp_die();
        }

        $lo = new LO();
        $lo->f_login = (int)WC_PostaPanduri::get_setari_generale('f_login');
        $lo->setRSAKey(WC_PostaPanduri::get_setari_generale('rsa_key'));

        $current_user = wp_get_current_user();

        if (empty($current_user->user_email)) {
            echo json_encode(array('status' => 'error', 'message' => 'No user email found'));
            wp_die();
        }

        $f_request['dulapid'] = (int)$dulapid;
        $f_request['email'] = strtolower($current_user->user_email);

        $response = $lo->SetPachetomatDefault($f_request);

        if (isset($response->status) && ($response->status == 'error')) {
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 'success', 'message' => $response));
        }
        wp_die();
    }

    public function ajax_load_map()
    {
        $lo = new LO();
        // numaram cate puncte de ridicare sunt in db din fiecare tip
        $count = $lo->get_count_delivery_points_by_type();
        $count_array = array();
        if (!empty($count)) {
            foreach ($count as $c) {
                $count_array[$c->dp_tip] = $c->cate;
            }
        }
        $this->rcp_get_template_part('plugin-postapanduri-map',
            array(
                'pp_type' => WC()->session->get('dp_tip'),
                'icon' => NS\PLUGIN_NAME_URL . 'assets/img/location-pin.png',
                'icon_posta' => NS\PLUGIN_NAME_URL . 'assets/img/location-pin-posta.png',
                'count_pp' => isset($count_array[1]) ? (int)$count_array[1] : 0,
                'count_pr' => isset($count_array[0]) ? (int)$count_array[0] : 0,
                'show_pr_delivery_points' => get_option('postapanduri_setari_pachetomat')['show_pr_delivery_points'],
            )
        );
        die();
    }

    public function ajax_get_judete()
    {
        $lo = new LO();
        $judete = $lo->get_all_delivery_points_states((int)sanitize_text_field($_POST['dp_tip']));
        $pachetomate = $lo->get_all_delivery_points_location_by_judet(sanitize_text_field($_POST['judet']), (int)sanitize_text_field($_POST['dp_tip']));
        echo json_encode(array('judete' => $judete, 'pachetomate' => $pachetomate, 'judet_selectat' => WC()->session->get('judet'), 'localitate_selectata' => WC()->session->get('oras'), 'pachetomat_selectat' => WC()->session->get('dp_id'), 'selected_name' => WC()->session->get('dp_name'), 'tip_selectat' => WC()->session->get('dp_tip'), 'icon' => NS\PLUGIN_NAME_URL . 'assets/img/location-pin.png', 'icon_posta' => NS\PLUGIN_NAME_URL . 'assets/img/location-pin-posta.png'));
        wp_die();
    }

    public function ajax_get_localitati()
    {
        $lo = new LO();
        $localitati = $lo->get_all_delivery_points_location_by_state(sanitize_text_field($_POST['judet']), (int)sanitize_text_field($_POST['dp_tip']));
        $pachetomate = $lo->get_all_delivery_points_location_by_judet(sanitize_text_field($_POST['judet']), (int)sanitize_text_field($_POST['dp_tip']));
        echo json_encode(array('count' => count($localitati), 'localitati' => $localitati, 'pachetomate' => $pachetomate, 'judet_selectat' => WC()->session->get('judet'), 'localitate_selectata' => WC()->session->get('oras'), 'pachetomat_selectat' => WC()->session->get('dp_id'), 'selected_name' => WC()->session->get('dp_name'), 'tip_selectat' => WC()->session->get('dp_tip')));
        wp_die();
    }

    public function ajax_get_pachetomate()
    {
        $lo = new LO();
        $pachetomate = $lo->get_all_delivery_points_location_by_localitate(sanitize_text_field($_POST['judet']), sanitize_text_field($_POST['localitate']), (int)(sanitize_text_field($_POST['dp_tip'])));
        echo json_encode(array('count' => count($pachetomate), 'pachetomate' => $pachetomate, 'localitate_selectata' => WC()->session->get('oras'), 'pachetomat_selectat' => WC()->session->get('dp_id'), 'selected_name' => WC()->session->get('dp_name'), 'tip_selectat' => WC()->session->get('dp_tip')));
        wp_die();
    }

    public function ajax_get_pachetomat()
    {
        $lo = new LO();

        $pachetomat = $lo->get_delivery_point_by_id(sanitize_text_field($_POST['pachetomat']));
        if (!empty($pachetomat)) {
            WC()->session->set('judet', $pachetomat->dp_judet);
            WC()->session->set('oras', $pachetomat->dp_oras);
            WC()->session->set('dp_id', $pachetomat->dp_id);
            WC()->session->set('dp_name', $pachetomat->dp_denumire);
            WC()->session->set('dp_tip', $pachetomat->dp_tip);

            if ($pachetomat->dp_tip == 0) {
                $dp_type_text = __('Posta Romana', 'postapanduri');
            } elseif ($pachetomat->dp_tip == 1) {
                $dp_type_text = __('PostaPanduri Smartlocker', 'postapanduri');
            }

            echo json_encode(array('pachetomat' => $pachetomat, 'pachetomat_selectat' => WC()->session->get('dp_id'), 'selected_name' => WC()->session->get('dp_name'), 'tip_selectat' => WC()->session->get('dp_tip'), 'tip_selectat_text' => $dp_type_text));
        }
        wp_die();
    }

    public function ajax_set_pachetomat_default()
    {
        $lo = new LO();

        $pachetomat = $lo->get_delivery_point_by_id(sanitize_text_field($_POST['pachetomat']));
        if (!empty($pachetomat) && $pachetomat->dp_active > 0 && $pachetomat->dp_active != 10) {
            WC()->session->set('judet', $pachetomat->dp_judet);
            WC()->session->set('oras', $pachetomat->dp_oras);
            WC()->session->set('dp_id', $pachetomat->dp_id);
            WC()->session->set('dp_name', $pachetomat->dp_denumire);
            WC()->session->set('dp_tip', $pachetomat->dp_tip);

            if ($pachetomat->dp_tip == 0) {
                $dp_type_text = __('Posta Romana', 'postapanduri');
            } elseif ($pachetomat->dp_tip == 1) {
                $dp_type_text = __('PostaPanduri Smartlocker', 'postapanduri');
            }

            echo json_encode(array('pachetomat' => $pachetomat, 'pachetomat_selectat' => WC()->session->get('dp_id'), 'selected_name' => WC()->session->get('dp_name'), 'tip_selectat' => WC()->session->get('dp_tip'), 'tip_selectat_text' => $dp_type_text));
        }
        wp_die();
    }

    public function pp_add_delivery_point_id($order_id)
    {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = $chosen_methods[0];
        $chosen_method = explode('_', $chosen_method)[0];
        $dp_id = WC()->session->get('dp_id');

        if ($chosen_method == 'pachetomat' && $dp_id) {
            update_post_meta($order_id, 'id_pachetomat', sanitize_text_field(WC()->session->get('dp_id')));
        }
    }

    public function pp_action_before_woocommerce_pay()
    {
        WC()->session->__unset('chosen_shipping_methods');
        WC()->session->__unset('judet');
        WC()->session->__unset('oras');
        WC()->session->__unset('dp_id');
        WC()->session->__unset('dp_name');
        WC()->session->__unset('dp_tip');
    }

    public function pp_action_woocommerce_thankyou_order_received_text($order_id)
    {
        WC()->session->__unset('chosen_shipping_methods');
        WC()->session->__unset('judet');
        WC()->session->__unset('oras');
        WC()->session->__unset('dp_id');
        WC()->session->__unset('dp_name');
        WC()->session->__unset('dp_tip');
        $order = new \WC_Order($order_id);
        if ($order->get_meta('id_pachetomat') && $order->is_paid()) {
            $lo = new LO();
            $pachetomat = $lo->get_delivery_point_by_id($order->get_meta('id_pachetomat'));

            if ($pachetomat->dp_tip == 0) {
                $dp_type_text = __('Posta Romana', 'postapanduri');
            } elseif ($pachetomat->dp_tip == 1) {
                $dp_type_text = __('PostaPanduri Smartlocker', 'postapanduri');
            }

            $message = sprintf(__('After the order is shipped, you can pick up your parcel from <b>%s - %s</b>. Address: %s, State %s, City: %s', 'postapanduri'), $pachetomat->dp_id, $dp_type_text . ' - ' . $pachetomat->dp_denumire, $pachetomat->dp_adresa, $pachetomat->dp_judet, $pachetomat->dp_oras);
            $gmaps_api_key = get_option('postapanduri_setari_generale')['gmaps_api_key'];
            $maps_api = get_option('postapanduri_setari_generale')['maps_api'];
            if ((!$maps_api && $gmaps_api_key) || ($maps_api == 'gmaps' && $gmaps_api_key)) {
                $icon = '';
                $harta = '<img style="-webkit-user-select: none;width:100%;" src="https://maps.googleapis.com/maps/api/staticmap?center=' . $pachetomat->dp_gps_lat . ',' . $pachetomat->dp_gps_long . '&zoom=15&size=1800x1600&markers=icon:' . $icon . '%7C' . $pachetomat->dp_gps_lat . ',' . $pachetomat->dp_gps_long . '&key=' . $gmaps_api_key . '" />';
            } else {
                $harta = '';
            }

            echo '<div class="woocommerce-message"><div>' . $message . '</div><div><hr />' . $harta . '</div></div>';
        }

    }

    public function postapanduri_validate_order()
    {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $chosen_method = $chosen_methods[0];
        $chosen_method = explode('_', $chosen_method)[0];
        $dp_id = WC()->session->get('dp_id');

        if ($chosen_method == 'pachetomat' && !$dp_id) {
            $message = __('You selected the pickup delivery method, but you did not select a pickup point', 'postapanduri');
            $messageType = "error";
            if (!wc_has_notice($message, $messageType)) {
                wc_add_notice($message, $messageType);
            }
        }

        if ($chosen_method == 'pachetomat' && $dp_id && WC()->cart->shipping_total == -1) {
            $message = __('Unfortunately we could not estimate the delivery price for this pickup location, please select another one', 'postapanduri');
            $messageType = "error";
            if (!wc_has_notice($message, $messageType)) {
                wc_add_notice($message, $messageType);
            }
        }
    }

    public function pp_action_woocommerce_after_shipping_rate($method, $index)
    {
        $meta = $method->get_meta_data();
        $chosen_method = WC()->session->get('chosen_shipping_methods')[0];
        $chosen_method = explode('_', $chosen_method)[0];
        if ($method->method_id == 'postapanduri' && $meta['tip'] == 'pachetomat' && $chosen_method == 'pachetomat') {
            $this->rcp_get_template_part('plugin-postapanduri-change-dp', array());
        }
    }

    function pp_action_woocommerce_checkout_update_order_review()
    {
        WC()->cart->calculate_shipping();
        return;
    }

    public function pp_clear_wc_shipping_rates_cache()
    {
        $packages = WC()->cart->get_shipping_packages();
        foreach ($packages as $key => $value) {
            $shipping_session = "shipping_for_package_$key";
            unset(WC()->session->$shipping_session);
        }
    }

    public function postapanduri_tracking_awb($order_id)
    {
        global $wpdb;
        $order = new \WC_Order($order_id);
        $table_name = $wpdb->prefix . "lo_awb";
        $awb_db = $wpdb->get_row($wpdb->prepare("SELECT * from {$table_name} WHERE id_comanda = %d and deleted = 0 order by generat desc limit 1", $order->get_id()));

        if ($awb_db && !empty($awb_db->awb)) {
            echo '<a id="postapanduri-public-tracking" target="_blank" href="https://static.livrarionline.ro/?awb=' . $awb_db->awb . '"><img src="' . NS\PLUGIN_NAME_URL . 'assets/img/logo.png">' . sprintf(__('AWB Tracking (%s)', 'postapanduri'), $awb_db->awb) . '</a>';
        }
    }

    public function process_issn()
    {
        $lo = new LO();
        $lo->f_login = (int)WC_PostaPanduri::get_setari_generale('f_login');
        $lo->setRSAKey(WC_PostaPanduri::get_setari_generale('rsa_key'));

        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        switch ($user_agent) {
            case "mozilla/5.0 (livrarionline.ro locker push service aes)":
                $this->run_lockers_update_push();
                break;
            case "mozilla/5.0 (livrarionline.ro locker update service aes)":
                $this->run_lockers_update();
                break;
            default:
                if (empty($_POST['F_CRYPT_MESSAGE_ISSN'])) {
                    wp_die('F_CRYPT_MESSAGE_ISSN nu a fost trimis');
                }
                $this->run_issn(sanitize_text_field($_POST['F_CRYPT_MESSAGE_ISSN']));
                break;
        }
    }

    public function pp_product_display_smartlocker_info()
    {
        if (is_product()) {
            global $product;
            if ($product->is_virtual()) {
                // daca e produs virtual nu are livrare
                return false;
            }
            if (!$product->is_in_stock()) {
                // daca e produsul nu este in stoc nu afisez
                return false;
            }

            if (!empty(get_option('postapanduri_setari_pachetomat')[0]['activ_serviciu']) && get_option('postapanduri_setari_pachetomat')[0]['activ_serviciu'] == 1) {
                // daca e activa livrarea in pachetomate si am cookie postapanduri setat si am default_dpid si am info de afisat
                if (!empty($_COOKIE['postapanduri'])) {
                    $postapanduri = json_decode(stripcslashes($_COOKIE['postapanduri']));
                    if (!empty($postapanduri->info)) {
                        // am info de afisat
                        echo '<div class="postapanduri-smartlocker_info"><img alt="smartlocker_info" src="' . NS\PLUGIN_NAME_URL . 'assets/img/logo-postapanduri.png' . '"/>' . '<div class="text-container">' . (!empty($postapanduri->default_dpname) ? '<div><b>' . sanitize_text_field($postapanduri->default_dpname) . '</b></div>' : '') . sanitize_text_field($postapanduri->info) . '</div></div>';
                    }
                }
            }
        }
    }

    public function pp_add_javascript_for_cookie()
    {
        if (!empty(get_option('postapanduri_setari_pachetomat')[0]['activ_serviciu']) && get_option('postapanduri_setari_pachetomat')[0]['activ_serviciu'] == 1) {
            // daca e activ serviciul de pachetomate
            $current_user = wp_get_current_user();
            $lang = esc_attr(substr(get_bloginfo('language'), 0, 2)) ?: 'ro';

            if (empty($current_user)) {
                // adauga script fara email
                echo '<script async type="text/javascript" src="https://static.livrarionline.ro/getppid/0/' . $lang . '/pp.js"></script>';
            } else {
                // adauga script cu email
                $email_hash = hash('sha256', strtolower($current_user->user_email . 'pp'), 0);
                echo '<script async type="text/javascript" src="https://static.livrarionline.ro/getppid/' . $email_hash . '/' . $lang . '/pp.js"></script>';
            }
        }
    }

    private function run_issn($f_crypt_message_issn)
    {
        $lo = new LO();
        $lo->f_login = (int)WC_PostaPanduri::get_setari_generale('f_login');
        $lo->setRSAKey(WC_PostaPanduri::get_setari_generale('rsa_key'));

        $issn = $lo->decrypt_ISSN($f_crypt_message_issn); //obiect decodat din JSON in clasa LO
        if (empty($issn)) {
            wp_die('Nu am putut decripta mesajul!');
        }
        if (!isset($issn->f_order_number)) {
            wp_die('Parametrul f_order_number lipseste.');
        }
        if (!isset($issn->f_statusid)) {
            wp_die('Parametrul f_statusid lipseste.');
        }
        if (!isset($issn->f_stamp)) {
            wp_die('Parametrul f_stamp lipseste.');
        }
        if (!isset($issn->f_awb_collection)) {
            wp_die('Parametrul f_awb lipseste.');
        }

        $post_type = get_post_type($issn->f_order_number);

        if ($post_type != 'shop_order') {
            self::success_response_issn($issn->f_order_number);
        }

        $order = new \WC_Order($issn->f_order_number);

        $issn_order_statuses = get_option('postapanduri_setari_generale')['issn'];
        $issn_order_statuses = array_keys($issn_order_statuses);
        $matches = WC_PostaPanduri::multidimensional_search(WC_PostaPanduri::$pp_order_statuses, array('cod' => $issn->f_statusid));

        if (!empty($matches)) {
            $match = $matches[0];
            if (in_array($match, $issn_order_statuses)) {
                $order->update_status(ltrim($match, 'wc-'));
                self::success_response_issn($issn->f_order_number);
            }
        } else {
            self::success_response_issn($issn->f_order_number);
        }
    }

    private static function success_response_issn($order_number)
    {
        $raspuns_xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $raspuns_xml .= '<issn>';
        $raspuns_xml .= '<x_order_number>' . $order_number . '</x_order_number>';
        $raspuns_xml .= '<merchServerStamp>' . date("Y-m-dTH:m:s") . '</merchServerStamp>';
        $raspuns_xml .= '<f_response_code>1</f_response_code>';
        $raspuns_xml .= '</issn>';
        header('Content-type: text/xml');
        echo $raspuns_xml;
        die();
    }

    // SMARTLOCKER UPDATE
    public function run_lockers_update()
    {
        global $wpdb;
        $posted = file_get_contents('php://input');
        $lo = new LO();

        $lo->f_login = (int)WC_PostaPanduri::get_setari_generale('f_login');
        $lo->setRSAKey(WC_PostaPanduri::get_setari_generale('rsa_key'));
        $lockers_data = $lo->decrypt_ISSN($posted); //obiect decodat din JSON in clasa LO
        if (is_null($lockers_data)) {
            wp_die('Nu am putut decripta payload-ul');
        }
        $login_id = $lockers_data->merchid;
        $lo_delivery_points = $lockers_data->dulap;
        $lo_dp_program = $lockers_data->zile2dulap;
        $lo_dp_exceptii = $lockers_data->exceptii_zile;

        if (!empty($lo_delivery_points)) {
            foreach ($lo_delivery_points as $delivery_point) {
                $sql = "INSERT INTO {$wpdb->prefix}lo_delivery_points 
							(
								dp_id, 
								dp_denumire, 
								dp_adresa, 
								dp_judet, 
								dp_oras, 
								dp_tara, 
								dp_gps_lat, 
								dp_gps_long, 
								dp_tip, 
								dp_active, 
								version_id, 
								dp_temperatura, 
								dp_indicatii, 
								termosensibil
							) 
							VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %s, %d) 
							ON DUPLICATE KEY UPDATE 
								dp_denumire = %s,
								dp_adresa = %s,
								dp_judet = %s,
								dp_oras = %s,
								dp_tara = %s,
								dp_gps_lat = %s,
								dp_gps_long = %s,
								dp_tip = %d,
								dp_active = %d,
								version_id = %d,
								dp_temperatura = %d,
								dp_indicatii = %s,
								termosensibil = %d;
							";
                $sql = $wpdb->prepare($sql,
                    $delivery_point->dulapid, $delivery_point->denumire, $delivery_point->adresa, $delivery_point->judet, $delivery_point->oras, $delivery_point->tara, $delivery_point->latitudine, $delivery_point->longitudine, (int)$delivery_point->tip_dulap, (int)$delivery_point->active, (int)$delivery_point->versionid, (float)$delivery_point->dp_temperatura, $delivery_point->dp_indicatii, (int)$delivery_point->termosensibil,
                    $delivery_point->denumire, $delivery_point->adresa, $delivery_point->judet, $delivery_point->oras, $delivery_point->tara, $delivery_point->latitudine, $delivery_point->longitudine, (int)$delivery_point->tip_dulap, (int)$delivery_point->active, (int)$delivery_point->versionid, (float)$delivery_point->dp_temperatura, $delivery_point->dp_indicatii, (int)$delivery_point->termosensibil);
                $wpdb->get_row($sql);
            }
        }

        if (!empty($lo_dp_program)) {
            foreach ($lo_dp_program as $program) {
                $sql = "INSERT INTO {$wpdb->prefix}lo_dp_program 
							(
								dp_start_program,
								dp_end_program,
								dp_id,
								day_active,
								version_id,
								day_number,
								day
							) 
							VALUES (%s, %s, %d, %d, %d, %d, %s) 
							ON DUPLICATE KEY UPDATE 
								dp_start_program = %s,
								dp_end_program = %s,
								day_active = %d,
								version_id = %d,
								day = %s;
							";
                $sql = $wpdb->prepare($sql,
                    $program->start_program, $program->end_program, (int)$program->dulapid, (int)$program->active, (int)$program->versionid, (int)$program->day_number, $program->day_name,
                    $program->start_program, $program->end_program, (int)$program->active, (int)$program->versionid, $program->day_name
                );
                $wpdb->get_row($sql);
            }
        }

        if (!empty($lo_dp_exceptii)) {
            foreach ($lo_dp_exceptii as $exceptie) {
                $sql = "INSERT INTO {$wpdb->prefix}lo_dp_day_exceptions 
							(
								dp_start_program,
		                        dp_end_program,
		                        dp_id,
		                        active,
		                        version_id,
		                        exception_day
							) 
							VALUES (%s, %s, %d, %d, %d, %s) 
							ON DUPLICATE KEY UPDATE 
								dp_start_program = %s,
                                dp_end_program = %s,
                                active = %d,
                                version_id = %d;
							";
                $sql = $wpdb->prepare($sql,
                    $exceptie->start_program, $exceptie->end_program, (int)$exceptie->dulapid, (int)$exceptie->active, (int)$exceptie->versionid, $exceptie->ziua,
                    $exceptie->start_program, $exceptie->end_program, (int)$exceptie->active, (int)$exceptie->versionid
                );
                $wpdb->get_row($sql);
            }
        }

        $sql = "SELECT
                        COALESCE(MAX(dp.version_id), 0) AS max_dulap_id,
                        COALESCE(MAX(dpp.version_id), 0) AS max_zile2dp,
                        COALESCE(MAX(dpe.version_id), 0) AS max_exceptii_zile
                    FROM
                        {$wpdb->prefix}lo_delivery_points dp
                        LEFT join {$wpdb->prefix}lo_dp_program dpp ON dpp.dp_id = dp.dp_id
                        LEFT join {$wpdb->prefix}lo_dp_day_exceptions dpe ON dpe.dp_id = dp.dp_id";

        $row = $wpdb->get_row($sql);

        $response['merch_id'] = (int)$login_id;
        $response['max_dulap_id'] = (int)$row->max_dulap_id;
        $response['max_zile2dp'] = (int)$row->max_zile2dp;
        $response['max_exceptii_zile'] = (int)$row->max_exceptii_zile;

        echo json_encode($response);
        die();
    }

    // SMARTLOCKER UPDATE cu notificare si preluare doar diferente
    public function run_lockers_update_push()
    {
        global $wpdb;
        $posted = file_get_contents('php://input');
        $lo = new LO();
        $lo->f_login = (int)WC_PostaPanduri::get_setari_generale('f_login');
        $lo->setRSAKey(WC_PostaPanduri::get_setari_generale('rsa_key'));
        $lockers_data = $lo->decrypt_ISSN($posted); //obiect decodat din JSON in clasa LO
        if (is_null($lockers_data)) {
            wp_die('Nu am putut decripta payload-ul');
        }
        if (!empty($lockers_data->update) && !empty($lockers_data->f_stamp)) {
            // citesc data ultimului update din DB local
            $check_sql = "SELECT last_update FROM {$wpdb->prefix}lo_locker_push";
            $result = $wpdb->get_row($check_sql);
            $last_update = '2000-01-01 00:00:00';
            if (!empty($result)) {
                $last_update = $result->last_update;
            } else {
                $sql = "INSERT INTO {$wpdb->prefix}lo_locker_push VALUES ('" . $last_update . "')";
                $wpdb->get_row($sql);
            }

            $lockers_data = $lo->GetPachetomatePR(array('f_action' => 10, 'f_stamp' => $last_update));
            if ($lockers_data->status == 'error') {
                throw new \Exception($lockers_data->message);
            }

            $lo_delivery_points = $lockers_data->dulap;
            $lo_dp_program = $lockers_data->zile2dulap;
            $lo_dp_exceptii = $lockers_data->exceptii_zile;

            if (!empty($lo_delivery_points)) {
                foreach ($lo_delivery_points as $delivery_point) {
                    $sql = "INSERT INTO {$wpdb->prefix}lo_delivery_points 
							(
								dp_id,
								dp_denumire,
								dp_adresa,
								dp_judet,
								dp_oras,
								dp_tara,
								dp_gps_lat,
								dp_gps_long,
								dp_tip,
								dp_active,
								version_id,
								dp_temperatura,
								dp_indicatii,
								termosensibil,
							 	img_indicatii,
							 	img_pachetomat
							) 
							VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %s, %d, %s, %s) 
							ON DUPLICATE KEY UPDATE 
								dp_denumire = %s,
								dp_adresa = %s,
								dp_judet = %s,
								dp_oras = %s,
								dp_tara = %s,
								dp_gps_lat = %s,
								dp_gps_long = %s,
								dp_tip = %d,
								dp_active = %d,
								version_id = %d,
								dp_temperatura = %d,
								dp_indicatii = %s,
								termosensibil = %d,
							    img_indicatii = %s,
							    img_pachetomat = %s;
							";
                    $sql = $wpdb->prepare($sql,
                        $delivery_point->dulapid, $delivery_point->denumire, $delivery_point->adresa, $delivery_point->judet, $delivery_point->oras, $delivery_point->tara, $delivery_point->latitudine, $delivery_point->longitudine, (int)$delivery_point->tip_dulap, (int)$delivery_point->active, (int)$delivery_point->versionid, (float)$delivery_point->dp_temperatura, $delivery_point->dp_indicatii, (int)$delivery_point->termosensibil, $delivery_point->img_indicatii, $delivery_point->img_pachetomat,
                        $delivery_point->denumire, $delivery_point->adresa, $delivery_point->judet, $delivery_point->oras, $delivery_point->tara, $delivery_point->latitudine, $delivery_point->longitudine, (int)$delivery_point->tip_dulap, (int)$delivery_point->active, (int)$delivery_point->versionid, (float)$delivery_point->dp_temperatura, $delivery_point->dp_indicatii, (int)$delivery_point->termosensibil, $delivery_point->img_indicatii, $delivery_point->img_pachetomat);
                    $wpdb->get_row($sql);
                }
            }

            if (!empty($lo_dp_program)) {
                foreach ($lo_dp_program as $program) {
                    $sql = "INSERT INTO {$wpdb->prefix}lo_dp_program 
							(
								dp_start_program,
								dp_end_program,
								dp_id,
								day_active,
								version_id,
								day_number,
								day
							) 
							VALUES (%s, %s, %d, %d, %d, %d, %s) 
							ON DUPLICATE KEY UPDATE 
								dp_start_program = %s,
								dp_end_program = %s,
								day_active = %d,
								version_id = %d,
								day = %s;
							";
                    $sql = $wpdb->prepare($sql,
                        $program->start_program, $program->end_program, (int)$program->dulapid, (int)$program->active, (int)$program->versionid, (int)$program->day_number, $program->day_name,
                        $program->start_program, $program->end_program, (int)$program->active, (int)$program->versionid, $program->day_name
                    );
                    $wpdb->get_row($sql);
                }
            }

            if (!empty($lo_dp_exceptii)) {
                foreach ($lo_dp_exceptii as $exceptie) {
                    $sql = "INSERT INTO {$wpdb->prefix}lo_dp_day_exceptions 
							(
								dp_start_program,
		                        dp_end_program,
		                        dp_id,
		                        active,
		                        version_id,
		                        exception_day
							) 
							VALUES (%s, %s, %d, %d, %d, %s) 
							ON DUPLICATE KEY UPDATE 
								dp_start_program = %s,
                                dp_end_program = %s,
                                active = %d,
                                version_id = %d;
							";
                    $sql = $wpdb->prepare($sql,
                        $exceptie->start_program, $exceptie->end_program, (int)$exceptie->dulapid, (int)$exceptie->active, (int)$exceptie->versionid, $exceptie->ziua,
                        $exceptie->start_program, $exceptie->end_program, (int)$exceptie->active, (int)$exceptie->versionid
                    );
                    $wpdb->get_row($sql);
                }
            }

            // actualizez ultimul stamp de update
            $sql = "UPDATE {$wpdb->prefix}lo_locker_push SET last_update = now()";
            $wpdb->get_row($sql);
        }
    }

    // END SMARTLOCKER UPDATE cu notificare si preluare doar diferente

    public function pp_show_zero_price($label, $method)
    {
        if ($method->method_id == 'postapanduri') {
            $label = $method->get_label();
            if (\WC()->cart->get_tax_price_display_mode() == 'excl') {
                $label .= ': ' . \wc_price($method->cost);
                if (\WC()->cart->prices_include_tax) {
                    $label .= ' ' . \WC()->countries->ex_tax_or_vat() . '';
                }
            } else {
                $label .= ': ' . \wc_price($method->cost + $method->get_shipping_tax());
                if (!\WC()->cart->prices_include_tax) {
                    $label .= ' ' . \WC()->countries->inc_tax_or_vat() . '';
                }
            }
        }
        return $label;
    }
}
