<?php

if (!defined('_PS_VERSION_') || !defined('_PS_MODULE_DIR_')) {
    exit;
}

// Core
require_once _PS_MODULE_DIR_.'/popup/core/PopupModuleInterface.php';

// Database
require_once _PS_MODULE_DIR_.'/popup/classes/db/PopupResponsivePopupPages.php';

// Forms
require_once _PS_MODULE_DIR_.'/popup/classes/form/PopupCloseAndFooterForm.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/PopupCustomizeCloseForm.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/PopupCustomizeStyleForm.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/PopupDisplayForm.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/PopupSettingsForm.php';
// Validators
require_once _PS_MODULE_DIR_.'/popup/classes/form/validators/PopupCloseAndFooterValidator.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/validators/PopupCustomizeCloseValidator.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/validators/PopupCustomizeStyleValidator.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/validators/PopupDisplayValidator.php';
require_once _PS_MODULE_DIR_.'/popup/classes/form/validators/PopupSettingsValidator.php';

// Utils
require_once _PS_MODULE_DIR_.'/popup/classes/utils/PopupHooks.php';
require_once _PS_MODULE_DIR_.'/popup/classes/utils/PopupVariables.php';

class Popup extends Module implements PopupModuleInterface
{
    private $errors;
    private $success = false;
    private $dynamicHooking = true;

    public function __construct()
    {
        $this->name = 'popup';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'Lukasz Kopciuch';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6', 'max' => _PS_VERSION_
        ];

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom popup notification');
        $this->description = $this->l('Customize and display a responsive popup window for chosen page(s).');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /**
     * Magic method to call hooks dynamically, based on user choice
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if ($this->dynamicHooking) {
            $prevent = false;

            if (!$prevent) {
                if (function_exists($method))
                    return call_user_func_array($method, $args);

                // Check for a call to an hook
                if (strpos($method, 'hook') !== false) {
                    return $this->functionHook($args[0]);
                }
            }
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        foreach (Language::getLanguages(true) as $lang) {
            $content='CUSTOMPOPUP_CONTENT_'.$lang['id_lang'];
            Configuration::updateValue($content, $lang['name']);
        }

        PopupVariables::setDefaultValues();

        return parent::install() &&
            PopupResponsivePopupPages::createTable() &&
            PopupResponsivePopupPages::fixtures();
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        PopupResponsivePopupPages::drop();
        return true;
    }

    /**
     * Module configuration page
     *
     * @return mixed
     */
    public function getContent()
    {
        $this->postProcess();
        $this->hookService();

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('colorpicker_path', __PS_BASE_URI__.'js/jquery/plugins/jquery.colorpicker.js');
        $this->context->smarty->assign('CUSTOMPOPUP_COLOR', Configuration::get('CUSTOMPOPUP_COLOR'));
        $this->context->smarty->assign('CUSTOMPOPUP_BACK_COLOR', Configuration::get('CUSTOMPOPUP_BACK_COLOR'));
        $this->context->smarty->assign('CUSTOMPOPUP_BUTTON_COLOR', Configuration::get('CUSTOMPOPUP_BUTTON_COLOR'));
        $this->context->smarty->assign(
            'CUSTOMPOPUP_BUTTON_HOVER_COLOR',
            Configuration::get('CUSTOMPOPUP_BUTTON_HOVER_COLOR')
        );
        $this->context->smarty->assign('IF_REQUIRE_HOOK_UPDATE', PopupHooks::ifRequireHookUpdate());

        // Tabs
        $this->context->smarty->assign('TAB_SETTINGS', $this->renderSettings());
        $this->context->smarty->assign('TAB_CUSTOMIZE_STYLE', $this->renderCustomizeStyle());
        $this->context->smarty->assign('TAB_CUSTOMIZE_CLOSE', $this->renderCustomizeClose());
        $this->context->smarty->assign('TAB_CLOSE_AND_FOOTER', $this->renderCloseAndFooter());
        $this->context->smarty->assign('TAB_DISPLAY', $this->renderDisplay());

        if (Configuration::get("PS_MULTISHOP_FEATURE_ACTIVE")) {
            $this->context->smarty->assign('multistore', true);
        }

        if ($this->errors) {
            $this->context->smarty->assign('errors', $this->errors);
        }

        if ($this->success) {
            $this->context->smarty->assign('success', $this->l('The settings have been updated.'));
        }

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * Handling form validation & sending
     *
     * @return string
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitHookUpdate')) {
            PopupHooks::updateHooks();
        } else {
            $settingsData = array(
                'CUSTOMPOPUP_ENABLED' => Tools::getValue('CUSTOMPOPUP_ENABLED'),
                'CUSTOMPOPUP_START_DATE' => Tools::getValue('CUSTOMPOPUP_START_DATE'),
                'CUSTOMPOPUP_END_DATE' => Tools::getValue('CUSTOMPOPUP_END_DATE'),
                'CUSTOMPOPUP_PRODUCTS' => Tools::getValue('CUSTOMPOPUP_PRODUCTS'),
                'CUSTOMPOPUP_CATEGORIES' => Tools::getValue('CUSTOMPOPUP_CATEGORIES'),
            );

            $langContent = array();

            foreach (Language::getLanguages(true) as $la) {
                $langContent['CUSTOMPOPUP_CONTENT_'.$la['id_lang']] = Tools::getValue(
                    'CUSTOMPOPUP_CONTENT_'.$la['id_lang']
                );
            }

            $settingsDataAll = array_merge($settingsData, $langContent);

            $CP_SettingsValidator = new PopupSettingsValidator($this, 'PopupSettingsForm');
            $CP_SettingsValidator->setData($settingsDataAll);
            $CP_SettingsValidator->validate();

            if ($CP_SettingsValidator->getErrors()) {
                $this->errors = $CP_SettingsValidator->getErrors();
            }

            $customizeStyleData = array(
                'CUSTOMPOPUP_COLOR' => Tools::getValue('CUSTOMPOPUP_COLOR'),
                'CUSTOMPOPUP_BACK_COLOR' => Tools::getValue('CUSTOMPOPUP_BACK_COLOR'),
                'CUSTOMPOPUP_PADDING' => Tools::getValue('CUSTOMPOPUP_PADDING'),
                'CUSTOMPOPUP_TOP_PADDING' => Tools::getValue('CUSTOMPOPUP_TOP_PADDING'),
            );

            $CP_CustomizeStyleValidator = new PopupCustomizeStyleValidator($this, 'PopupCustomizeStyleForm');
            $CP_CustomizeStyleValidator->setData($customizeStyleData);
            $CP_CustomizeStyleValidator->validate();

            if ($CP_CustomizeStyleValidator->getErrors()) {
                $this->errors = $CP_CustomizeStyleValidator->getErrors();
            }

            $custoimzeCloseData = array(
                'CUSTOMPOPUP_BUTTON_COLOR' => Tools::getValue('CUSTOMPOPUP_BUTTON_COLOR'),
                'CUSTOMPOPUP_BUTTON_HOVER_COLOR' => Tools::getValue('CUSTOMPOPUP_BUTTON_HOVER_COLOR'),
                'CUSTOMPOPUP_BUTTON_SIZE' => Tools::getValue('CUSTOMPOPUP_BUTTON_SIZE'),
                'CUSTOMPOPUP_BUTTON_TOP_PADDING' => Tools::getValue('CUSTOMPOPUP_BUTTON_TOP_PADDING'),
                'CUSTOMPOPUP_BUTTON_POSITION' => Tools::getValue('CUSTOMPOPUP_BUTTON_POSITION'),
            );

            $CP_CustomizeCloseValidator = new PopupCustomizeCloseValidator($this, 'PopupCustomizeCloseForm');
            $CP_CustomizeCloseValidator->setData($custoimzeCloseData);
            $CP_CustomizeCloseValidator->validate();

            if ($CP_CustomizeCloseValidator->getErrors()) {
                $this->errors = $CP_CustomizeCloseValidator->getErrors();
            }

            $displayData = array();

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'pages_') === 0) {
                    if ($value) {
                        $displayData[str_replace("pages_", "", $key)] = 1;
                    }
                }
            }

            $CP_DisplayValidator = new PopupDisplayValidator($this, 'PopupDisplayForm');
            $CP_DisplayValidator->setData($displayData, true);
            $CP_DisplayValidator->validate();

            $closeAndFooterDataCloseType = array();

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'closetype_') === 0) {
                    if ($value) {
                        $closeAndFooterDataCloseType[$key] = 1;
                    }
                }
            }

            $closeAndFooterLangData = array();

            foreach (Language::getLanguages(true) as $la) {
                $closeAndFooterLangData['CUSTOMPOPUP_BUTTON1_TEXT_'.$la['id_lang']] =
                    Tools::getValue('CUSTOMPOPUP_BUTTON1_TEXT_'.$la['id_lang']);

                $closeAndFooterLangData['CUSTOMPOPUP_BUTTON2_TEXT_'.$la['id_lang']] =
                    Tools::getValue('CUSTOMPOPUP_BUTTON2_TEXT_'.$la['id_lang']);

                $closeAndFooterLangData['CUSTOMPOPUP_FOOTER_TEXT_'.$la['id_lang']] =
                    Tools::getValue('CUSTOMPOPUP_FOOTER_TEXT_'.$la['id_lang']);

                $closeAndFooterLangData['CUSTOMPOPUP_BUTTON1_URL_'.$la['id_lang']] =
                    Tools::getValue('CUSTOMPOPUP_BUTTON1_URL_'.$la['id_lang']);

                $closeAndFooterLangData['CUSTOMPOPUP_BUTTON2_URL_'.$la['id_lang']] =
                    Tools::getValue('CUSTOMPOPUP_BUTTON2_URL_'.$la['id_lang']);
            }

            $closeAndFooterData = array(
                'CUSTOMPOPUP_FOOTER' => Tools::getValue('CUSTOMPOPUP_FOOTER'),
                'CUSTOMPOPUP_BUTTON1_TEXT' => Tools::getValue('CUSTOMPOPUP_BUTTON1_TEXT'),
                'CUSTOMPOPUP_BUTTON2_TEXT' => Tools::getValue('CUSTOMPOPUP_BUTTON2_TEXT'),
                'CUSTOMPOPUP_BUTTON1_URL' => Tools::getValue('CUSTOMPOPUP_BUTTON1_URL'),
                'CUSTOMPOPUP_BUTTON2_URL' => Tools::getValue('CUSTOMPOPUP_BUTTON2_URL'),
                'CUSTOMPOPUP_BUTTON1_BACKGROUND' => Tools::getValue('CUSTOMPOPUP_BUTTON1_BACKGROUND'),
                'CUSTOMPOPUP_BUTTON2_BACKGROUND' => Tools::getValue('CUSTOMPOPUP_BUTTON2_BACKGROUND'),
                'CUSTOMPOPUP_FOOTER_TEXT' => Tools::getValue('CUSTOMPOPUP_FOOTER_TEXT'),
                'CUSTOMPOPUP_FOOTER_ALIGN' => Tools::getValue('CUSTOMPOPUP_FOOTER_ALIGN'),
                'CUSTOMPOPUP_FOOTER_TYPE' => Tools::getValue('CUSTOMPOPUP_FOOTER_TYPE'),
                'CUSTOMPOPUP_BUTTON1_ENABLED' => Tools::getValue('CUSTOMPOPUP_BUTTON1_ENABLED'),
                'CUSTOMPOPUP_BUTTON2_ENABLED' => Tools::getValue('CUSTOMPOPUP_BUTTON2_ENABLED'),
                'CUSTOMPOPUP_BUTTON1_NEW_TAB' => Tools::getValue('CUSTOMPOPUP_BUTTON1_NEW_TAB'),
                'CUSTOMPOPUP_BUTTON2_NEW_TAB' => Tools::getValue('CUSTOMPOPUP_BUTTON2_NEW_TAB'),
                'CUSTOMPOPUP_FOOTER_BACKGROUND' => Tools::getValue('CUSTOMPOPUP_FOOTER_BACKGROUND'),
            );

            $closeAndFooterDataAll = array_merge(
                $closeAndFooterDataCloseType,
                $closeAndFooterData,
                $closeAndFooterLangData
            );

            $CP_CloseAndFooterValidator = new PopupCloseAndFooterValidator($this, 'PopupCloseAndFooterForm');
            $CP_CloseAndFooterValidator->setData($closeAndFooterDataAll);
            $CP_CloseAndFooterValidator->validate();

            if ($CP_CloseAndFooterValidator->getErrors()) {
                $this->errors = $CP_CloseAndFooterValidator->getErrors();
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!$this->errors) {
                    $this->success = true;
                }
            }
        }

        return '';
    }

    // ---- Forms [start] ----
    public function renderSettings()
    {
        $form = new PopupSettingsForm($this);
        return $form->render()->buildForm();
    }

    public function renderCustomizeStyle()
    {
        $form = new PopupCustomizeStyleForm($this);
        return $form->render()->buildForm();
    }

    public function renderCustomizeClose()
    {
        $form = new PopupCustomizeCloseForm($this);
        return $form->render()->buildForm();
    }

    public function renderCloseAndFooter()
    {
        $form = new PopupCloseAndFooterForm($this);
        return $form->render()->buildForm();
    }

    public function renderDisplay()
    {
        $form = new PopupDisplayForm($this);
        return $form->render()->buildForm();
    }
    // ---- Forms [end] ----

    // ---- Hooks [start] ----
    private function hookService()
    {
        $enabledHooks = array();
        $rpp = new PopupResponsivePopupPages();

        foreach ($rpp->getAll() as $item) {
            if ($item["enabled"] == 1) {
                $this->registerHook($item["id_page"]);
                $enabledHooks[] = $item["id_page"];
            } else {
                $this->unregisterHook($item["id_page"]);
            }
        }
    }

    public function functionHook()
    {
        $langContent = array();

        foreach (Language::getLanguages(true) as $lang) {
            $content = Configuration::get("CUSTOMPOPUP_CONTENT", $lang["id_lang"]);
            $langContent['pc_content_'.$lang["id_lang"]] = trim(json_encode($content), '"');
        }

        $scripts = array(
            'pc_css_tingle'  => $this->_path.'views/css/tingle.min.css',
            'pc_css_popup'  => $this->_path.'views/css/popup.css',
            'pc_js_tingle' => $this->_path.'views/js/tingle.min.js',
            'pc_jquery'    => $this->_path.'views/js/jquery.min.js'
        );

        $assign = PopupVariables::getTemplateVars();

        if($assign['pc_start_date'] && $assign['pc_end_date']){
            if(strtotime($assign['pc_start_date']) >= strtotime(date('Y-m-d')) || strtotime($assign['pc_end_date']) <= strtotime(date('Y-m-d'))){
                die();
            }
        }

        $idProduct=(int)Tools::getValue('id_product');

        if($assign['pc_products'] && !in_array($idProduct, explode(',', $assign['pc_products']))){
            die();
        }

        $idCategory=(int)Tools::getValue('id_category');

        if($assign['pc_categories'] && !in_array($idCategory, explode(',', $assign['pc_categories']))){
            die();
        }

        $all = array_merge($langContent, $scripts, $assign);
        $this->context->smarty->assign($all);

        return $this->display(__FILE__, 'popup.tpl');
    }

    // ---- Hooks [end] ----
}
