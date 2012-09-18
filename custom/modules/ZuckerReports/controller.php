<?php

class ZuckerReportsController extends SugarController
{
    public function action_AjaxSave()
    {
	global $current_user;
        $current_user->setPreference('ORPersonalSettings', $_REQUEST['ORPersonalSettings']*24*60*60);
        $current_user->setPreference('SASRPersonalSettings', $_REQUEST['SASRPersonalSettings']*24*60*60);
        return true;
    }
}