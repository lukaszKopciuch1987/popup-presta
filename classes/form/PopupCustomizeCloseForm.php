<?php


require_once _PS_MODULE_DIR_.'/popup/core/PopupModuleCore.php';

class PopupCustomizeCloseForm extends PopupModuleCore
{
    public function __construct($moduleObject)
    {
        parent::__construct($moduleObject, __CLASS__);
    }

    public function render()
    {
        $this->fields = array(
            'form' => array(
                'legend' => array('title' => $this->module->l('[X] Close button style')),
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
                        'label' => $this->module->l('Color'),
                        'name' => 'CUSTOMPOPUP_BUTTON_COLOR',
                        'class' => 'leftfix'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->module->l('Hover color'),
                        'hint' => $this->module->l('Color shown after moving your cursor over the button'),
                        'name' => 'CUSTOMPOPUP_BUTTON_HOVER_COLOR',
                        'class' => 'leftfix'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Size'),
                        'name' => 'CUSTOMPOPUP_BUTTON_SIZE',
                        'class' => 'fixed-width-sm',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->module->l('Top padding'),
                        'name' => 'CUSTOMPOPUP_BUTTON_TOP_PADDING',
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
        $fields['CUSTOMPOPUP_BUTTON_COLOR'] = Configuration::get('CUSTOMPOPUP_BUTTON_COLOR');
        $fields['CUSTOMPOPUP_BUTTON_HOVER_COLOR'] = Configuration::get('CUSTOMPOPUP_BUTTON_HOVER_COLOR');
        $fields['CUSTOMPOPUP_BUTTON_SIZE'] = Configuration::get('CUSTOMPOPUP_BUTTON_SIZE');
        $fields['CUSTOMPOPUP_BUTTON_TOP_PADDING'] = Configuration::get('CUSTOMPOPUP_BUTTON_TOP_PADDING');

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
