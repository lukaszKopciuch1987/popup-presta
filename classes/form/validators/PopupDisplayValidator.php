<?php

require_once _PS_MODULE_DIR_.'popup/core/PopupValidatorCore.php';
require_once _PS_MODULE_DIR_.'popup/classes/utils/PopupValidation.php';
require_once _PS_MODULE_DIR_.'popup/classes/db/PopupResponsivePopupPages.php';

class PopupDisplayValidator extends PopupValidatorCore
{
    public function __construct($moduleObject, $formName)
    {
        parent::__construct($moduleObject, $formName);
    }

    protected function processCP_Validation()
    {
        return true;
    }

    protected function save()
    {
        PopupResponsivePopupPages::disableAll();

        foreach ($this->getData() as $hook => $value) {
            PopupResponsivePopupPages::setHookValue($hook, $value);
        }
    }
}
