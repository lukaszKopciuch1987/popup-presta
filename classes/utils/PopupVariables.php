<?php


class PopupVariables
{
    public static function setDefaultValues()
    {
        Configuration::updateValue('CUSTOMPOPUP_ENABLED', 0);
        Configuration::updateValue('CUSTOMPOPUP_START_DATE', '');
        Configuration::updateValue('CUSTOMPOPUP_END_DATE', '');
        Configuration::updateValue('CUSTOMPOPUP_PRODUCTS', '');
        Configuration::updateValue('CUSTOMPOPUP_CATEGORIES', '');
        Configuration::updateValue('CUSTOMPOPUP_COLOR', '#fff');
        Configuration::updateValue('CUSTOMPOPUP_BACK_COLOR', '#222');
        Configuration::updateValue('CUSTOMPOPUP_PADDING', '25');
        Configuration::updateValue('CUSTOMPOPUP_TOP_PADDING', '40');
        Configuration::updateValue('CUSTOMPOPUP_BUTTON_COLOR', '#000');
        Configuration::updateValue('CUSTOMPOPUP_BUTTON_HOVER_COLOR', '#111');
        Configuration::updateValue('CUSTOMPOPUP_BUTTON_SIZE', '26');
        Configuration::updateValue('CUSTOMPOPUP_BUTTON_TOP_PADDING', '15');
        Configuration::updateValue('CUSTOMPOPUP_BUTTON_POSITION', 'right');
        Configuration::updateValue('CUSTOMPOPUP_MAINSELECT', 1);
        Configuration::updateValue('CUSTOMPOPUP_BUTTON1_NEW_TAB', 0);
        Configuration::updateValue('CUSTOMPOPUP_BUTTON2_NEW_TAB', 0);
    }

    public static function getTemplateVars()
    {
        $closeType = "'button',";

        if ((int)Configuration::get('CUSTOMPOPUP_OVERLAY') == 1) {
            $closeType .= "'overlay',";
        }

        $array = array(
            'pc_popup_enabled' => Configuration::get('CUSTOMPOPUP_ENABLED'),
            'pc_start_date' => Configuration::get('CUSTOMPOPUP_START_DATE'),
            'pc_end_date' => Configuration::get('CUSTOMPOPUP_END_DATE'),
            'pc_products' => Configuration::get('CUSTOMPOPUP_PRODUCTS'),
            'pc_categories' => Configuration::get('CUSTOMPOPUP_CATEGORIES'),
            'pc_popup_color' => Configuration::get('CUSTOMPOPUP_COLOR'),
            'pc_back_color' => Configuration::get('CUSTOMPOPUP_BACK_COLOR'),
            'pc_padding' => Configuration::get('CUSTOMPOPUP_PADDING'),
            'pc_top_padding' => Configuration::get('CUSTOMPOPUP_TOP_PADDING'),
            'pc_button_color' => Configuration::get('CUSTOMPOPUP_BUTTON_COLOR'),
            'pc_button_hover_color' => Configuration::get('CUSTOMPOPUP_BUTTON_HOVER_COLOR'),
            'pc_button_size' => Configuration::get('CUSTOMPOPUP_BUTTON_SIZE'),
            'pc_button_top_padding' => Configuration::get('CUSTOMPOPUP_BUTTON_TOP_PADDING'),
            'pc_button_position' => Configuration::get('CUSTOMPOPUP_BUTTON_POSITION'),
            'pc_closetype' => rtrim($closeType, ','),
            'pc_footer' => Configuration::get('CUSTOMPOPUP_FOOTER'),
            'pc_footer_background' => Configuration::get('CUSTOMPOPUP_FOOTER_BACKGROUND'),
            'pc_footer_type' => Configuration::get('CUSTOMPOPUP_FOOTER_TYPE'),
            'pc_footer_align' => Configuration::get('CUSTOMPOPUP_FOOTER_ALIGN'),
            'pc_footer_button1_enabled' => Configuration::get('CUSTOMPOPUP_BUTTON1_ENABLED'),
            'pc_footer_button2_enabled' => Configuration::get('CUSTOMPOPUP_BUTTON2_ENABLED'),
            'pc_footer_button1_background' => Configuration::get('CUSTOMPOPUP_BUTTON1_BACKGROUND'),
            'pc_footer_button2_background' => Configuration::get('CUSTOMPOPUP_BUTTON2_BACKGROUND'),
            'pc_button1_new_tab' => Configuration::get('CUSTOMPOPUP_BUTTON1_NEW_TAB'),
            'pc_button2_new_tab' => Configuration::get('CUSTOMPOPUP_BUTTON2_NEW_TAB'),
        );

        $footer = array();

        foreach (Language::getLanguages(true) as $lang) {
            $footer['pc_footer_text_'.$lang["id_lang"]] = Configuration::get(
                'CUSTOMPOPUP_FOOTER_TEXT', $lang["id_lang"]
            );

            $footer['pc_button1_text_'.$lang["id_lang"]] = Configuration::get(
                'CUSTOMPOPUP_BUTTON1_TEXT', $lang["id_lang"]
            );

            $footer['pc_button2_text_'.$lang["id_lang"]] = Configuration::get(
                'CUSTOMPOPUP_BUTTON2_TEXT', $lang["id_lang"]
            );

            $footer['pc_button1_url_'.$lang["id_lang"]] = Configuration::get(
                'CUSTOMPOPUP_BUTTON1_URL', $lang["id_lang"]
            );

            $footer['pc_button2_url_'.$lang["id_lang"]] = Configuration::get(
                'CUSTOMPOPUP_BUTTON2_URL', $lang["id_lang"]
            );
        }

        $array = array_merge($array, $footer);

        return $array;
    }

    public static function getVersion()
    {
        return Tools::substr(_PS_VERSION_, 0, 3);
    }
}