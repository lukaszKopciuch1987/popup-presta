<?php



require_once _PS_MODULE_DIR_.'/popup/core/PopupModuleCore.php';

class PopupSettingsForm extends PopupModuleCore
{
    public function __construct($moduleObject)
    {
        parent::__construct($moduleObject, __CLASS__);
    }

    public function render()
    {
        $this->fields = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'TAB_1',
                        'value' => '1',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->module->l('Enable popup?'),
                        'name' => 'CUSTOMPOPUP_ENABLED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->module->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->module->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->module->l('Start date:'),
                        'name' => 'CUSTOMPOPUP_START_DATE',
                        'size' => 10,
                        'required' => false,
                        'desc' => $this->module->l('The expected date of start popup showing'),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->module->l('End date:'),
                        'name' => 'CUSTOMPOPUP_END_DATE',
                        'size' => 10,
                        'required' => false,
                        'desc' => $this->module->l('The expected date of end popup showing'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Products IDs'),
                        'name' => 'CUSTOMPOPUP_PRODUCTS',
                        'desc'  => $this->module->l('IDS of products (coma separated)'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Categories IDs'),
                        'name' => 'CUSTOMPOPUP_CATEGORIES',
                        'desc'  => $this->module->l('IDS of categories (coma separated)'),
                    ),
                    array(
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'type' => 'textarea',
                        'lang' => true,
                        'label' => $this->module->l('popup content'),
                        'name' => 'CUSTOMPOPUP_CONTENT',
                        'required' => true,
                        'cols' => 40,
                        'rows' => 20,
                        'desc' => '<strong>'.$this->module->l(
                            'REMEMBER TO FILL CONTENT FOR ALL LANGUAGES BEFORE SAVING'
                        ).'</strong>',
                    )
                ),
                'submit' => array(
                    'title' => $this->module->l('Save')
                )
            ),
        );

        $this->module->addJqueryUI('ui.datepicker');

        return $this;
    }

    public function getFieldsValues()
    {
        $languages = Language::getLanguages(true);
        $fields = array();

        $fields['TAB_1'] = Configuration::get('TAB_1');

        foreach ($languages as $lang) {
            @$fields['CUSTOMPOPUP_CONTENT'][$lang['id_lang']] = Configuration::get("CUSTOMPOPUP_CONTENT", $lang['id_lang']);
        }

        $fields['CUSTOMPOPUP_ENABLED'] = Configuration::get('CUSTOMPOPUP_ENABLED');
        $fields['CUSTOMPOPUP_START_DATE'] = Configuration::get('CUSTOMPOPUP_START_DATE');
        $fields['CUSTOMPOPUP_END_DATE'] = Configuration::get('CUSTOMPOPUP_END_DATE');
        $fields['CUSTOMPOPUP_PRODUCTS'] = Configuration::get('CUSTOMPOPUP_PRODUCTS');
        $fields['CUSTOMPOPUP_CATEGORIES'] = Configuration::get('CUSTOMPOPUP_CATEGORIES');

        return $fields;
    }

    public function install()
    {
        // TODO: Implement install() method.
    }

    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    public function getContent()
    {
        // TODO: Implement getContent() method.
    }

    public function postProcess()
    {
        // TODO: Implement postProcess() method.
    }
}
