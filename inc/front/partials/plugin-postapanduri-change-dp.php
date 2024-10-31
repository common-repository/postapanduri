<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://postapanduri.ro
 * @since      1.0.0
 *
 * @package    PostaPanduri
 * @subpackage PostaPanduri/public/partials
 */

use PostaPanduri as NS;

?>

<div id="pp-selected-dp">
    <div id="pp-selected-dp-text"></div>
    <button type="button"
            id="pp-selected-dp-map"><?php echo __('Change pickup point', 'postapanduri'); ?></button>
</div>

<div style="display: none">
    <script type="text/javascript">
        last_dp_id = "<?php echo WC()->session->get('dp_id') ?: ''?>";
        last_dp_name = "<?php echo WC()->session->get('dp_name') ?: ''?>";
        last_dp_type = "<?php echo WC()->session->get('dp_tip') ?: ''?>";
        icon = "<?php echo NS\PLUGIN_NAME_URL . 'assets/img/location-pin.png';?>";
        icon_posta = "<?php echo NS\PLUGIN_NAME_URL . 'assets/img/location-pin-posta.png';?>";
        if (last_dp_type == 0) {
            dp_type_text = "<?php echo __('Posta Romana', 'postapanduri'); ?>";
        } else if (last_dp_type == 1) {
            dp_type_text = "<?php echo __('PostaPanduri Smartlocker', 'postapanduri'); ?>";
        }
    </script>
</div>
