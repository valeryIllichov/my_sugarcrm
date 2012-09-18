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
/*********************************************************************************
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
********************************************************************************/
// SugarCRM free/busy server
// put and get free/busy information for sugarcrm users in vCalendar format.
// Uses WebDAV for HTTP PUT and GET methods of access
// REQUIRED PHP packages:
// 1. PEAR
//
// Saves PUTs as Freebusy SugarBeans
//
// documentation on Free/Busy from Microsoft:
// http://support.microsoft.com/kb/196484
//
// other docs:
// http://www.windowsitpro.com/MicrosoftExchangeOutlook/Article/ArticleID/7697/7697.html
//
// excerpt:
// You must install the Microsoft Internet Explorer (IE) Web Publishing Wizard to get
// the functionality you need to publish Internet free/busy data to a server or the Web.
// You can install this wizard from Control Panel, Add/Remove Programs, Microsoft Internet
// Explorer, Web Publishing Wizard. For every user, you must configure the path and filename
// where you want Outlook to store free/busy information. You configure this location on the
// Free/Busy Options dialog box you see in Screen 2. You must initiate publishing manually by
// using Tools, Send/Receive, Free/Busy Information in Outlook.
//
// To access a non-Exchange Server user's free/busy information, you must configure the
// appropriate path on each contact's Details tab. For example, you enter
// "http://servername/sugarcrm/index.php?entryPoint=vcal_server/type=vfb&source=outlook&email=myemail@servername.com".
// If you don't configure this information correctly, the client software looks up the entry
// in the Search at this URL window on the Free/Busy Options dialog box.
//
// Setup for: Microsoft Outlook XP
// In Tools > Options > Calendar Options > Free/Busy
//
// Global search path:
// %USERNAME% and %SERVER% are Outlook replacement variables to construct the email address:
// http://servername/sugarcrm/index.php?entryPoint=vcal_server/type=vfb&source=outlook&email=%NAME%@%SERVER%
// or contact by contact by editing the details and entering its Free/Busy URL:
// http://servername/sugarcrm/index.php?entryPoint=vcal_server/type=vfb&source=outlook&email=user@email.com
// or
// http://servername/sugarcrm/index.php?entryPoint=vcal_server/type=vfb&source=outlook&user_name=user_name
// or:
// http://servername/sugarcrm/index.php?entryPoint=vcal_server/type=vfb&source=outlook&user_id=user_id
	require_once "modules/vCals/HTTP_WebDAV_Server_vCal_cal2.php";
	$server = new HTTP_WebDAV_Server_vCal_cal2();
	$server->ServeRequest();
	sugar_cleanup();
?>
