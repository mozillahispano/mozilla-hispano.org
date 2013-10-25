<?php

/* 
 * User Role Editor On Screen Help class
 * 
 */

class URE_Screen_Help {
    
    protected function get_settings_overview_tab_help() {
    
        $text = '<h2>User Role Editor Options page help</h2>
            <p>
            <ul>
            <li><strong>' . esc_html__('Show Administrator role at User Role Editor', 'ure').'</strong> - ' .
                esc_html__('turn this option on in order to make the "Administrator" role available at the User Role Editor '
                        . 'roles selection drop-down list. It is hidden by default for security reasons.','ure') . '</li>
            <li><strong>' . esc_html__('Show capabilities in the human readable form','ure').'</strong> - ' .
                esc_html__('automatically converts capability names from the technical form for internal use like '
                        . '"edit_others_posts" to more user friendly form, e.g. "Edit others posts".','ure') . '</li>
            <li><strong>' . esc_html__('Show deprecated capabilities','ure').'</strong> - '.
                esc_html__('Capabilities like "level_0", "level_1" are deprecated and are not used by WordPress. '
                        . 'They are left at the user roles for the compatibility purpose with the old themes and plugins code. '
                        . 'Turning on this option will show those deprecated capabilities.', 'ure') . '</li>';
        if (is_multisite()) {
            $text .='
                <li><strong>' . esc_html__('Allow create, edit and delete users to not super-admininstrators', 'ure').'</strong> - ' .
                esc_html__('Super administrator only may create, edit and delete users under WordPress multi-site. ' 
                        . 'Turn this option on in order to remove this limitation.','ure') . '</li>';
        }
        $text = apply_filters('ure_get_settings_overview_tab_help', $text);
        $text .='
            </ul>
                </p>';
        
        return $text;
    }
    // end of get_settings_overview_tab_help()
    
    
    public function get_settings_help($tab_name) {
        switch ($tab_name) {
            case 'overview':{
                $text = $this->get_settings_overview_tab_help();
                break;
            }
            default: 
        }
        
        return $text;
    }
    // end of get_settings_help()

    
}
// end of URE_Screen_Help
