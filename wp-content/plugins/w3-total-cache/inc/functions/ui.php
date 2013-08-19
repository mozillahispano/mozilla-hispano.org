<?php
/**
 * Returns an notification box
 * @param $message
 * @return string
 */
function w3_get_notification_box($message) {
    return sprintf('<div class="updated fade">%s</div>', $message);
}

/**
 * Echos an notification box
 * @param $message
 */
function w3_e_notification_box($message) {
    echo w3_get_notification_box($message);
}

/**
 * Returns an error box
 * @param $message
 * @return string
 */
function w3_get_error_box($message) {
    return sprintf('<div class="error">%s</div>', $message);
}

/**
 * Echos an error box
 * @param $message
 */
function w3_e_error_box($message) {
    echo w3_get_error_box($message);
}

/**
 * Format bytes into B, KB, MB, GB and TB
 * @param $bytes
 * @param int $precision
 * @return string
 */
function w3_format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Echos an input text element
 *
 * @param string $id
 * @param string $name
 * @param string $value
 * @param bool $disabled
 */
function w3_ui_textbox($id, $name, $value, $disabled = false) {?>
    <input class="enabled" type="text" id="<?php echo esc_attr($id)?>" name="<?php echo esc_attr($name)?>" value="<?php echo esc_attr($value)?>" <?php disabled($disabled) ?>>
    <?php
}

/**
 * Echos an input text element
 * @param string $id
 * @param string $name
 * @param string $value
 * @param bool $disabled
 */
function w3_ui_textarea($id, $name, $value, $disabled = false) {?>
    <textarea class="enabled" id="<?php echo esc_attr($id)?>" name="<?php echo esc_attr($name)?>" rows="5" cols=25 <?php disabled($disabled) ?>><?php echo esc_textarea($value)?></textarea>
<?php
}

/**
 * Echos an input checkbox element
 * @param string $id
 * @param string $name
 * @param bool $state whether checked or not
 * @param bool $disabled
 */
function w3_ui_checkbox($id, $name, $state, $disabled = false) {?>
<input type="hidden" name="<?php echo esc_attr($name)?>" value="0">
<input class="enabled" type="checkbox" id="<?php echo esc_attr($id)?>" name="<?php echo esc_attr($name)?>" value="1" <?php checked($state)?> <?php disabled($disabled) ?>>
<?php
}

/**
 * Echos an element
 * @param string $type
 * @param string $id
 * @param string $name
 * @param mixed $value
 * @param bool $disabled
 */
function w3_ui_element($type, $id, $name, $value, $disabled = false) {
    switch ($type) {
        case 'textbox':
            w3_ui_textbox($id, $name, $value, $disabled);
            break;
        case 'textarea':
            w3_ui_textarea($id, $name, $value, $disabled);
            break;
        case 'checkbox':
        default:
            w3_ui_checkbox($id, $name, $value, $disabled);
            break;

    }
}

/**
 * @param string $path
 * @return string|void
 */
function w3_admin_url($path) {
    if (is_network_admin()) {
        return network_admin_url($path);
    }
    return admin_url($path);
}