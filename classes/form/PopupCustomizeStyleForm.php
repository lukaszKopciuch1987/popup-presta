<?php


require_once _PS_MODULE_DIR_.'/popup/core/PopupModuleCore.php';

class PopupCustomizeStyleForm extends PopupModuleCore
{
    public function __construct($moduleObject)
    {
        parent::__construct($moduleObject, __CLASS__);
    }

    public function render()
    {
        $this->fields = array(
            'form' => array(
                'legend' => array('title' => $this->module->l('popup style')),
                'submit' => array(
                    'title' => $this->module->l('Save')
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'TAB_2',
                        'value' => '1',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->module->l('popup color'),
                        'name' => 'CUSTOMPOPUP_COLOR',
                        'class' => 'leftfix',
                        'hint' => $this->module->l('Inside the popup')
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->module->l('Background color'),
                        'name' => 'CUSTOMPOPUP_BACK_COLOR',
                        'class' => 'leftfix',
                        'hint' => $this->module->l('Outside the popup'),
                        'desc' => $this->module->l('Leave empty to apply transparent background'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Content padding'),
                        'name' => 'CUSTOMPOPUP_PADDING',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->module->l('pixels'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Content top padding'),
                        'name' => 'CUSTOMPOPUP_TOP_PADDING',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->module->l('pixels'),
                    ),
                ),

            ),
        );

        return $this;
    }

    public function getFieldsValues()
    {
        $fields = array();

        $fields['TAB_2'] = Configuration::get('TAB_2');
        $fields['CUSTOMPOPUP_COLOR'] = Configuration::get('CUSTOMPOPUP_COLOR');
        $fields['CUSTOMPOPUP_BACK_COLOR'] = Configuration::get('CUSTOMPOPUP_BACK_COLOR');
        $fields['CUSTOMPOPUP_PADDING'] = Configuration::get('CUSTOMPOPUP_PADDING');
        $fields['CUSTOMPOPUP_TOP_PADDING'] = Configuration::get('CUSTOMPOPUP_TOP_PADDING');

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
