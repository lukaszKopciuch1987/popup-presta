<?php


require_once _PS_MODULE_DIR_.'/popup/core/PopupModuleCore.php';
require_once _PS_MODULE_DIR_.'/popup/classes/db/PopupResponsivePopupPages.php';

class PopupDisplayForm extends PopupModuleCore
{
    public function __construct($moduleObject)
    {
        parent::__construct($moduleObject, __CLASS__);
    }

    public function render()
    {
        $rpp = new PopupResponsivePopupPages();
        $pages = array();

        foreach ($rpp->getAll(true) as $item) {
            $pages[$item['id_page']] = array(
                'id' => $item['id_page'],
                'name' => $item['id_page']
            );
        }

        $this->fields = array(
            'form' => array(
                'legend' => array('title' => $this->module->l('Where do you want to display the popup?')),
                'submit' => array(
                    'title' => $this->module->l('Save')
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'TAB_4',
                        'value' => '1',
                    ),
                    array(
                        'type'    => 'checkbox',
                        'name'    => 'pages',
                        'values'  => array(
                            'query' => $pages,
                            'id'    => 'id',
                            'name'  => 'name'
                        ),
                    )
                ),
            ),
        );

        return $this;
    }

    public function getFieldsValues()
    {
        $fields = array();
        $fields['TAB_4'] = Configuration::get('TAB_4');
        $rpp = new PopupResponsivePopupPages();

        foreach ($rpp->getAll() as $item) {
            $fields['pages_'.$item["id_page"]] = PopupResponsivePopupPages::checkEnable($item["id_page"]);
        }

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
