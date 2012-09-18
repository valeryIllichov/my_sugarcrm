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
$dictionary['calls_resources'] = array (
	'table' => 'calls_resources',
	'fields' => array (
		array(	'name'			=>'id',
				'type'			=>'varchar',
				'len'			=>'36'
		),
		array(	'name'			=>'call_id',
				'type'			=>'varchar',
				'len'			=>'36',
		),
		array(	'name'			=>'resource_id',
				'type'			=>'varchar',
				'len'			=>'36',
		),
		array(	'name'			=> 'required', 
				'type'			=> 'varchar', 
				'len'			=> '1', 
				'default'		=> '1',
		),
		array(	'name'			=> 'accept_status', 
				'type'			=> 'varchar', 
				'len'			=> '25', 
				'default'		=> 'none'
		),
		array(	'name'			=> 'date_modified',
				'type'			=> 'datetime',
		),
		array(	'name'			=>'deleted',
				'type'			=>'bool',
				'len'			=>'1',
				'default'		=>'0',
				'required'=>true
		),
	),
	'indices' => array (
		array(	'name'			=>'calls_resspk',
				'type'			=>'primary',
				'fields'		=>array('id'),
		),
		array(	'name'			=>'idx_cal_res_cal',
				'type'			=>'index',
				'fields'		=>array('call_id'),
		),
		array(	'name'			=>'idx_cal_res_res',
				'type'			=>'index',
				'fields'		=>array('resource_id'),
		),
		array(	'name'			=>'idx_call_resources',
				'type'			=>'alternate_key',
				'fields'=>array('call_id','resource_id'),
		),
	),
	'relationships' => array(
		'calls_resources' => array(
			'lhs_module'		=> 'Calls',
			'lhs_table'			=> 'calls',
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Resources',
			'rhs_table'			=> 'resources',
			'rhs_key'			=> 'id',
			'relationship_type'	=> 'many-to-many',
			'join_table'		=> 'calls_resources',
			'join_key_lhs'		=> 'call_id',
			'join_key_rhs'=>'resource_id',
		),
	),
);
?>
