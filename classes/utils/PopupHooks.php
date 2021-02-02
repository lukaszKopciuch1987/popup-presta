<?php


require_once(_PS_MODULE_DIR_.'/popup/classes/db/PopupResponsivePopupPages.php');

class PopupHooks
{
    /**
     * Get hooks using PrestaShop getHooks() method, but with extra filter - in case you want only FrontOffice hooks
     *
     * @param bool $frontOfficeOnly
     * @param bool $position
     * @param bool $hideActions
     *
     * @return array
     */
    public static function getHooks($frontOfficeOnly = false, $position = false, $hideActions = false)
    {
        $hooks = Hook::getHooks($position);
        $hooksReturn = array();

        if ($frontOfficeOnly) {
            foreach ($hooks as $hook) {
                if (!strpos($hook["name"], "Admin") && !strpos($hook["name"], "BackOffice")) {
                    $hooksReturn[] = $hook["name"];
                }
            }
        } else {
            foreach ($hooks as $hook) {
                $hooksReturn[] = $hook["name"];
            }
        }

        if ($hideActions) {
            foreach ($hooksReturn as $k => $v) {
                if (Tools::substr($v, 0, 6) == "action") {
                    unset($hooksReturn[$k]);
                }
            }
        }

        return $hooksReturn;
    }

    public static function ifRequireHookUpdate()
    {
        if (count(PopupHooks::getMissingHooks()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getMissingHooks()
    {
        $psHooks = PopupHooks::getHooks(true, false, true);
        $rpp = new PopupResponsivePopupPages();
        $moduleHookList = $rpp->getAll();

        $psHooksArray = array();
        $moduleHookListArray = array();
        $missing = array();

        foreach ($psHooks as $hook) {
            $psHooksArray[] = $hook;
        }

        foreach ($moduleHookList as $hook) {
            $moduleHookListArray[] = $hook["id_page"];
        }

        foreach ($psHooksArray as $hook) {
            if (!in_array($hook, $moduleHookListArray)) {
                $missing[] = $hook;
            }
        }

        return $missing;
    }

    /**
     * Add missing hooks
     */
    public static function updateHooks()
    {
        $shops = Shop::getShops(true, null, true);

        foreach ($shops as $shopid) {
            foreach (PopupHooks::getMissingHooks() as $hook) {
                $rpp = new PopupResponsivePopupPages();
                $rpp->id_page = $hook;
                $rpp->id_shop = $shopid;
                $rpp->enabled = 0;
                $rpp->save();
            }
        }
    }
}
