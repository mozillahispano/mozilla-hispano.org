<?php
/**
 * Returns button html
 *
 * @param string $text
 * @param string $onclick
 * @param string $class
 * @return string
 */
function w3_button($text, $onclick = '', $class = '') {
    return sprintf('<input type="button" class="button %s" value="%s" onclick="%s" />', htmlspecialchars($class), htmlspecialchars($text), htmlspecialchars($onclick));
}

/**
 * Returns button link html
 *
 * @param string $text
 * @param string $url
 * @param boolean $new_window
 * @return string
 */
function w3_button_link($text, $url, $new_window = false) {
    $url = str_replace('&amp;', '&', $url);

    if ($new_window) {
        $onclick = sprintf('window.open(\'%s\');', addslashes($url));
    } else {
        $onclick = sprintf('document.location.href=\'%s\';', addslashes($url));
    }

    return w3_button($text, $onclick);
}

/**
 * Returns hide note button html
 *
 * @param string $text
 * @param string $note
 * @param string $redirect
 * @param boolean $admin if to use config admin
 * @param string $page
 * @return string
 */
function w3_button_hide_note($text, $note, $redirect = '', $admin = false, $page = '') {
    if ($page == '') {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $page = W3_Request::get_string('page', 'w3tc_dashboard');
    }

    $url = sprintf('admin.php?page=%s&w3tc_default_hide_note&note=%s', $page, $note);

    if ($admin)
        $url .= '&admin=1';

    if ($redirect != '') {
        $url .= '&redirect=' . urlencode($redirect);
    }

    $url = wp_nonce_url($url, 'w3tc');

    return w3_button_link($text, $url);
}

/**
 * Returns popup button html
 *
 * @param string $text
 * @param string $action
 * @param string $params
 * @param integer $width
 * @param integer $height
 * @return string
 */
function w3_button_popup($text, $action, $params = '', $width = 800, $height = 600) {
    $url = wp_nonce_url(sprintf('admin.php?page=w3tc_dashboard&w3tc_%s%s', $action, ($params != '' ? '&' . $params : '')), 'w3tc');
    $url = str_replace('&amp;', '&', $url);

    $onclick = sprintf('window.open(\'%s\', \'%s\', \'width=%d,height=%d,status=no,toolbar=no,menubar=no,scrollbars=yes\');', $url, $action, $width, $height);

    return w3_button($text, $onclick);
}

/**
 * Returns nonce field HTML
 *
 * @param string $action
 * @param string $name
 * @param bool $referer
 * @internal param bool $echo
 * @return string
 */
function w3_nonce_field($action = -1, $name = '_wpnonce', $referer = true) {
    $name = esc_attr($name);
    $return = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';

    if ($referer) {
        $return .= wp_referer_field(false);
    }

    return $return;
}

/**
 * @param string $body http response body
 */
function w3_in_plugin_update_message($body) {
    $matches = null;
    $regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote(W3TC_VERSION) . '\s*=|$)~Uis';

    if (preg_match($regexp, $body, $matches)) {
        $changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));

        echo '<div style="color: #f00;">' . __('Take a minute to update, here\'s why:', 'w3-total-cache') . '</div><div style="font-weight: normal;">';
        $ul = false;

        foreach ($changelog as $index => $line) {
            if (preg_match('~^\s*\*\s*~', $line)) {
                if (!$ul) {
                    echo '<ul style="list-style: disc; margin-left: 20px;">';
                    $ul = true;
                }
                $line = preg_replace('~^\s*\*\s*~', '', htmlspecialchars($line));
                echo '<li style="width: 50%; margin: 0; float: left; ' . ($index % 2 == 0 ? 'clear: left;' : '') . '">' . $line . '</li>';
            } else {
                if ($ul) {
                    echo '</ul><div style="clear: left;"></div>';
                    $ul = false;
                }
                echo '<p style="margin: 5px 0;">' . htmlspecialchars($line) . '</p>';
            }
        }

        if ($ul) {
            echo '</ul><div style="clear: left;"></div>';
        }

        echo '</div>';
    }
}