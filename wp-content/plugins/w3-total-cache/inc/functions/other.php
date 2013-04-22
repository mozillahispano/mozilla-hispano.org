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