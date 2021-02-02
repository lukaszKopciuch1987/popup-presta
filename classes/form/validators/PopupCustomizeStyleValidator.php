<?php


require_once _PS_MODULE_DIR_.'popup/core/PopupValidatorCore.php';
require_once _PS_MODULE_DIR_.'popup/classes/utils/PopupValidation.php';

class PopupCustomizeStyleValidator extends PopupValidatorCore
{
    public function __construct($moduleObject, $formName)
    {
        parent::__construct($moduleObject, $formName);
    }

    protected function processCP_Validation()
    {
        $this->validation = new PopupValidation($this->module);

        $this->validation->validate(
            $this->module->l('popup color'),
            $this->getField('CUSTOMPOPUP_COLOR'),
            array(
                'ishex' => 1,
                'notempty' => 1
            )
        );

        $this->validation->validate(
            $this->module->l('Background color'),
            $this->getField('CUSTOMPOPUP_BACK_COLOR')
        );

        $this->validation->validate(
            $this->module->l('Content padding'),
            $this->getField('CUSTOMPOPUP_PADDING'),
            array(
                'isnumber' => 1,
                'notempty' => 1
            )
        );

        $this->validation->validate(
            $this->module->l('Content top padding'),
            $this->getField('CUSTOMPOPUP_TOP_PADDING'),
            array(
                'isnumber' => 1,
                'notempty' => 1
            )
        );
    }

    protected function save()
    {
        if (!$this->validation->getError($this->module->l('popup color'))) {
            Configuration::updateValue('CUSTOMPOPUP_COLOR', $this->getField('CUSTOMPOPUP_COLOR'));
        }

        if (!$this->validation->getError($this->module->l('Background color'))) {
            Configuration::updateValue('CUSTOMPOPUP_BACK_COLOR', $this->getField('CUSTOMPOPUP_BACK_COLOR'));
        }

        if (!$this->validation->getError($this->module->l('Content padding'))) {
            Configuration::updateValue('CUSTOMPOPUP_PADDING', $this->getField('CUSTOMPOPUP_PADDING'));
        }

        if (!$this->validation->getError($this->module->l('Content top padding'))) {
            Configuration::updateValue('CUSTOMPOPUP_TOP_PADDING', $this->getField('CUSTOMPOPUP_TOP_PADDING'));
        }
    }
}
