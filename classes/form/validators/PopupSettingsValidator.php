<?php


require_once _PS_MODULE_DIR_.'popup/core/PopupValidatorCore.php';
require_once _PS_MODULE_DIR_.'popup/classes/utils/PopupValidation.php';

class PopupSettingsValidator extends PopupValidatorCore
{
    public function __construct($moduleObject, $formName)
    {
        parent::__construct($moduleObject, $formName);
    }

    protected function processCP_Validation()
    {
        $this->validation = new PopupValidation($this->module);

        foreach (Language::getLanguages(true) as $lang) {
            $this->validation->validate(
                $this->module->l('popup content'),
                $this->getField('CUSTOMPOPUP_CONTENT_'.$lang['id_lang']),
                array('notempty' => 1)
            );
        }
    }

    protected function save()
    {
        Configuration::updateValue('CUSTOMPOPUP_ENABLED', $this->getField('CUSTOMPOPUP_ENABLED'));
        Configuration::updateValue('CUSTOMPOPUP_START_DATE', $this->getField('CUSTOMPOPUP_START_DATE'));
        Configuration::updateValue('CUSTOMPOPUP_END_DATE', $this->getField('CUSTOMPOPUP_END_DATE'));
        Configuration::updateValue('CUSTOMPOPUP_PRODUCTS', $this->getField('CUSTOMPOPUP_PRODUCTS'));
        Configuration::updateValue('CUSTOMPOPUP_CATEGORIES', $this->getField('CUSTOMPOPUP_CATEGORIES'));


        // if no errors occured
        if (!$this->validation->getError($this->module->l('popup content'))) {
            foreach (Language::getLanguages(true) as $lang) {
                Configuration::updateValue("CUSTOMPOPUP_CONTENT", array(
                        $lang['id_lang'] => $this->getField('CUSTOMPOPUP_CONTENT_'.$lang['id_lang']),
                        $lang['id_lang'] => $this->getField('CUSTOMPOPUP_CONTENT_'.$lang['id_lang'])
                    ), true
                );
            }
        }
    }
}
