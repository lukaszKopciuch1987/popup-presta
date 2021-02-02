<?php


/**
 * Interface PopupModuleInterface
 */

interface PopupModuleInterface
{
    public function install();

    public function uninstall();

    public function getContent();

    public function postProcess();
}
