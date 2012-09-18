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
$dictionary['Call']['fields']['resources'] = array (
  	'name' => 'resources',
    'type' => 'link',
    'relationship' => 'calls_resources',
    'source'=>'non-db',
	'vname'=>'LBL_RESOURCE',
);
$dictionary['Call']['fields']['cal2_category_c'] = array (
      'required' => '0',
      'name' => 'cal2_category_c',
      'vname' => 'LBL_CATEGORY',
      'type' => 'enum',
      'massupdate' => '0',
      'default' => 'First',
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 100,
      'options' => 'category_list',
      'studio' => 'visible',
      'dependency' => NULL,
      'id' => 'Callscal2_category_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_options_c'] = array (
      'required' => '0',
      'name' => 'cal2_options_c',
      'vname' => 'LBL_PRIVATE',
      'type' => 'bool',
      'massupdate' => '0',
      'default' => '0',
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '255',
      'id' => 'Callscal2_options_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_whole_day_c'] = array (
      'required' => '0',
      'name' => 'cal2_whole_day_c',
      'vname' => 'LBL_WHOLE_DAY',
      'type' => 'bool',
      'massupdate' => '0',
      'default' => '0',
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '255',
      'id' => 'Callscal2_whole_day_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_call_id_c'] = array (
      'required' => '0',
      'name' => 'cal2_call_id_c',
      'vname' => 'LBL_LIST_RELATED_TO',
      'type' => 'id',
      'massupdate' => '0',
      'default' => NULL,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '36',
      'id' => 'Callscal2_call_id_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_recur_id_c'] = array (
      'required' => '0',
      'source' => 'non-db',
      'name' => 'cal2_recur_id_c',
      'vname' => 'LBL_REC_ID',
      'type' => 'relate',
      'massupdate' => '0',
      'default' => NULL,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 1,
      'len' => '255',
      'id_name' => 'cal2_call_id_c',
      'ext2' => 'Calls',
      'module' => 'Calls',
      'rname' => 'name',
      'quicksearch' => 'enabled',
      'studio' => 'visible',
      'id' => 'Callscal2_recur_id_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_repeat_type_c'] = array (
      'required' => '0',
      'name' => 'cal2_repeat_type_c',
      'vname' => 'LBL_REPEAT_TYPE',
      'type' => 'varchar',
      'massupdate' => '0',
      'default' => NULL,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 1,
      'len' => '25',
      'id' => 'Callscal2_repeat_type_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_repeat_interval_c'] = array (
      'required' => '0',
      'name' => 'cal2_repeat_interval_c',
      'vname' => 'LBL_REPEAT_INTERVAL',
      'type' => 'int',
      'massupdate' => '0',
      'default' => NULL,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 1,
      'len' => '11',
      'disable_num_format' => NULL,
      'id' => 'Callscal2_repeat_interval_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_repeat_days_c'] = array (
      'required' => '0',
      'name' => 'cal2_repeat_days_c',
      'vname' => 'LBL_REPEAT_DAYS',
      'type' => 'varchar',
      'massupdate' => '0',
      'default' => NULL,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 1,
      'len' => '25',
      'id' => 'Callscal2_repeat_days_c',
      'custom_module' => 'Calls',
    );
$dictionary['Call']['fields']['cal2_repeat_end_date_c'] = array (
      'required' => '0',
      'name' => 'cal2_repeat_end_date_c',
      'vname' => 'LBL_REPEAT_END_DATE',
      'type' => 'date',
      'massupdate' => '0',
      'default' => NULL,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 1,
      'id' => 'Callscal2_repeat_end_date_c',
      'custom_module' => 'Calls',
    );
?>
