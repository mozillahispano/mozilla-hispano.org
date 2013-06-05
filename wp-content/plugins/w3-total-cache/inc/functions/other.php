<?php
/**
 * Parses FAQ XML file into array
 *
 * @return array
 */
function w3_parse_faq() {
    $faq = array();
    $file = W3TC_LANGUAGES_DIR . '/faq-' . get_locale() . '.xml';
    if (!file_exists($file)) {
        $file = W3TC_LANGUAGES_DIR . '/faq-en_US.xml';
    }
    $xml_premium = null;
    if ((defined('W3TC_ENTERPRISE') && W3TC_ENTERPRISE) || (defined('W3TC_PRO') && W3TC_PRO)) {
        $file_premium = W3TC_LANGUAGES_DIR . '/faq-premium-en_US.xml';
        $xml_premium = @file_get_contents($file_premium);
    }

    $xml = @file_get_contents($file);

    if ($xml) {
        if (function_exists('xml_parser_create')) {
            $parser = @xml_parser_create('UTF-8');

            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            $values = null;

            $result = xml_parse_into_struct($parser, $xml, $values);
            xml_parser_free($parser);

            if ($result) {
                $index = 0;
                $current_section = '';
                $current_entry = array();

                foreach ($values as $value) {
                    switch ($value['type']) {
                        case 'open':
                            if ($value['tag'] === 'section') {
                                $current_section = $value['attributes']['name'];
                            }
                            break;

                        case 'complete':
                            switch ($value['tag']) {
                                case 'question':
                                    $current_entry['question'] = $value['value'];
                                    break;

                                case 'answer':
                                    $current_entry['answer'] = $value['value'];
                                    break;
                            }
                            break;

                        case 'close':
                            if ($value['tag'] == 'entry') {
                                $current_entry['index'] = ++$index;
                                $faq[$current_section][] = $current_entry;
                            }
                            break;
                    }
                }

                if ($xml_premium) {
                    $parser = @xml_parser_create('UTF-8');

                    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
                    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
                    $values = null;

                    $result = xml_parse_into_struct($parser, $xml_premium, $values);
                    xml_parser_free($parser);

                    if ($result) {
                        $current_section = '';
                        $current_entry = array();

                        foreach ($values as $value) {
                            switch ($value['type']) {
                                case 'open':
                                    if ($value['tag'] === 'section') {
                                        $current_section = $value['attributes']['name'];
                                    }
                                    break;

                                case 'complete':
                                    switch ($value['tag']) {
                                        case 'question':
                                            $current_entry['question'] = $value['value'];
                                            break;

                                        case 'answer':
                                            $current_entry['answer'] = $value['value'];
                                            break;
                                    }
                                    break;

                                case 'close':
                                    if ($value['tag'] == 'entry') {
                                        $current_entry['index'] = ++$index;
                                        $faq[$current_section][] = $current_entry;
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }

    return $faq;
}

/**
 * Returns button link html
 *
 * @param string $text
 * @param string $url
 * @param boolean $new_window
 * @return string
 */
function w3tc_button_link($text, $url, $new_window = false) {
    $url = str_replace('&amp;', '&', $url);

    if ($new_window) {
        $onclick = sprintf('window.open(\'%s\');', addslashes($url));
    } else {
        $onclick = sprintf('document.location.href=\'%s\';', addslashes($url));
    }

    return w3tc_button($text, $onclick);
}

/**
 * Returns hide note button html
 *
 * @param string $text
 * @param string $note
 * @param string $redirect
 * @param boolean $admin if to use config admin
 * @return string
 */
function w3tc_button_hide_note($text, $note, $redirect = '', $admin = false, $page ='') {
    if (!$page) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $page = W3_Request::get_string('page');
    }

    $url = sprintf('admin.php?page=%s&w3tc_hide_note&note=%s', $page, $note);

    if ($admin)
        $url .= '&admin=1';

    if ($redirect != '') {
        $url .= '&redirect=' . urlencode($redirect);
    }

    $url = wp_nonce_url($url, 'w3tc');

    return w3tc_button_link($text, $url);
}

/**
 * Returns button html
 *
 * @param string $text
 * @param string $onclick
 * @param string $class
 * @return string
 */
function w3tc_button($text, $onclick = '', $class = '') {
    return sprintf('<input type="button" class="button %s" value="%s" onclick="%s" />', htmlspecialchars($class), htmlspecialchars($text), htmlspecialchars($onclick));
}