<?php

namespace Postapanduri\Inc\Admin;

use PostaPanduri as NS;
use PostaPanduri\Inc\Core\WC_PostaPanduri;
use PostaPanduri\Inc\Libraries\LO;

class SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $message = null;
    private $type = null;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }


    public function importa_servicii()
    {
        $lo = new LO();
        $setari_generale = get_option('postapanduri_setari_generale');
        $lo->f_login = (int)$setari_generale['f_login'];
        $lo->setRSAKey($setari_generale['rsa_key']);
        return $lo->GetServicii()->f_servicii;

    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_menu_page(
            __('Posta Panduri', 'postapanduri'), __('Posta Panduri', 'postapanduri'), 'manage_woocommerce', 'postapanduri-general', array($this, 'postapanduri_toplevel_descriere'), NS\PLUGIN_NAME_URL . 'assets/img/pachetomat-icon.png'
        );

        add_submenu_page(
            'postapanduri-general', __('General settings', 'postapanduri'), __('General settings', 'postapanduri'), 'manage_woocommerce', 'postapanduri-setari-generale', array($this, 'postapanduri_sublevel_setari_generale')
        );
        add_submenu_page(
            'postapanduri-general', __('Pickup point settings', 'postapanduri'), __('Pickup point settings', 'postapanduri'), 'manage_woocommerce', 'postapanduri-setari-puncte-ridicare', array($this, 'postapanduri_sublevel_setari_puncte_ridicare')
        );
        add_submenu_page(
            'postapanduri-general', __('Delivery settings', 'postapanduri'), __('Delivery settings', 'postapanduri'), 'manage_woocommerce', 'postapanduri-setari-curierat', array($this, 'postapanduri_sublevel_setari_curierat')
        );
        add_submenu_page(
            'postapanduri-general', __('Click&Collect settings', 'postapanduri'), __('Click&Collect settings', 'postapanduri'), 'manage_woocommerce', 'postapanduri-setari-pachetomat', array($this, 'postapanduri_sublevel_setari_pachetomat')
        );
        add_submenu_page(
            'postapanduri-general', __('Click&Collect list', 'postapanduri'), __('Click&Collect list', 'postapanduri'), 'manage_woocommerce', 'postapanduri-setari-lista-pachetomate', array($this, 'postapanduri_sublevel_setari_lista_pachetomate')
        );
    }

    /**
     * Options page callback
     */
    public function postapanduri_toplevel_descriere()
    {
        echo '<div class="wrap">';
        echo '<h1>' . __('LivrariOnline.ro', 'postapanduri') . '</h1>';
        echo '<h2>' . __('Description', 'postapanduri') . '</h2>
                    <p><a href="https://livrarionline.ro" target="_blank"><b>' . __('LivrariOnline.ro', 'postapanduri') . '</b></a>' .
            __('is a software as a service (SAAS) system that incorporates the latest concepts and technologies for an efficient management of the modern courier activity. The web platform for managing courier services and reimbursement payments that connects online stores in Romania with the most competitive courier service providers enrolled in the system. Dedicated to online store owners who want to manage and monitor in an automated, efficient and fast way the orders and courier logistics operations as well as the cash receipts for the orders on the site.', 'postapanduri') . '
                    </p>
                    <h2>' . __('Benefits', 'postapanduri') . '</h2>
                    <ol>
                        <li>' . __('Automatic, efficient and fast management of deliveries for all orders on site;', 'postapanduri') . '</li>
                        <li>' . __('Super competitive prices from LivrariOnline partner couriers;', 'postapanduri') . '</li>
                        <li>' . __('Fast and free integration for the most popular e-commerce platforms;', 'postapanduri') . '</li>
                        <li>' . __('Click&Collect for SmartLockers and Postal Stations - alternative preset delivery point management service for online orders.', 'postapanduri') . '</li>
                    </ol>
                    <p>' . __('LivrariOnline.ro system has 3 components:', 'postapanduri') . '<p>
                    <ol>
                        <li>' . __('Door to door delivery by courier;', 'postapanduri') . '</li>
                        <li>' . __('Click&Collect Smartlocker - allows you to pick up online orders from the nearest Smartlocker;', 'postapanduri') . '</li>
                        <li>' . __('Click&Collect Counter - allows you to pick up online orders from the nearest pre-set pick-up point at the counter.', 'postapanduri') . '</li>
                    </ol>';
        echo '</div>';
    }

    public function postapanduri_sublevel_setari_generale()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'postapanduri'));
        }
        // Set class property
        $this->options = get_option('postapanduri_setari_generale');
        if (isset(get_option('postapanduri_setari_generale')['issn'])) {
            $this->options['issn'] = get_option('postapanduri_setari_generale')['issn'];
        }
        ?>
        <div class="wrap">
            <h1><?php echo __('General settings', 'postapanduri'); ?></h1>
            <?php settings_errors('postapanduri_setari_generale_error'); ?>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('postapanduri_setari_generale_group');
                do_settings_sections('postapanduri-setari-generale-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function postapanduri_sublevel_setari_puncte_ridicare()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'postapanduri'));
        }
        // Set class property
        // $this->options = get_option( 'postapanduri_setari_curierat' ); -- de reactivat

        ?>
        <div class="wrap">
            <h1>Setari Puncte de ridicare</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('postapanduri_setari_puncte_ridicare_group');
                do_settings_sections('postapanduri-setari-puncte_ridicare-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function postapanduri_sublevel_setari_curierat()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'postapanduri'));
        }
        // Set class property
        // $this->options = get_option( 'postapanduri_setari_curierat' ); -- de reactivat

        ?>
        <div class="wrap">
            <h1><?php echo __('Delivery settings', 'postapanduri'); ?></h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('postapanduri_setari_curierat_group');
                do_settings_sections('postapanduri-setari-curierat-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function postapanduri_sublevel_setari_pachetomat()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'postapanduri'));
        }
        // Set class property
        $this->options = get_option('postapanduri_setari_pachetomat');

        ?>
        <div class="wrap">
            <h1><?php echo __('Click&Collect settings', 'postapanduri'); ?></h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('postapanduri_setari_pachetomat_group');
                do_settings_sections('postapanduri-setari-pachetomat-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function postapanduri_sublevel_setari_lista_pachetomate()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'postapanduri'));
        }
        ?>
        <div class="wrap">
            <h1><?php __('Click&Collect delivery points list', 'postapanduri') ?></h1>
            <table class="wp-list-table widefat fixed striped comments">
                <thead>
                <tr>
                    <th width="10%"><b><?php echo __('Delivery point ID', 'postapanduri'); ?></b></th>
                    <th width="10%"><b><?php echo __('Type', 'postapanduri'); ?></b></th>
                    <th width="30%"><b><?php echo __('Name', 'postapanduri'); ?></b></th>
                    <th width="10%"><b><?php echo __('State', 'postapanduri'); ?></b></th>
                    <th width="10%"><b><?php echo __('City', 'postapanduri'); ?></b></th>
                    <th width="20%"><b><?php echo __('Schedule', 'postapanduri'); ?></b></th>
                    <th width="10%"><b><?php echo __('Temperature', 'postapanduri'); ?></b></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $lo = new LO();
                $pachetomate = $lo->get_all_delivery_points_location_admin();
                if (!empty($pachetomate)) {
                    foreach ($pachetomate as $pachetomat) {
                        switch ($pachetomat->dp_tip) {
                            case 1:
                                $img = '<img src="' . NS\PLUGIN_NAME_URL . 'assets/img/location-pin.png' . '"/>';
                                break;
                            case 0:
                                $img = '<img src="' . NS\PLUGIN_NAME_URL . 'assets/img/location-pin-posta.png' . '"/>';
                                break;
                        }
                        echo '<tr>';
                        echo '<td><b>' . $pachetomat->dp_id . '</b></td>';
                        echo '<td>' . $img . '</td>';
                        echo '<td><b>' . $pachetomat->dp_denumire . '</b></td>';
                        echo '<td>' . $pachetomat->dp_judet . '</td>';
                        echo '<td>' . $pachetomat->dp_oras . '</td>';
                        echo '<td>' . $pachetomat->orar . '</td>';
                        echo '<td ' . ($pachetomat->termosensibil ? 'style="color:red;font-weight:bold"' : '') . '>' . ($pachetomat->dp_temperatura ?: '-') . '&deg; C</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr>';
                    echo '<td colspan="7">' . __('For now there is no delivery point in your system. Please check that you copied the <b>ISSN URL</b> in the merchant account at <a href="https://comercianti.livrarionline.ro" target="_blank"><b>https://comercianti.livrarionline.ro</b></a>, <b>Settings</b> section, <b>Info API</b> menu to receive the delivery points. More information can be found in <b>General settings</b> menu of our plugin.', 'postapanduri') . '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }


    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'postapanduri_setari_generale_group', // Option group
            'postapanduri_setari_generale', // Option name
            array($this, 'sanitize_general_settings') // Sanitize
        );

        register_setting(
            'postapanduri_setari_puncte_ridicare_group', // Option group
            'postapanduri_setari_puncte_ridicare', // Option name
            array($this, 'sanitize_puncte_ridicare_settings') // Sanitize
        );

        register_setting(
            'postapanduri_setari_curierat_group', // Option group
            'postapanduri_setari_curierat', // Option name
            array($this, 'sanitize_curierat_settings') // Sanitize
        );

        register_setting(
            'postapanduri_setari_pachetomat_group', // Option group
            'postapanduri_setari_pachetomat', // Option name
            array($this, 'sanitize_pachetomat_settings') // Sanitize
        );

        // SETARI GENERALE
        add_settings_section('postapanduri_setari_generale_section_id', __('General settings', 'postapanduri'), array($this, 'print_general_section_info'), 'postapanduri-setari-generale-admin');
        add_settings_field('is_active', __('Activate PostaPanduri plugin', 'postapanduri'), array($this, 'is_active_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('f_login', __('Merchant Login ID (f_login)', 'postapanduri'), array($this, 'f_login_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('rsa_key', __('Merchant RSA Key (rsakey)', 'postapanduri'), array($this, 'rsa_key_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('plateste_ramburs', __('Pay cash on delivery to merchant', 'postapanduri'), array($this, 'plateste_ramburs_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('use_google_maps_api', __('Use Google Maps API', 'postapanduri'), array($this, 'use_google_maps_api_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('gmaps_api_key', __('Google Maps API key (used in the checkout process to show delivery points map)', 'postapanduri'), array($this, 'gmaps_api_key_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('use_mapbox_api', __('Use Mapbox API', 'postapanduri'), array($this, 'use_mapbox_api_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('mapbox_api_key', __('Mapbox API key (used in the checkout process to show delivery points map)', 'postapanduri'), array($this, 'mapbox_api_key_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_section('postapanduri_setari_generale_section_id2', __('ISSN - Instant Shipping Status Notification', 'postapanduri'), array($this, 'print_general_section_info2'), 'postapanduri-setari-generale-admin');
        add_settings_field('use_thermo', __('I deliver perishable products', 'postapanduri'), array($this, 'use_thermo_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');
        add_settings_field('free_shipping_calculation_method', __('Free shipping calculation method', 'postapanduri'), array($this, 'free_shipping_calculation_method_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id');

        foreach (WC_PostaPanduri::$pp_order_statuses as $key => $value) {
            add_settings_field($key, $value['cod'] . ' : ' . $value['denumire'], array($this, 'pp_order_statuses_callback'), 'postapanduri-setari-generale-admin', 'postapanduri_setari_generale_section_id2', array($key, $value));
        }

        // END SETARI GENERALE

        // SETARI PUNCTE DE RIDICARE
        add_settings_section('postapanduri_setari_puncte_ridicare_section_id', __('Setting up picking points', 'postapanduri'), array($this, 'print_puncte_ridicare_section_info'), 'postapanduri-setari-puncte_ridicare-admin');
        // END SETARI PUNCTE DE RIDICARE

        // SETARI CURIERAT
        add_settings_section('postapanduri_setari_curierat_section_id', __('Setting up courier services door to door PostaPanduri', 'postapanduri'), array($this, 'print_curierat_section_info'), 'postapanduri-setari-curierat-admin');
        // END SETARI CURIERAT

        // SETARI PACHETOMAT
        add_settings_section('postapanduri_setari_pachetomat_section_id', __('Setting up Click & Collect services', 'postapanduri'), array($this, 'print_pachetomat_section_info'), 'postapanduri-setari-pachetomat-admin');

        add_settings_field('show_pr_delivery_points', __('Show PostaRomana delivery points', 'postapanduri'), array($this, 'show_pr_delivery_points'), 'postapanduri-setari-pachetomat-admin', 'postapanduri_setari_pachetomat_section_id');

        // END SETARI PACHETOMAT
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize_general_settings($input)
    {
        $this->type = 'updated';
        $this->message = __('General settings have been successfully saved', 'postapanduri');
        $new_input = array();

        foreach ($input as $key => $value) {
            if ((!in_array($key, array('issn'))) && isset($value) && $value) {
                $new_input[$key] = sanitize_text_field($value);
            } elseif (!in_array($key, array('issn', 'maps_api', 'mapbox_api_key', 'gmaps_api_key'))) {
                $this->type = 'error';
                $this->message = sprintf(__('%s field is mandatory', 'postapanduri'), $key);

            }
        }

        foreach ($input['issn'] as $key => $value) {
            if (isset($value) && $value) {
                $new_input['issn'][$key] = sanitize_text_field($value);
            }
        }

        add_settings_error(
            'postapanduri_setari_generale_error',
            esc_attr('settings_updated'),
            $this->message,
            $this->type
        );
        return $new_input;
    }

    public function sanitize_puncte_ridicare_settings($input)
    {
        $this->type = 'updated';
        $this->message = __('Pickup points settings have been successfully saved', 'postapanduri');
        $new_input = array();

        foreach ($input as $k => $i) {
            if (!is_array($i)) {
                $new_input[$k] = sanitize_text_field($i);
            }
            foreach ($i as $key => $value) {

                if (isset($value) && $value) {
                    $new_input[$k][$key] = sanitize_text_field($value);
                } else {
                    $new_input[$k][$key] = false;
                }
            }
        }

        return $new_input;
    }

    public function sanitize_curierat_settings($input)
    {
        $this->type = 'updated';
        $this->message = __('Delivery settings have been successfully saved', 'postapanduri');
        $new_input = array();

        foreach ($input as $k => $i) {
            if (!is_array($i)) {
                $new_input[$k] = sanitize_text_field($i);
            }
            foreach ($i as $key => $value) {

                if (isset($value) && $value) {
                    $new_input[$k][$key] = sanitize_text_field($value);
                } else {
                    $new_input[$k][$key] = false;
                }
            }
        }

        return $new_input;
    }

    public function sanitize_pachetomat_settings($input)
    {
        $this->type = 'updated';
        $this->message = __('Click&Collect settings have been successfully saved', 'postapanduri');
        $new_input = array();

        foreach ($input as $k => $i) {
            if (!is_array($i)) {
                $new_input[$k] = sanitize_text_field($i);
            }
            foreach ($i as $key => $value) {

                if (isset($value) && $value) {
                    $new_input[$k][$key] = sanitize_text_field($value);
                } else {
                    $new_input[$k][$key] = false;
                }
            }
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_general_section_info()
    {
        echo __('Please obtain the settings from the <a href="https://comercianti.livrarionline.ro/comercianti/ListAPI" target="_blank"><b>merchant account</b></a>', 'postapanduri');
    }

    public function print_general_section_info2()
    {
        echo '<p>' . __('The ISSN service has the following functionalities:', 'postapanduri') . '</p>';
        echo '<ol>
                    <li>' . __('Notifies your store in real time about the change in delivery status and update the status of orders so that it reflects the current status of delivery', 'postapanduri') . '</li>
                    <li>' . __('Notifies your store in real time about the new SmartLockers added to the PostaPanduri system and update the status of the existing SmartLockers (schedule, temperature, etc.)', 'postapanduri') . '</li>
              </ol>';
        echo '<div style="background: #ffffff;padding:10px;border:1px solid #cccccc;margin:10px 0">
                    ' . sprintf(__('<b>INFO:</b> To set up ISSN URL please visit <a href="https://comercianti.livrarionline.ro" target="_blank"><b>https://comercianti.livrarionline.ro</b></a>, <b>Info API</b> section, <b>ISSN URL</b> field. Please fill <b>%s/wc-api/wc_postapanduri_issn</b> URL and choose <b>POST</b> for ISSN URL method. Check the statuses for which you want your website to be notified in the merchant account. Please check the same statuses below as in the merchant account.', 'postapanduri'), get_bloginfo('url')) . '
                </div>';

        echo '<h3>' . __('Set the delivery status for which you want the order status to be updated in your store.', 'postapanduri') . '</h3>';
    }

    public function print_puncte_ridicare_section_info()
    {
        echo '<div>' . __('Please define the pickup point(s)', 'postapanduri') . '</div>';
        echo '<div>' . __('The fields marked with <b>*</b> are mandatory. After adding a new pickup point you will be able to activate it and set it as default.', 'postapanduri') . '</div><hr />';
        echo '<div id="servicii">';
        $ppsc = get_option('postapanduri_setari_puncte_ridicare');
        $dpr = isset($ppsc['default_punct_de_ridicare']) ? $ppsc['default_punct_de_ridicare'] : null;
        $judete = WC()->countries->get_shipping_country_states();
        $judete = $judete['RO'];

        if (is_array($ppsc) && !empty($ppsc)) {
            $i = 0;
            foreach ($ppsc as $data) {
                if (!is_array($data)) {
                    continue;
                }
                $data = (object)$data;
                $toate_judetele_select = "<select name='postapanduri_setari_puncte_ridicare[" . $i . "][judet_punct_de_ridicare]'>";
                foreach ($judete as $key => $value) {
                    $toate_judetele_select .= "<option value='" . $key . "' " . ($data->judet_punct_de_ridicare == $key ? 'selected' : '') . ">" . $value . "</option>";
                }
                $toate_judetele_select .= "</select>";

                echo "<table class='form-table adauga_serviciu_table clone_table'>
                    <tr>
                        <th scope=\"row\">" . __('Activate this pickup point', 'postapanduri') . "</th>
                        <td><input type='checkbox' class='activ_serviciu' name='postapanduri_setari_puncte_ridicare[" . $i . "][activ_punct_ridicare]' value='1' " . checked(1, isset($data->activ_punct_ridicare) ? $data->activ_punct_ridicare : '', false) . " /></td>
                    </tr>
                		<tr>
                			<th scope=\"row\">" . __('Pickup point name', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][nume_punct_de_ridicare]' value='" . $data->nume_punct_de_ridicare . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Contact person last name', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][nume_persoana_de_contact]' value='" . $data->nume_persoana_de_contact . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Contact person first name', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][prenume_persoana_de_contact]' value='" . $data->prenume_persoana_de_contact . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Pickup point email', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][email_punct_de_ridicare]' value='" . $data->email_punct_de_ridicare . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Pickup point phone', 'postapanduri') . "</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][telefon_punct_de_ridicare]' value='" . $data->telefon_punct_de_ridicare . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Pickup point mobile phone', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][telefon_mobil_punct_de_ridicare]' value='" . $data->telefon_mobil_punct_de_ridicare . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Pickup address', 'postapanduri') . " *</th>
                			<td><textarea type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][adresa_punct_ridicare]'/>" . $data->adresa_punct_ridicare . "</textarea></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Pickup point state', 'postapanduri') . " *</th>
                			<td>" . $toate_judetele_select . "</td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Pickup point city', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][oras_punct_de_ridicare]' value='" . $data->oras_punct_de_ridicare . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Pickup point zipcode', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_puncte_ridicare[" . $i . "][cod_postal_punct_de_ridicare]' value='" . $data->cod_postal_punct_de_ridicare . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Default pickup point', 'postapanduri') . "</th>
                			<td><input type='radio' name='postapanduri_setari_puncte_ridicare[default_punct_de_ridicare]' value='" . $data->nume_punct_de_ridicare . "' " . (isset($dpr) && $dpr == $data->nume_punct_de_ridicare ? 'checked' : '') . " /></td>
                		</tr>
                		<tr>
                			<td colspan='2' style='text-align:left;'><span class='sterge_serviciu button-secondary'>" . __('Delete pickup point', 'postapanduri') . "</span></td>
                		</tr>
                		<tr>
                			<td colspan='2'><hr/></td>
                		</tr>
                	</table>";
                $i++;
            }
        } else {
            $toate_judetele_select = "<select name='postapanduri_setari_puncte_ridicare[0][judet_punct_de_ridicare]'>";
            foreach ($judete as $key => $value) {
                $toate_judetele_select .= "<option value='" . $key . "'>" . $value . "</option>";
            }
            $toate_judetele_select .= "</select>";
            echo "<table class='form-table adauga_serviciu_table clone_table'>
                    <tr>
                        <th scope=\"row\">" . __('Activate this pickup point', 'postapanduri') . "</th>
                        <td><input type='checkbox' class='activ_serviciu' name='postapanduri_setari_puncte_ridicare[0][activ_punct_ridicare]' value='1' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point name', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][nume_punct_de_ridicare]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Contact person last name', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][nume_persoana_de_contact]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Contact person first name', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][prenume_persoana_de_contact]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point email', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][email_punct_de_ridicare]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point phone', 'postapanduri') . "</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][telefon_punct_de_ridicare]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point mobile phone', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][telefon_mobil_punct_de_ridicare]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup address', 'postapanduri') . " *</th>
                        <td><textarea type='text' name='postapanduri_setari_puncte_ridicare[0][adresa_punct_ridicare]'/></textarea></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point state', 'postapanduri') . " *</th>
                        <td>" . $toate_judetele_select . "</td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point city', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][oras_punct_de_ridicare]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Pickup point zipcode', 'postapanduri') . " *</th>
                        <td><input type='text' name='postapanduri_setari_puncte_ridicare[0][cod_postal_punct_de_ridicare]' value='' /></td>
                    </tr>
                    <tr>
                        <th scope=\"row\">" . __('Default pickup point', 'postapanduri') . "</th>
                        <td><input type='radio' name='postapanduri_setari_puncte_ridicare[default_punct_de_ridicare]' value='' checked/></td>
                    </tr>

                    <tr>
                        <td colspan='2' style='text-align:left;'><span class='sterge_serviciu button-secondary'>" . __('Delete pickup point', 'postapanduri') . "</span></td>
                    </tr>
                    <tr>
                        <td colspan='2'><hr/></td>
                    </tr>
                </table>";
        }
        echo '</div>';
        echo "<span class='add_serviciu button-primary'>" . __('Add pickup point', 'postapanduri') . "</span>";
    }

    public function print_curierat_section_info()
    {
        echo '<div>' . __('Please obtain available shipping services from your <a href="https://comercianti.livrarionline.ro/comercianti/ListAPI" target="_blank"><b>merchant account</b></a>', 'postapanduri') . '</div>';
        echo '<div>' . __('The fields marked with <b>*</b> are mandatory. After adding a new shipping service you will be able to activate it.', 'postapanduri') . '</div><hr />';
        echo '<div id="servicii">';
        $ppsc = get_option('postapanduri_setari_curierat');

        if (is_array($ppsc) && !empty($ppsc)) {
            $i = 0;
            foreach ($ppsc as $data) {
                $data = (object)$data;

                echo "<table class='form-table adauga_serviciu_table clone_table'>
                        <tr>
                            <th scope=\"row\">" . __('Activate this shipping service', 'postapanduri') . "</th>
                            <td><input type='checkbox' class='activ_serviciu' name='postapanduri_setari_curierat[" . $i . "][activ_serviciu]' value='1' " . checked(1, isset($data->activ_serviciu) ? $data->activ_serviciu : '', false) . " /></td>
                        </tr>
                		<tr>
                			<th scope=\"row\">" . __('Service name', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_curierat[" . $i . "][nume_serviciu]' value='" . $data->nume_serviciu . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Service ID', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_curierat[" . $i . "][id_serviciu]' value='" . $data->id_serviciu . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Shipping company ID', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_curierat[" . $i . "][id_shipping_company]' value='" . $data->id_shipping_company . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Fixed price (shipping price estimation will not pe performed)', 'postapanduri') . "</th>
                			<td><input type='text' name='postapanduri_setari_curierat[" . $i . "][pret_fix]' value='" . $data->pret_fix . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Change shipping price (+ or -, value or percent relative to estimated shipping price)', 'postapanduri') . "</th>
                			<td>
                                <select name='postapanduri_setari_curierat[" . $i . "][semn_reducere]'>
                                    <option value='P' " . (isset($data->semn_reducere) && $data->semn_reducere == 'P' ? 'selected' : '') . ">+</option>
                                    <option value='M' " . (isset($data->semn_reducere) && $data->semn_reducere == 'M' ? 'selected' : '') . ">-</option>
                                </select>
                                <input type='text' name='postapanduri_setari_curierat[" . $i . "][reducere]' value='" . $data->reducere . "' />
                                <select name='postapanduri_setari_curierat[" . $i . "][tip_reducere]'>
                                    <option value='V' " . (isset($data->tip_reducere) && $data->tip_reducere == 'V' ? 'selected' : '') . ">" . __('Value (RON)', 'postapanduri') . "</option>
                                    <option value='P' " . (isset($data->tip_reducere) && $data->tip_reducere == 'P' ? 'selected' : '') . ">" . __('Percent (%)', 'postapanduri') . "</option>
                                </select>
                            </td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Free shipping for cart over', 'postapanduri') . "</th>
                			<td><input type='text' name='postapanduri_setari_curierat[" . $i . "][gratuit_peste]' value='" . $data->gratuit_peste . "' /> RON</td>
                		</tr>
                		<tr>
                			<td colspan='2' style='text-align:left;'><span class='sterge_serviciu button-secondary'>" . __('Delete shipping service', 'postapanduri') . "</span></td>
                		</tr>
                		<tr>
                			<td colspan='2'><hr/></td>
                		</tr>
                	</table>";
                $i++;
            }
        } else {
            echo "<table class='form-table adauga_serviciu_table clone_table'>
                    <tr>
                        <th scope=\"row\">" . __('Activate this shipping service', 'postapanduri') . "</th>
                        <td><input type='checkbox' class='activ_serviciu' name='postapanduri_setari_curierat[0][activ_serviciu]' value='1' /></td>
                    </tr>
            		<tr>
            			<th scope=\"row\">" . __('Service name', 'postapanduri') . " *</th>
            			<td><input type='text' name='postapanduri_setari_curierat[0][nume_serviciu]' value='' /></td>
            		</tr>
            		<tr>
            			<th scope=\"row\">" . __('Service ID', 'postapanduri') . " *</th>
            			<td><input type='text' name='postapanduri_setari_curierat[0][id_serviciu]' value='' /></td>
            		</tr>
            		<tr>
            			<th scope=\"row\">" . __('Shipping company ID', 'postapanduri') . " *</th>
            			<td><input type='text' name='postapanduri_setari_curierat[0][id_shipping_company]' value='' /></td>
            		</tr>
            		<tr>
            			<th scope=\"row\">" . __('Fixed price (shipping price estimation will not pe performed)', 'postapanduri') . "</th>
            			<td><input type='text' name='postapanduri_setari_curierat[0][pret_fix]' value='' /></td>
            		</tr>
                    <tr>
            			<th scope=\"row\">" . __('Change shipping price (+ or -, value or percent relative to estimated shipping price)', 'postapanduri') . "</th>
            			<td>
                            <select name='postapanduri_setari_curierat[0][semn_reducere]'>
                                <option value='P'>+</option>
                                <option value='M'>-</option>
                            </select>
                            <input type='text' name='postapanduri_setari_curierat[0][reducere]' value='' />
                            <select name='postapanduri_setari_curierat[0][tip_reducere]'>
                                <option value='V'>" . __('Value (RON)', 'postapanduri') . "</option>
                                <option value='P'>" . __('Percent (%)', 'postapanduri') . "</option>
                            </select>
                        </td>
            		</tr>
                    <tr>
            			<th scope=\"row\">" . __('Free shipping for cart over', 'postapanduri') . "</th>
            			<td><input type='text' name='postapanduri_setari_curierat[0][gratuit_peste]' value='' /> RON</td>
            		</tr>
            		<tr>
            			<td colspan='2' style='text-align:left;'><span class='sterge_serviciu button-secondary'>" . __('Delete shipping service', 'postapanduri') . "</span></td>
            		</tr>
            		<tr>
            			<td colspan='2'><hr/></td>
            		</tr>
            	</table>";
        }
        echo '</div>';
        echo "<span class='add_serviciu button-primary'>" . __('Add shipping service', 'postapanduri') . "</span>";
    }

    public function print_pachetomat_section_info()
    {
        echo '<div>' . __('Please obtain available shipping services from your <a href="https://comercianti.livrarionline.ro/comercianti/ListAPI" target="_blank"><b>merchant account</b></a>', 'postapanduri') . '</div>';
        echo '<div style="background: #ffffff;padding:10px;border:1px solid #dc3232;margin:10px 0;">' . __('Click&Collect shipping service involves upfront order payment. For this reason, when the customer selects Click&Collect shipping service in the checkout process, <b>the Cash on delivery payment method will be deactivated</b>.', 'postapanduri') . '</div>';
        echo '<div>' . __('The fields marked with <b>*</b> are mandatory. After adding a new shipping service you will be able to activate it.', 'postapanduri') . '</div><hr />';
        echo '<div id="servicii">';
        $ppsc = get_option('postapanduri_setari_pachetomat');

        if (is_array($ppsc) && !empty($ppsc)) {
            $i = 0;
            foreach ($ppsc as $index => $data) {
                if (!is_numeric($index)) {
                    continue;
                }
                $data = (object)$data;
                echo "<table class='form-table adauga_serviciu_table clone_table'>
                        <tr>
                            <th scope=\"row\">" . __('Activate this shipping service', 'postapanduri') . "</th>
                            <td><input type='checkbox' class='activ_serviciu' name='postapanduri_setari_pachetomat[" . $i . "][activ_serviciu]' value='1' " . checked(1, isset($data->activ_serviciu) ? $data->activ_serviciu : '', false) . " /></td>
                        </tr>
                		<tr>
                			<th scope=\"row\">" . __('Service name', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_pachetomat[" . $i . "][nume_serviciu]' value='" . $data->nume_serviciu . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Service ID', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_pachetomat[" . $i . "][id_serviciu]' value='" . $data->id_serviciu . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Shipping company ID', 'postapanduri') . " *</th>
                			<td><input type='text' name='postapanduri_setari_pachetomat[" . $i . "][id_shipping_company]' value='" . $data->id_shipping_company . "' /></td>
                		</tr>
                		<tr>
                			<th scope=\"row\">" . __('Fixed price (shipping price estimation will not pe performed)', 'postapanduri') . "</th>
                			<td><input type='text' name='postapanduri_setari_pachetomat[" . $i . "][pret_fix]' value='" . $data->pret_fix . "' /></td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Change shipping price (+ or -, value or percent relative to estimated shipping price)', 'postapanduri') . "</th>
                			<td>
                                <select name='postapanduri_setari_pachetomat[" . $i . "][semn_reducere]'>
                                    <option value='P' " . (isset($data->semn_reducere) && $data->semn_reducere == 'P' ? 'selected' : '') . ">+</option>
                                    <option value='M' " . (isset($data->semn_reducere) && $data->semn_reducere == 'M' ? 'selected' : '') . ">-</option>
                                </select>
                                <input type='text' name='postapanduri_setari_pachetomat[" . $i . "][reducere]' value='" . $data->reducere . "' />
                                <select name='postapanduri_setari_pachetomat[" . $i . "][tip_reducere]'>
                                    <option value='V' " . (isset($data->tip_reducere) && $data->tip_reducere == 'V' ? 'selected' : '') . ">" . __('Value (RON)', 'postapanduri') . "</option>
                                    <option value='P' " . (isset($data->tip_reducere) && $data->tip_reducere == 'P' ? 'selected' : '') . ">" . __('Percent (%)', 'postapanduri') . "</option>
                                </select>
                            </td>
                		</tr>
                        <tr>
                			<th scope=\"row\">" . __('Free shipping for cart over', 'postapanduri') . "</th>
                			<td><input type='text' name='postapanduri_setari_pachetomat[" . $i . "][gratuit_peste]' value='" . $data->gratuit_peste . "' /> RON</td>
                		</tr>
                		<tr>
                			<td colspan='2' style='text-align:left;'><span class='sterge_serviciu button-secondary'>" . __('Delete shipping service', 'postapanduri') . "</span></td>
                		</tr>
                		<tr>
                			<td colspan='2'><hr/></td>
                		</tr>
                	</table>";
                $i++;
            }
        } else {
            echo "<table class='form-table adauga_serviciu_table clone_table'>
                    <tr>
                        <th scope=\"row\">" . __('Activate this shipping service', 'postapanduri') . "</th>
                        <td><input type='checkbox' class='activ_serviciu' name='postapanduri_setari_pachetomat[0][activ_serviciu]' value='1' /></td>
                    </tr>
            		<tr>
            			<th scope=\"row\">" . __('Service name', 'postapanduri') . " *</th>
            			<td><input type='text' name='postapanduri_setari_pachetomat[0][nume_serviciu]' value='' /></td>
            		</tr>
            		<tr>
            			<th scope=\"row\">" . __('Service ID', 'postapanduri') . " *</th>
            			<td><input type='text' name='postapanduri_setari_pachetomat[0][id_serviciu]' value='' /></td>
            		</tr>
            		<tr>
            			<th scope=\"row\">" . __('Shipping company ID', 'postapanduri') . " *</th>
            			<td><input type='text' name='postapanduri_setari_pachetomat[0][id_shipping_company]' value='' /></td>
            		</tr>
            		<tr>
            			<th scope=\"row\">" . __('Fixed price (shipping price estimation will not pe performed)', 'postapanduri') . "</th>
            			<td><input type='text' name='postapanduri_setari_pachetomat[0][pret_fix]' value='' /></td>
            		</tr>
                    <tr>
            			<th scope=\"row\">" . __('Change shipping price (+ or -, value or percent relative to estimated shipping price)', 'postapanduri') . "</th>
            			<td>
                            <select name='postapanduri_setari_pachetomat[0][semn_reducere]'>
                                <option value='P'>+</option>
                                <option value='M'>-</option>
                            </select>
                            <input type='text' name='postapanduri_setari_pachetomat[0][reducere]' value='' />
                            <select name='postapanduri_setari_pachetomat[0][tip_reducere]'>
                                <option value='V'>" . __('Value (RON)', 'postapanduri') . "</option>
                                <option value='P'>" . __('Percent (%)', 'postapanduri') . "</option>
                            </select>
                        </td>
            		</tr>
                    <tr>
            			<th scope=\"row\">" . __('Free shipping for cart over', 'postapanduri') . "</th>
            			<td><input type='text' name='postapanduri_setari_pachetomat[0][gratuit_peste]' value='' /> RON</td>
            		</tr>
            		<tr>
            			<td colspan='2' style='text-align:left;'><span class='sterge_serviciu button-secondary'>" . __('Delete shipping service', 'postapanduri') . "</span></td>
            		</tr>
            		<tr>
            			<td colspan='2'><hr/></td>
            		</tr>
            	</table>";
        }
        echo '</div>';
        echo "<span class='add_serviciu button-primary'>" . __('Add shipping service', 'postapanduri') . "</span>";
    }

    /**
     * Get the settings option array and print one of its values
     */

    public function is_active_callback()
    {
        printf('<input name="postapanduri_setari_generale[is_active]" id="is_active" type="checkbox" value="1" %s />',
            isset($this->options['is_active']) ? checked(1, esc_attr($this->options['is_active']), false) : ''
        );
    }

    public function use_google_maps_api_callback()
    {
        printf('<input name="postapanduri_setari_generale[maps_api]" id="gmaps_api" type="radio" value="gmaps" %s />',
            isset($this->options['maps_api']) ? checked('gmaps', esc_attr($this->options['maps_api']), false) : 'checked'
        );
    }

    public function use_mapbox_api_callback()
    {
        printf('<input name="postapanduri_setari_generale[maps_api]" id="mapbox_api" type="radio" value="mapbox" %s />',
            isset($this->options['maps_api']) ? checked('mapbox', esc_attr($this->options['maps_api']), false) : ''
        );
    }

    public function show_pr_delivery_points()
    {
        printf('<input name="postapanduri_setari_pachetomat[show_pr_delivery_points]" id="show_pr_delivery_points" type="checkbox" value="1" %s />',
            isset($this->options['show_pr_delivery_points']) ? checked(1, esc_attr($this->options['show_pr_delivery_points']), false) : ''
        );
    }

    public function use_thermo_callback()
    {
        printf('<input name="postapanduri_setari_generale[use_thermo]" id="use_thermo" type="checkbox" value="1" %s />',
            isset($this->options['use_thermo']) ? checked(1, esc_attr($this->options['use_thermo']), false) : ''
        );
        echo '<p class="description">' . __('By checking this option, the customer will not be able to select in checkout a SmartLocker whose cells have a temperature higher than 30&deg; C. The temperature of the SmartLocker is constantly monitored, and when the temperature inside the cells drops below 30&deg; C, they will be automatically reactivated', 'postapanduri') . '</p>';
    }

    public function pp_order_statuses_callback($args)
    {
        printf('<input name="postapanduri_setari_generale[issn][' . $args[0] . ']" id="' . $args[0] . '" type="checkbox" value="1" %s />',
            isset($this->options['issn'][$args[0]]) ? checked(1, esc_attr($this->options['issn'][$args[0]]), false) : ''
        );
    }

    public function f_login_callback()
    {
        printf(
            '<input type="text" id="f_login" name="postapanduri_setari_generale[f_login]" value="%s" size="47"/>',
            isset($this->options['f_login']) ? esc_attr($this->options['f_login']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function rsa_key_callback()
    {
        printf(
            '<textarea type="textarea" id="rsa_key" name="postapanduri_setari_generale[rsa_key]" rows="8" cols="50"/>%s</textarea>',
            isset($this->options['rsa_key']) ? esc_attr($this->options['rsa_key']) : ''
        );
    }

    public function plateste_ramburs_callback()
    {
        $items = array(__('Cash', 'postapanduri') => 1, __('Bank', 'postapanduri') => 2);
        //$this->options = get_option( 'postapanduri_setari_generale' );
        echo "<select id='plateste_ramburs' name='postapanduri_setari_generale[plateste_ramburs]'>";
        foreach ($items as $key => $value) {
            $selected = ($this->options['plateste_ramburs'] == $value) ? 'selected="selected"' : '';
            echo "<option value='$value' $selected>$key</option>";
        }
        echo "</select>";
    }

    public function free_shipping_calculation_method_callback()
    {
        $items = array(__('Order subtotal (default)', 'postapanduri') => 'subtotal', __('Order subtotal including coupons', 'postapanduri') => 'subtotal_with_coupons');
        echo "<select id='free_shipping_calculation_method' name='postapanduri_setari_generale[free_shipping_calculation_method]'>";
        foreach ($items as $key => $value) {
            $selected = ($this->options['free_shipping_calculation_method'] == $value) ? 'selected="selected"' : '';
            echo "<option value='$value' $selected>$key</option>";
        }
        echo "</select>";
    }

    public function gmaps_api_key_callback()
    {
        printf(
            '<input type="text" id="gmaps_api_key" name="postapanduri_setari_generale[gmaps_api_key]" value="%s" size="47"/>',
            isset($this->options['gmaps_api_key']) ? esc_attr($this->options['gmaps_api_key']) : ''
        );
        echo '<p class="description">' . __('To obtain a Google Maps API key you need to visit <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"><b>this address</b></a> and click on <b>GET A KEY</b> button.<br />
        Then, please enable <b>Google Static Maps API</b> <a target="_blank" href="https://console.developers.google.com/apis/api/static_maps_backend"><b>here.</b>', 'postapanduri') . '</a></p>';
    }

    public function mapbox_api_key_callback()
    {
        printf(
            '<input type="text" id="mapbox_api_key" name="postapanduri_setari_generale[mapbox_api_key]" value="%s" size="47"/>',
            isset($this->options['mapbox_api_key']) ? esc_attr($this->options['mapbox_api_key']) : ''
        );
        echo '<p class="description">' . __('To obtain a Mapbox token you need to create an account <a target="_blank" href="https://account.mapbox.com/auth/signup/"><b>here</b></a> and then click on <b>Create a token</b> button.', 'postapanduri') . '</a></p>';
    }
}
