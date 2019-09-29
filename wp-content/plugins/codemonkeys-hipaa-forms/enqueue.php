<?php
/**
 * Created by Code Monkeys LLC
 * http://www.codemonkeysllc.com
 * User: Spencer
 * Date: 11/29/2017
 * Time: 12:36 PM
 */

//* ENQUEUE ADMIN SCRIPTS AND STYLES
function cm_hipaa_enqueue_admin_scripts() {
    // ENQUEUE GOOGLE FONTS
    $gFonts = array(
        "Roboto+Condensed:300,300i,400,400i,700,700i",
        "Roboto:100,100i,200,200i,300,300i,400,400i,500,500i,700,700i,900,900i"
    );

    $gf_args = array(
        'family' => urlencode(implode("|",$gFonts)),
        'subset' => 'latin,latin-ext'
    );
    wp_enqueue_style('google_fonts', add_query_arg($gf_args, "//fonts.googleapis.com/css"), array(), null);

    // ENQUEUE GOOGLE MATERIAL.IO ICONS
    wp_enqueue_style( 'materialIcons', 'https://fonts.googleapis.com/icon?family=Material+Icons' );

    // ENQUEUE MAIN PLUGIN ADMIN CSS
    wp_enqueue_style( 'cmHipaaAdminStyle', plugin_dir_url(__FILE__) . '/css/admin-style.css' );

    // ENQUEUE SCRIPT
    wp_enqueue_script( 'jquery-form' );
    wp_enqueue_script( 'cmHipaaAdminBuggyFill', plugin_dir_url(__FILE__) . '/js/viewport-units-buggyfill.js', array('jquery'), '2.2.5', true );
    wp_enqueue_script( 'cmHipaaAdminBuggyFillHack', plugin_dir_url(__FILE__) . '/js/viewport-units-buggyfill.hacks.js', array('jquery'), '2.2.5', true );
    wp_enqueue_script( 'cmHipaaAdminScript', plugin_dir_url(__FILE__) . '/js/admin-script.js', array('jquery'), '2.2.5', true );
    wp_enqueue_script('cm-hipaa-signature', plugin_dir_url(__FILE__) . 'js/jSignature/jSignature.min.noconflict.js', array('jquery'), '2.2.5', true);
    wp_enqueue_script('cm-hipaa-jquery-print', plugin_dir_url(__FILE__) . 'js/printThis.js', array('jquery'), '2.2.5', true);

    // PASS PHP DATA TO SCRIPT FILE
    global $post;
    wp_localize_script('cmHipaaAdminScript', 'hipaaScript', array(
        'pluginUrl' => plugin_dir_url(__FILE__),
        'siteUrl' =>  get_site_url(),
        'nonce' => wp_create_nonce('cm-hipaa-forms-nonce')
    ));

    // LOCALIZE CUSTOM JS FILE TO USE WITH AJAX
    wp_localize_script( 'cmHipaaAdminScript', 'ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}
add_action( 'admin_enqueue_scripts', 'cm_hipaa_enqueue_admin_scripts' );

//* ENQUEUE FRONT END AJAX JAVASCRIPT
function enqueue_cm_hipaa_scripts() {
    // ENQUEUE GOOGLE MATERIAL.IO ICONS
    wp_enqueue_style( 'materialIcons', 'https://fonts.googleapis.com/icon?family=Material+Icons' );

    // ENQUEUE MAIN PLUGIN CSS
    wp_enqueue_style( 'cmHipaaAdminStyle', plugin_dir_url(__FILE__) . '/css/style.css' );

    // ENQUEUE CUSTOM JS
    wp_enqueue_script( 'cmHipaaBuggyFill', plugin_dir_url(__FILE__) . '/js/viewport-units-buggyfill.js', array('jquery'), '2.2.5', true );
    wp_enqueue_script( 'cmHipaaBuggyFillHack', plugin_dir_url(__FILE__) . '/js/viewport-units-buggyfill.hacks.js', array('jquery'), '2.2.5', true );
    wp_enqueue_script('cm-hipaa-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), '2.2.5', true);
    wp_enqueue_script('cm-hipaa-signature', plugin_dir_url(__FILE__) . 'js/jSignature/jSignature.min.noconflict.js', array('jquery'), '2.2.5', true);

    // CHECK IF HOMEPAGE
    if (is_front_page()) {
        $frontpage = 1;
    } else {
        $frontpage = 0;
    }

    // CHECK IF USING SSL
    if (is_ssl()) {
        $ssl = 1;
    } else {
        $ssl = 0;
    }

    // GET ENABLED FORMS
    $calderaEnabledForms = json_encode(explode(',', esc_attr(get_option('caldera_enabled_form_ids'))));
    $gravityEnabledForms = json_encode(explode(',', esc_attr(get_option('gravity_enabled_form_ids'))));
    $enabledFormsSettings = get_option('enabled_forms_settings'); // NEW JSON VERSION WITH FORM SETTINGS

    $decodedSettings = json_decode($enabledFormsSettings);
    if(is_array($decodedSettings)) {
        foreach($decodedSettings as $decodedSetting) {
            if($decodedSetting->form_builder == 'gravity' && $decodedSetting->enabled == 'yes') {
                // REMOVE GRAVITY SUBMIT BUTTON FOR ENABLED FORMS
                add_filter( 'gform_submit_button_' . $decodedSetting->id, '__return_false' );

                wp_enqueue_style(
                    'custom-gravity-style',
                    plugin_dir_url(__FILE__) . '/css/style.css'
                );
                $custom_css = '
                        #gform_' . $decodedSetting->id . ' .gform_fileupload_multifile {
                            display: none;
                        }
                    ';
                wp_add_inline_style( 'custom-gravity-style', $custom_css );
            }
        }
    }

    // GET ADDONS
    $hipaaForms = new cmHipaaForms;
    $validated = json_decode($hipaaForms->validateAccount());
    $add_ons = '';
    $fileUpload = '';
    if(isset($validated->add_ons)) {
        $add_ons = explode(',', $validated->add_ons);
    }
    if(is_array($add_ons)) {
        if(in_array('fileupload', $add_ons)) {
            $fileUpload = 'yes';
        }
    }

    // PASS PHP DATA TO SCRIPT FILE (use example: cmHipaaScript.siteUrl)
    wp_localize_script('cm-hipaa-script', 'cmHipaaScript', array(
        'pluginUrl' => plugin_dir_url(__FILE__),
        'siteUrl' => get_site_url(),
        'frontPage' => $frontpage,
        'formBuilder' => esc_attr(get_option('form_builder')),
        'calderaEnabledForms' => $calderaEnabledForms,
        'gravityEnabledForms' => $gravityEnabledForms,
        'enabledFormsSettings' => $enabledFormsSettings,
        'privacyNoticeMethod' => esc_attr(get_option('privacy_notice_method')),
        'privacyNoticeLabel' => esc_attr(get_option('privacy_notice_label')),
        'privacyNoticeCopy' => esc_attr(get_option('privacy_notice_copy')),
        'privacyNoticeLink' => esc_attr(get_option('privacy_notice_link')),
        'ssl' => $ssl,
        'fileUpload' => $fileUpload,
        'nonce' => wp_create_nonce('cm-hipaa-forms-nonce')
    ));

    // LOCALIZE CUSTOM JS FILE TO USE WITH AJAX
    wp_localize_script('cm-hipaa-script', 'ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_cm_hipaa_scripts');

/*
function cm_hipaa_forms_gravity_styles() {
    // GET ENABLED FORMS
    $calderaEnabledForms = json_encode(explode(',', esc_attr(get_option('caldera_enabled_form_ids'))));
    $gravityEnabledForms = json_encode(explode(',', esc_attr(get_option('gravity_enabled_form_ids'))));
    $enabledFormsSettings = get_option('enabled_forms_settings'); // NEW JSON VERSION WITH FORM SETTINGS

    $decodedSettings = json_decode($enabledFormsSettings);
    if(is_array($decodedSettings)) {
        foreach($decodedSettings as $decodedSetting) {
            if($decodedSetting->form_builder == 'gravity') {
                wp_enqueue_style(
                    'custom-gravity-style',
                    plugin_dir_url(__FILE__) . '/css/style.css'
                );
                $custom_css = '
                        ' . $decodedSetting->id . ' .gform_button_select_files {
                            display: none;
                        }
                    ';
                wp_add_inline_style( 'custom-gravity-style', $custom_css );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'cm_hipaa_forms_gravity_styles' );
*/