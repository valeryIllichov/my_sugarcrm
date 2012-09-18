<?php 
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/******************************************************************************
OpensourceCRM End User License Agreement

INSTALLING OR USING THE OpensourceCRM's SOFTWARE THAT YOU HAVE SELECTED TO 
PURCHASE IN THE ORDERING PROCESS (THE "SOFTWARE"), YOU ARE AGREEING ON BEHALF OF
THE ENTITY LICENSING THE SOFTWARE ("COMPANY") THAT COMPANY WILL BE BOUND BY AND 
IS BECOMING A PARTY TO THIS END USER LICENSE AGREEMENT ("AGREEMENT") AND THAT 
YOU HAVE THE AUTHORITY TO BIND COMPANY.

IF COMPANY DOES NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, DO NOT SELECT 
THE "ACCEPT" BOX AND DO NOT INSTALL THE SOFTWARE. THE SOFTWARE IS PROTECTED BY 
COPYRIGHT LAWS AND INTERNATIONAL COPYRIGHT TREATIES, AS WELL AS OTHER 
INTELLECTUAL PROPERTY LAWS AND TREATIES. THE SOFTWARE IS LICENSED, NOT SOLD.

    *The COMPANY may not copy, deliver, distribute the SOFTWARE without written
     permit from OpensourceCRM.
    *The COMPANY may not reverse engineer, decompile, or disassemble the 
    SOFTWARE, except and only to the extent that such activity is expressly 
    permitted by applicable law notwithstanding this limitation.
    *The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
     value, the SOFTWARE.
    *Termination. Without prejudice to any other rights, OpensourceCRM may 
    terminate this Agreement if the COMPANY fail to comply with the terms and 
    conditions of this Agreement. In such event, the COMPANY must destroy all 
    copies of the SOFTWARE and all of its component parts.
    *OpensourceCRM will give the COMPANY notice and 30 days to correct above 
    before the contract will be terminated.

The SOFTWARE is protected by copyright and other intellectual property laws and 
treaties. OpensourceCRM owns the title, copyright, and other intellectual 
property rights in the SOFTWARE.
*****************************************************************************/
global $mod_strings;

$module_menu[]=Array("index.php?module=Calendar2&action=index&return_module=Resources&return_action=index", $mod_strings['LNK_CALENDAR2'],"Calendar");
//if(ACLController::checkAccess('Resources', 'edit', true))$module_menu[]=Array("index.php?module=Resources&action=EditView&return_module=Resources&return_action=DetailView", $mod_strings['LNK_NEW_RES'],"CreateResource", 'Resources');
if(ACLController::checkAccess('Resources', 'edit', true))$module_menu[]=Array("index.php?module=Resources&action=EditView&return_module=Resources&return_action=DetailView", $mod_strings['LNK_NEW_RES'],"CreateResource");
//if(ACLController::checkAccess('Resources', 'list', true))$module_menu [] =Array("index.php?module=Resources&action=index&return_module=Resources&return_action=DetailView", $mod_strings['LBL_LIST_FORM_TITLE'],"Resources", 'Resources');
if(ACLController::checkAccess('Resources', 'list', true))$module_menu [] =Array("index.php?module=Resources&action=index&return_module=Resources&return_action=DetailView", $mod_strings['LBL_LIST_FORM_TITLE'],"Resources");
//if(ACLController::checkAccess('Resources', 'list', true))$module_menu[]=Array("index.php?module=Resources&action=WeeklyListView&return_module=Resources&return_action=DetailView", $mod_strings['LNK_RES_CAL'],"Resources", 'Resources');
if(ACLController::checkAccess('Resources', 'list', true))$module_menu[]=Array("index.php?module=Resources&action=WeeklyListView&return_module=Resources&return_action=DetailView", $mod_strings['LNK_RES_CAL'],"Resources");
//if(ACLController::checkAccess('Meetings', 'edit', true))$module_menu[]=Array("index.php?module=Meetings&action=EditView&return_module=Meetings&return_action=DetailView", $mod_strings['LNK_NEW_MEETING'],"CreateMeetings", 'Resources');
?>
