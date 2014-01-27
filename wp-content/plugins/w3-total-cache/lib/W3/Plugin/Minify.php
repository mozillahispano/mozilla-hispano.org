<?php

/**
 * W3 Minify plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_Minify
 */
class W3_Plugin_Minify extends W3_Plugin {
    /**
     * Minify reject reason
     *
     * @var string
     */
    var $minify_reject_reason = '';

    /**
     * Error
     *
     * @var string
     */
    var $error = '';

    /**
     * Array of printed styles
     *
     * @var array
     */
    var $printed_styles = array();

    /**
     * Array of printed scripts
     *
     * @var array
     */
    var $printed_scripts = array();

    /**
     * Array of replaced styles
     *
     * @var array
     */
    var $replaced_styles = array();

    /**
     * Array of replaced scripts
     *
     * @var array
     */
    var $replaced_scripts = array();

    /**
     * Runs plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        if ($this->_config->get_string('minify.engine') == 'file') {
            add_action('w3_minify_cleanup', array(
                &$this,
                'cleanup'
            ));
        }

        /**
         * Start minify
         */
        if ($this->can_minify()) {
            w3tc_add_ob_callback('minify', array($this,'ob_callback'));
        }

        if (!is_admin()) {
            $dispatcher = w3_instance('W3_Dispatcher');
            if($dispatcher->send_minify_headers($this->_config))
                add_action('send_headers', array(
                    &$this,
                    'send_headers'
                ));
        }
    }

     /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        $a = w3_instance('W3_Plugin_MinifyAdmin');
        $a->cleanup();
    }

    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc = $this->_config->get_integer('minify.file.gc');

        return array_merge($schedules, array(
            'w3_minify_cleanup' => array(
                'interval' => $gc,
                'display' => sprintf('[W3TC] Minify file GC (every %d seconds)', $gc)
            )
        ));
    }

    /**
     * OB callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            if ($this->can_minify2($buffer)) {
                /**
                 * Replace script and style tags
                 */
                if (function_exists('is_feed') && !is_feed()) {
                    w3_require_once(W3TC_INC_DIR . '/functions/extract.php');
                    $head_prepend = '';
                    $body_prepend = '';
                    $body_append = '';
                    $embed_extsrcjs = false;

                    if ($this->_config->get_boolean('minify.auto')) {
                        if ($this->_config->get_boolean('minify.js.enable')) {
                            $ignore_js_files = $this->_config->get_array('minify.reject.files.js');
                            $embed_type = $this->_config->get_string('minify.js.header.embed_type');
                            $embed_type = $embed_type == 'extsrc' || $embed_type == 'asyncsrc'?'blocking':$embed_type;

                            $embed_pos = strpos($buffer, '<!-- W3TC-include-js-head -->');
                            $buffer = str_replace('<!-- W3TC-include-js-head -->', '', $buffer);
                            if ($embed_pos === false) {
                                preg_match('~<head(\s+[^<>]+)*>~Ui', $buffer, $match, PREG_OFFSET_CAPTURE);
                                $embed_pos = strlen($match[0][0]) + $match[0][1];
                            }

                            $ignore_js_files = array_map('w3_normalize_file', $ignore_js_files);
                            $files_to_minify = array();
                            $handled_scripts = array();
                            $script_tags = w3_extract_js2($buffer);
                            $previous_file_was_ignored = false;
                            foreach ($script_tags as $script_tag) {
                                $tag_pos = strpos($buffer, $script_tag);
                                $match = array();
                                preg_match('~<script\s+[^<>]*src=["\']?([^"\']+)["\']?[^<>]*>\s*</script>~is', $script_tag, $match);
                                $file = $match[1];
                                $file = w3_normalize_file_minify2($file);
                                $script_len = strlen($script_tag);
                                if (!$this->_filter_files($file) || in_array($file, $handled_scripts)) {
                                    continue;
                                }
                                $handled_scripts[] = $file;
                                $this->replaced_scripts[] = $file;
                                if (in_array($file, $ignore_js_files)) {
                                    if ($tag_pos > $embed_pos) {
                                        if ($files_to_minify) {
                                            $script = $this->get_script_custom($files_to_minify, $embed_type);
                                            $buffer = substr_replace($buffer, $script, $embed_pos, 0);
                                            $files_to_minify = array();
                                            $script_len = $script_len +strlen($script);
                                        }

                                        $embed_pos = $embed_pos + $script_len;
                                        $previous_file_was_ignored = true;
                                    }
                                } else {
                                    $buffer = substr_replace($buffer,'', $tag_pos, $script_len);
                                    if ($embed_pos > $tag_pos)
                                        $embed_pos -= $script_len;
                                    elseif ($previous_file_was_ignored)
                                        $embed_pos = $tag_pos;
                                    $files_to_minify[] = $file;
                                }
                            }
                            $script = $this->get_script_custom($files_to_minify, $embed_type);
                            $buffer = substr_replace($buffer, $script, $embed_pos, 0);
                        }

                        if ($this->_config->get_boolean('minify.css.enable')) {
                            $ignore_css_files = $this->_config->get_array('minify.reject.files.css');
                            $files_to_minify = array();

                            $embed_pos = strpos($buffer, '<!-- W3TC-include-css -->');


                            $buffer = str_replace('<!-- W3TC-include-css -->', '', $buffer);
                            if ($embed_pos === false) {
                                preg_match('~<head(\s+[^<>]+)*>~Ui', $buffer, $match, PREG_OFFSET_CAPTURE);
                                $embed_pos = strlen($match[0][0]) + $match[0][1];
                            }

                            $ignore_css_files = array_map('w3_normalize_file', $ignore_css_files);
                            $handled_styles = array();
                            $style_tags = w3_extract_css2($buffer);
                            $previous_file_was_ignored = false;
                            foreach ($style_tags as $style_tag_tuple) {
                                $style_tag = $style_tag_tuple[0];
                                $tag_pos = strpos($buffer, $style_tag);
                                $match = array();
                                $file = $style_tag_tuple[1];
                                $file = w3_normalize_file_minify2($file);
                                $style_len = strlen($style_tag);

                                if (!$this->_filter_files($file) || in_array($file, $handled_styles)) {
                                    continue;
                                }
                                $handled_styles[] = $file;
                                $this->replaced_styles[] = $file;
                                if (in_array($file, $ignore_css_files)) {
                                    if ($tag_pos > $embed_pos) {
                                        if ($files_to_minify) {
                                            $style = $this->get_style_custom($files_to_minify);
                                            $buffer = substr_replace($buffer, $style, $embed_pos, 0);
                                            $files_to_minify = array();
                                            $style_len = $style_len +strlen($style);
                                        }
                                        $embed_pos = $embed_pos + $style_len;
                                        $previous_file_was_ignored = true;
                                    }
                                } else {
                                    $buffer = substr_replace($buffer,'', $tag_pos, $style_len);
                                    if ($embed_pos > $tag_pos)
                                        $embed_pos -= $style_len;
                                    elseif ($previous_file_was_ignored)
                                        $embed_pos = $tag_pos;

                                    $files_to_minify[] = $file;
                                }
                            }
                            $style = $this->get_style_custom($files_to_minify);
                            $buffer = substr_replace($buffer, $style, $embed_pos, 0);
                        }
                    } else {
                        if ($this->_config->get_boolean('minify.css.enable') && !in_array('include', $this->printed_styles)) {
                            $style = $this->get_style_group('include');

                            if ($style) {
                                if ($this->_custom_location_does_not_exist('/<!-- W3TC-include-css -->/', $buffer, $style))
                                    $head_prepend .= $style;

                                $this->remove_styles_group($buffer, 'include');
                            }
                        }

                        if ($this->_config->get_boolean('minify.js.enable')) {

                            if (!in_array('include', $this->printed_scripts)) {
                                $embed_type = $this->_config->get_string('minify.js.header.embed_type');
                                $script = $this->get_script_group('include',$embed_type);

                                if ($script) {
                                    $embed_extsrcjs = $embed_type == 'extsrc' || $embed_type == 'asyncsrc'?true:$embed_extsrcjs;

                                    if ($this->_custom_location_does_not_exist('/<!-- W3TC-include-js-head -->/', $buffer, $script))
                                        $head_prepend .= $script;

                                     $this->remove_scripts_group($buffer, 'include', $embed_type);
                                }
                            }

                            if (!in_array('include-body', $this->printed_scripts)) {
                                $embed_type = $this->_config->get_string('minify.js.body.embed_type');
                                $script = $this->get_script_group('include-body',$embed_type);

                                if ($script) {
                                    $embed_extsrcjs = $embed_type == 'extsrc' || $embed_type == 'asyncsrc'?true:$embed_extsrcjs;

                                    if ($this->_custom_location_does_not_exist('/<!-- W3TC-include-js-body-start -->/', $buffer, $script))
                                        $body_prepend .= $script;

                                     $this->remove_scripts_group($buffer, 'include-body', $embed_type);
                                }
                            }

                            if (!in_array('include-footer', $this->printed_scripts)) {
                                $embed_type = $this->_config->get_string('minify.js.footer.embed_type');
                                $script = $this->get_script_group('include-footer',$embed_type);

                                if ($script) {
                                    $embed_extsrcjs = $embed_type == 'extsrc' || $embed_type == 'asyncsrc'?true:$embed_extsrcjs;

                                    if ($this->_custom_location_does_not_exist('/<!-- W3TC-include-js-body-end -->/', $buffer, $script))
                                        $body_append .= $script;

                                    $this->remove_scripts_group($buffer, 'include-footer', $embed_type);
                                }
                            }
                        }
                    }

                    if ($head_prepend != '') {
                        $buffer = preg_replace('~<head(\s+[^<>]+)*>~Ui', '\\0' . $head_prepend, $buffer, 1);
                    }

                    if ($body_prepend != '') {
                        $buffer = preg_replace('~<body(\s+[^<>]+)*>~Ui', '\\0' . $body_prepend, $buffer, 1);
                    }

                    if ($body_append != '') {
                        $buffer = preg_replace('~<\\/body>~', $body_append . '\\0', $buffer, 1);
                    }

                    if ($embed_extsrcjs) {
                        $script = "
<script type=\"text/javascript\">
" ."var extsrc=null;
".'(function(){function j(){if(b&&g){document.write=k;document.writeln=l;var f=document.createElement("span");f.innerHTML=b;g.appendChild(f);b=""}}function d(){j();for(var f=document.getElementsByTagName("script"),c=0;c<f.length;c++){var e=f[c],h=e.getAttribute("asyncsrc");if(h){e.setAttribute("asyncsrc","");var a=document.createElement("script");a.async=!0;a.src=h;document.getElementsByTagName("head")[0].appendChild(a)}if(h=e.getAttribute("extsrc")){e.setAttribute("extsrc","");g=document.createElement("span");e.parentNode.insertBefore(g,e);document.write=function(a){b+=a};document.writeln=function(a){b+=a;b+="\n"};a=document.createElement("script");a.async=!0;a.src=h;/msie/i.test(navigator.userAgent)&&!/opera/i.test(navigator.userAgent)?a.onreadystatechange=function(){("loaded"==this.readyState||"complete"==this.readyState)&&d()}:-1!=navigator.userAgent.indexOf("Firefox")||"onerror"in a?(a.onload=d,a.onerror=d):(a.onload=d,a.onreadystatechange=d);document.getElementsByTagName("head")[0].appendChild(a);return}}j();document.write=k;document.writeln=l;for(c=0;c<extsrc.complete.funcs.length;c++)extsrc.complete.funcs[c]()}function i(){arguments.callee.done||(arguments.callee.done=!0,d())}extsrc={complete:function(b){this.complete.funcs.push(b)}};extsrc.complete.funcs=[];var k=document.write,l=document.writeln,b="",g="";document.addEventListener&&document.addEventListener("DOMContentLoaded",i,!1);if(/WebKit/i.test(navigator.userAgent))var m=setInterval(function(){/loaded|complete/.test(document.readyState)&&(clearInterval(m),i())},10);window.onload=i})();' . "
</script>
";

                        $buffer = preg_replace('~<head(\s+[^<>]+)*>~Ui', '\\0' . $script, $buffer, 1);
                    }
                }

                /**
                 * Minify HTML/Feed
                 */
                if ($this->_config->get_boolean('minify.html.enable')) {
                    try {
                        $this->minify_html($buffer);
                    } catch (Exception $exception) {
                        $this->error = $exception->getMessage();
                    }
                }
            }

            if ($this->_config->get_boolean('minify.debug')) {
                $buffer .= "\r\n\r\n" . $this->get_debug_info();
            }
        }

        return $buffer;
    }

    /**
     * Checks to see if pattern exists in source if so replaces it with the provided script
     * and returns false. If pattern does not exists returns true.
     * @param $pattern
     * @param $source
     * @param $script
     * @return bool
     */
    function _custom_location_does_not_exist($pattern, &$source, $script){
        $count = 0;
        $source = preg_replace($pattern, $script, $source, 1, $count);
        return $count==0;
    }

    /**
     * Parse buffer and return array of JS files from it
     *
     * @param string $buffer
     * @return array
     */
    function get_files_js(&$buffer) {
        w3_require_once(W3TC_INC_DIR . '/functions/extract.php');

        $files = w3_extract_js($buffer);
        $files = $this->filter_files($files);

        return $files;
    }

    /**
     * Parse buffer and return array of CSS files from it
     *
     * @param string $buffer
     * @return array
     */
    function get_files_css(&$buffer) {
        w3_require_once(W3TC_INC_DIR . '/functions/extract.php');

        $files = w3_extract_css($buffer);
        $files = $this->filter_files($files);
        return $files;
    }

    /**
     * Filters files
     *
     * @param array $files
     * @return array
     */
    function filter_files($files) {
        $files = array_map('w3_normalize_file_minify2', $files);
        $files = array_filter($files, array(&$this, '_filter_files'));
        $files = array_values(array_unique($files));
        return $files;
    }

    /**
     * URL file filter
     *
     * @param string $file
     * @return bool
     */
    function _filter_files($file) {
        static $external;
        $ext = strrchr($file, '.');

        if ($ext != '.js' && $ext != '.css') {
            return false;
        }

        if (!isset($external))
            $external = $this->_config->get_array('minify.cache.files');
        foreach($external as $ext) {
            if(preg_match('#'.w3_get_url_regexp($ext).'#',$file))
                return true;
        }

        if (w3_is_url($file)) {
            return false;
        }

        $path = w3_get_document_root() . '/' . $file;

        if (!file_exists($path)) {
            return false;
        }

        return true;
    }

    /**
     * Removes style tags from the source
     *
     * @param string $content
     * @param array $files
     * @return void
     */
    function remove_styles(&$content, $files) {
        $regexps = array();
        $home_url_regexp = w3_get_home_url_regexp();

        $path = '';
        if (w3_is_network() && !w3_is_subdomain_install())
            $path = ltrim(w3_get_home_path(), '/');

        foreach ($files as $file) {
            if ($path && strpos($file, $path) === 0)
                $file = substr($file, strlen($path));

            $this->replaced_styles[] = $file;

            if (w3_is_url($file) && !preg_match('~' . $home_url_regexp . '~i', $file)) {
                // external CSS files
                $regexps[] = w3_preg_quote($file);
            } else {
                // local CSS files
                $file = ltrim($file, '/');
                if (ltrim(w3_get_site_path(),'/') && strpos($file, ltrim(w3_get_site_path(),'/')) === 0)
                    $file = str_replace(ltrim(w3_get_site_path(),'/'), '', $file);
                $file = ltrim(preg_replace('~' . $home_url_regexp . '~i', '', $file), '/\\');
                $regexps[] = '(' . $home_url_regexp . ')?/?' . w3_preg_quote($file);
            }
        }

        foreach ($regexps as $regexp) {
            $content = preg_replace('~<link\s+[^<>]*href=["\']?' . $regexp . '["\']?[^<>]*/?>(.*</link>)?~Uis', '', $content);
            $content = preg_replace('~@import\s+(url\s*)?\(?["\']?\s*' . $regexp . '\s*["\']?\)?[^;]*;?~is', '', $content);
        }

        $content = preg_replace('~<style[^<>]*>\s*</style>~', '', $content);
    }

    /**
     * Remove script tags from the source
     *
     * @param string $content
     * @param array $files
     * @return void
     */
    function remove_scripts(&$content, $files) {
        $regexps = array();
        $home_url_regexp = w3_get_home_url_regexp();

        $path = '';
        if (w3_is_network() && !w3_is_subdomain_install())
            $path = ltrim(w3_get_home_path(), '/');

        foreach ($files as $file) {
            if ($path && strpos($file, $path) === 0)
                $file = substr($file, strlen($path));

            $this->replaced_scripts[] = $file;

            if (w3_is_url($file) && !preg_match('~' . $home_url_regexp . '~i', $file)) {
                // external JS files
                $regexps[] = w3_preg_quote($file);
            } else {
                // local JS files
                $file = ltrim($file, '/');
                if (ltrim(w3_get_site_path(),'/') && strpos($file, ltrim(w3_get_site_path(),'/')) === 0)
                    $file = str_replace(ltrim(w3_get_site_path(),'/'), '', $file);
                $file = ltrim(preg_replace('~' . $home_url_regexp . '~i', '', $file), '/\\');
                $regexps[] = '(' . $home_url_regexp . ')?/?' . w3_preg_quote($file);
            }
        }

        foreach ($regexps as $regexp) {
            $content = preg_replace('~<script\s+[^<>]*src=["\']?' . $regexp . '["\']?[^<>]*>\s*</script>~Uis', '', $content);
        }
    }

    /**
     * Removes style tag from the source for group
     *
     * @param string $content
     * @param string $location
     * @return void
     */
    function remove_styles_group(&$content, $location) {
        $theme = $this->get_theme();
        $template = $this->get_template();

        $files = array();
        $groups = $this->_config->get_array('minify.css.groups');

        if (isset($groups[$theme]['default'][$location]['files'])) {
            $files = (array) $groups[$theme]['default'][$location]['files'];
        }

        if ($template != 'default' && isset($groups[$theme][$template][$location]['files'])) {
            $files = array_merge($files, (array) $groups[$theme][$template][$location]['files']);
        }

        $this->remove_styles($content, $files);
    }

    /**
     * Removes script tags from the source for group
     *
     * @param string $content
     * @param string $location
     * @return void
     */
    function remove_scripts_group(&$content, $location, $type) {
        $theme = $this->get_theme();
        $template = $this->get_template();
        $files = array();
        $groups = $this->_config->get_array('minify.js.groups');

        if (isset($groups[$theme]['default'][$location]['files'])) {
            $files = (array) $groups[$theme]['default'][$location]['files'];
        }

        if ($template != 'default' && isset($groups[$theme][$template][$location]['files'])) {
            $files = array_merge($files, (array) $groups[$theme][$template][$location]['files']);
        }

        $this->remove_scripts($content, $files);
    }

    /**
     * Minifies HTML
     *
     * @param string $html
     * @return string
     */
    function minify_html(&$html) {
        $w3_minifier = w3_instance('W3_Minifier');

        $ignored_comments = $this->_config->get_array('minify.html.comments.ignore');

        if (count($ignored_comments)) {
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/IgnoredCommentPreserver.php');

            $ignored_comments_preserver = new Minify_IgnoredCommentPreserver();
            $ignored_comments_preserver->setIgnoredComments($ignored_comments);

            $ignored_comments_preserver->search($html);
        }

        if ($this->_config->get_boolean('minify.html.inline.js')) {
            $js_engine = $this->_config->get_string('minify.js.engine');

            if (!$w3_minifier->exists($js_engine) || !$w3_minifier->available($js_engine)) {
                $js_engine = 'js';
            }

            $js_minifier = $w3_minifier->get_minifier($js_engine);
            $js_options = $w3_minifier->get_options($js_engine);

            $w3_minifier->init($js_engine);

            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Inline.php');
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Inline/JavaScript.php');

            $html = Minify_Inline_JavaScript::minify($html, $js_minifier, $js_options);
        }

        if ($this->_config->get_boolean('minify.html.inline.css')) {
            $css_engine = $this->_config->get_string('minify.css.engine');

            if (!$w3_minifier->exists($css_engine) || !$w3_minifier->available($css_engine)) {
                $css_engine = 'css';
            }

            $css_minifier = $w3_minifier->get_minifier($css_engine);
            $css_options = $w3_minifier->get_options($css_engine);

            $w3_minifier->init($css_engine);

            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Inline.php');
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Inline/CSS.php');

            $html = Minify_Inline_CSS::minify($html, $css_minifier, $css_options);
        }

        $engine = $this->_config->get_string('minify.html.engine');

        if (!$w3_minifier->exists($engine) || !$w3_minifier->available($engine)) {
            $engine = 'html';
        }

        if (function_exists('is_feed') && is_feed()) {
            $engine .= 'xml';
        }

        $minifier = $w3_minifier->get_minifier($engine);
        $options = $w3_minifier->get_options($engine);

        $w3_minifier->init($engine);

        $html = call_user_func($minifier, $html, $options);

        if (isset($ignored_comments_preserver)) {
            $ignored_comments_preserver->replace($html);
        }
    }

    /**
     * Returns current theme
     *
     * @return string
     */
    function get_theme() {
        static $theme = null;

        if ($theme === null) {
            $theme = w3_get_theme_key(get_theme_root(), get_template(), get_stylesheet());
        }

        return $theme;
    }

    /**
     * Returns current template
     *
     * @return string
     */
    function get_template() {
        static $template = null;

        if ($template === null) {
            switch (true) {
                case (is_404() && ($template_file = get_404_template())):
                case (is_search() && ($template_file = get_search_template())):
                case (is_tax() && ($template_file = get_taxonomy_template())):
                case (is_front_page() && function_exists('get_front_page_template') && $template_file = get_front_page_template()):
                case (is_home() && ($template_file = get_home_template())):
                case (is_attachment() && ($template_file = get_attachment_template())):
                case (is_single() && ($template_file = get_single_template())):
                case (is_page() && ($template_file = get_page_template())):
                case (is_category() && ($template_file = get_category_template())):
                case (is_tag() && ($template_file = get_tag_template())):
                case (is_author() && ($template_file = get_author_template())):
                case (is_date() && ($template_file = get_date_template())):
                case (is_archive() && ($template_file = get_archive_template())):
                case (is_comments_popup() && ($template_file = get_comments_popup_template())):
                case (is_paged() && ($template_file = get_paged_template())):
                    break;

                default:
                    if (function_exists('get_index_template')) {
                        $template_file = get_index_template();
                    } else {
                        $template_file = 'index.php';
                    }
                    break;
            }

            $template = basename($template_file, '.php');
        }

        return $template;
    }

    /**
     * Returns style tag
     *
     * @param string $url
     * @param boolean $import
     * @return string
     */
    function get_style($url, $import = false, $use_style = true) {
        if ($import && $use_style) {
            return "<style type=\"text/css\" media=\"all\">@import url(\"" . $url . "\");</style>\r\n";
        } elseif ($import && !$use_style) {
            return "@import url(\"" . $url . "\");\r\n";
        }else {
            return "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . str_replace('&', '&amp;', $url) . "\" media=\"all\" />\r\n";
        }
    }

    /**
     * Prints script tag
     *
     * @param string $url
     * @param string $embed_type
     * @return string
     */
    function get_script($url, $embed_type = 'blocking') {
        static $non_blocking_function = false;

        if ($embed_type == 'blocking') {
            $script = '<script type="text/javascript" src="' . str_replace('&', '&amp;', $url) . '"></script>';
        } else {
            $script = '';

            if($embed_type == 'nb-js'){
                if (!$non_blocking_function) {
                    $non_blocking_function = true;
                    $script = "<script type=\"text/javascript\">function w3tc_load_js(u){var d=document,p=d.getElementsByTagName('HEAD')[0],c=d.createElement('script');c.type='text/javascript';c.src=u;p.appendChild(c);}</script>";
                }

                $script .= "<script type=\"text/javascript\">w3tc_load_js('" . $url . "');</script>";

            } else if ($embed_type == 'nb-async') {
                $script = '<script async type="text/javascript" src="' . str_replace('&', '&amp;', $url) . '"></script>';
            } else if ($embed_type == 'nb-defer') {
                $script = '<script defer type="text/javascript" src="' . str_replace('&', '&amp;', $url) . '"></script>';
            } else if ($embed_type == 'extsrc') {
                $script = '<script type="text/javascript" extsrc="' . str_replace('&', '&amp;', $url) . '"></script>';
            } else if ($embed_type == 'asyncsrc') {
                $script = '<script type="text/javascript" asyncsrc="' . str_replace('&', '&amp;', $url) . '"></script>';
            }
        }
        return $script . "\r\n";
    }

    /**
     * Returns style tag for style group
     *
     * @param string $location
     * @return array
     */
    function get_style_group($location) {
        $style = false;
        $type = 'css';
        $groups = $this->_config->get_array('minify.css.groups');
        $theme = $this->get_theme();
        $template = $this->get_template();

        if ($template != 'default' && empty($groups[$theme][$template][$location]['files'])) {
            $template = 'default';
        }

        if (!empty($groups[$theme][$template][$location]['files'])) {
            $url = $this->format_url_group($theme, $template, $location, $type);

            if ($url) {
                $import = (isset($groups[$theme][$template][$location]['import']) ? (boolean) $groups[$theme][$template][$location]['import'] : false);

                $style = $this->get_style($url, $import);
            }
        }

        return $style;
    }

    /**
     * Returns script tag for script group
     *
     * @param string $location
     * @param string $embed_type
     * @return array
     */
    function get_script_group($location, $embed_type = 'blocking') {
        $script = false;
        $fileType = 'js';
        $theme = $this->get_theme();
        $template = $this->get_template();
        $groups = $this->_config->get_array('minify.js.groups');

        if ($template != 'default' && empty($groups[$theme][$template][$location]['files'])) {
            $template = 'default';
        }

        if (!empty($groups[$theme][$template][$location]['files'])) {
            $url = $this->format_url_group($theme, $template, $location, $fileType);

            if ($url) {
                $script = $this->get_script($url, $embed_type);
            }
        }

        return $script;
    }

    /**
     * Returns script tag for custom files
     *
     * @param string|array $files
     * @param string $embed_type
     * @return string
     */
    function get_script_custom($files, $embed_type = 'blocking') {
        $script = false;

        if (count($files)) {
            $urls = $this->get_minify_urls_for_files($files, 'js');
            $script = '';

            if ($urls) {
                foreach ($urls as $url) {
                    $script .= $this->get_script($url, $embed_type);
                }
            }
        }

        return $script;
    }

    /**
     * Returns style tag for custom files
     *
     * @param string|array $files
     * @param boolean $import
     * @return string
     */
    function get_style_custom($files, $import = false, $use_style = false) {
        $style = false;

        if (count($files)) {
            $urls = $this->get_minify_urls_for_files($files, 'css');
            $style = '';

            if ($urls) {
                foreach ($urls as $url) {
                    $style .= $this->get_style($url, $import, $use_style);
                }
            }
        }

        return $style;
    }

    /**
     * Formats URL
     *
     * @param string $theme
     * @param string $template
     * @param string $location
     * @param string $type
     * @param boolean $rewrite
     * @return string
     */
    function format_url_group($theme, $template, $location, $type, $rewrite = null) {
        /**
         * @var W3_Minify $w3_minify
         */
        $w3_minify = w3_instance('W3_Minify');

        $url = false;
        $id = $w3_minify->get_id_group($theme, $template, $location, $type);

        if ($id) {
            $minify_filename = $theme . '/' . $template . '.' . $location .
                '.'. $id . '.' . $type;
            $filename = w3_cache_blog_dir('minify') . '/' . $minify_filename;

            if ($this->_config->get_boolean('minify.rewrite')) {
                $url = w3_filename_to_url($filename);
            } else {
                $url = plugins_url('pub/minify.php?file=' . $minify_filename, W3TC_FILE);
            }
        }

        return $url;
    }

    /**
     * Formats custom URL
     *
     * @param array $files
     * @param string $type
     * @return array
     */
    function get_minify_urls_for_files($files, $type) {
        $w3_minify = w3_instance('W3_Minify');
        $url = array();
        $minify_filenames = $w3_minify->compress_minify_files($files, $type);
        foreach ($minify_filenames as $minify_filename) {
            $filename = w3_cache_blog_dir('minify') . '/' . $minify_filename;

            if ($this->_config->get_boolean('minify.rewrite')) {
                $url[] = w3_filename_to_url($filename);
            } else {
                $url[] = plugins_url('pub/minify.php?file=' . $minify_filename, W3TC_FILE);
            }
        }
        return $url;
    }

    /**
     * Returns array of minify URLs
     *
     * @return array
     */
    function get_urls() {
        $files = array();

        $js_groups = $this->_config->get_array('minify.js.groups');
        $css_groups = $this->_config->get_array('minify.css.groups');

        foreach ($js_groups as $js_theme => $js_templates) {
            foreach ($js_templates as $js_template => $js_locations) {
                foreach ((array) $js_locations as $js_location => $js_config) {
                    if (!empty($js_config['files'])) {
                        $files[] = $this->format_url_group($js_theme, $js_template, $js_location, 'js');
                    }
                }
            }
        }

        foreach ($css_groups as $css_theme => $css_templates) {
            foreach ($css_templates as $css_template => $css_locations) {
                foreach ((array) $css_locations as $css_location => $css_config) {
                    if (!empty($css_config['files'])) {
                        $files[] = $this->format_url_group($css_theme, $css_template, $css_location, 'css');
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Returns debug info
     *
     * @return string
     */
    function get_debug_info() {
        $debug_info = "<!-- W3 Total Cache: Minify debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('minify.engine')));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Theme: ', 20), $this->get_theme());
        $debug_info .= sprintf("%s%s\r\n", str_pad('Template: ', 20), $this->get_template());

        if ($this->minify_reject_reason) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $this->minify_reject_reason);
        }

        if ($this->error) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Errors: ', 20), $this->error);
        }

        if (count($this->replaced_styles)) {
            $debug_info .= "\r\nReplaced CSS files:\r\n";

            foreach ($this->replaced_styles as $index => $file) {
                $debug_info .= sprintf("%d. %s\r\n", $index + 1, w3_escape_comment($file));
            }
        }

        if (count($this->replaced_scripts)) {
            $debug_info .= "\r\nReplaced JavaScript files:\r\n";

            foreach ($this->replaced_scripts as $index => $file) {
                $debug_info .= sprintf("%d. %s\r\n", $index + 1, w3_escape_comment($file));
            }
        }

        $debug_info .= '-->';

        return $debug_info;
    }

    /**
     * Check if we can do minify logic
     *
     * @return boolean
     */
    function can_minify() {
        /**
         * Skip if doint AJAX
         */
        if (defined('DOING_AJAX')) {
            $this->minify_reject_reason = 'Doing AJAX';

            return false;
        }

        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            $this->minify_reject_reason = 'Doing cron';

            return false;
        }

        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            $this->minify_reject_reason = 'Application request';

            return false;
        }

        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            $this->minify_reject_reason = 'XMLRPC request';

            return false;
        }

        /**
         * Skip if Admin
         */
        if (defined('WP_ADMIN')) {
            $this->minify_reject_reason = 'wp-admin';

            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            $this->minify_reject_reason = 'Short init';

            return false;
        }

        /**
         * Check User agent
         */
        if (!$this->check_ua()) {
            $this->minify_reject_reason = 'User agent is rejected';

            return false;
        }

        /**
         * Check request URI
         */
        if (!$this->check_request_uri()) {
            $this->minify_reject_reason = 'Request URI is rejected';

            return false;
        }

        /**
         * Skip if user is logged in
         */
        if ($this->_config->get_boolean('minify.reject.logged') && !$this->check_logged_in()) {
            $this->minify_reject_reason = 'User is logged in';

            return false;
        }

        return true;
    }

    /**
     * Returns true if we can minify
     *
     * @param string $buffer
     * @return string
     */
    function can_minify2(&$buffer) {
        /**
         * Check for database error
         */
        if (w3_is_database_error($buffer)) {
            $this->minify_reject_reason = 'Database Error occurred';

            return false;
        }

        /**
         * Check for DONOTMINIFY constant
         */
        if (defined('DONOTMINIFY') && DONOTMINIFY) {
            $this->minify_reject_reason = 'DONOTMINIFY constant is defined';

            return false;
        }

        /**
         * Check feed minify
         */
        if ($this->_config->get_boolean('minify.html.reject.feed') && function_exists('is_feed') && is_feed()) {
            $this->minify_reject_reason = 'Feed is rejected';

            return false;
        }

        return true;
    }

    /**
     * Checks User Agent
     *
     * @return boolean
     */
    function check_ua() {
        $uas = array_merge($this->_config->get_array('minify.reject.ua'), array(
            W3TC_POWERED_BY
        ));

        foreach ($uas as $ua) {
            if (!empty($ua)) {
                if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    function check_logged_in() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if (strpos($cookie_name, 'wordpress_logged_in') === 0)
                return false;
        }

        return true;
    }

    /**
     * Checks request URI
     *
     * @return boolean
     */
    function check_request_uri() {
        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );

        foreach ($auto_reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }

        $reject_uri = $this->_config->get_array('minify.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $_SERVER['REQUEST_URI'])) {
                return false;
            }
        }

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        if (W3_Request::get_string('wp_customize'))
            return false;

        return true;
    }

    /**
     * Send headers
     */
    function send_headers() {
        @header('X-W3TC-Minify: On');
    }
}