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
$dictionary['Resource'] = array ( 'table' => 'resources'
                                  , 'fields' => array (
  'name' => 
  array (
    'name' => 'name',
    'vname' => 'LBL_RES_NAME',
    'type' => 'varchar',
    'len' => '20',
  ),  
  'res_type' => 
  array (
    'name' => 'res_type',
    'vname' => 'LBL_RES_TYPE',
    'type' => 'enum',
    'options' => 'res_type_dom',
    'dbType' => 'varchar',
    'len' => '20',
  ),
  'department' => 
  array (
    'name' => 'department',
    'vname' => 'LBL_RES_DEPARTMENT',
    'type' => 'varchar',
    'len' => '20',
  ), 
  'location' => 
  array (
    'name' => 'location',
    'vname' => 'LBL_RES_LOCATION',
    'type' => 'varchar',
    'len' => '20',
  ), 
  'status' => 
  array (
    'name' => 'status',
    'vname' => 'LBL_RES_STATUS',
    'type' => 'enum',
    'options' => 'res_status_dom',
    'len' => '20',
  ), 
  'phone' => 
  array (
    'name' => 'phone',
    'vname' => 'LBL_RES_PHONE',
    'type' => 'varchar',
    'len' => '25',
  ), 
  'description' => 
  array (
    'name' => 'description',
    'vname' => 'LBL_RES_DESCRIPTION',
    'type' => 'text',
  ), 
  'meetings' => 
  array (
  	'name' => 'meetings',
    'type' => 'link',
    'relationship' => 'meetings_resources',
    'source'=>'non-db',
	'vname'=>'LBL_MEETINGS'
  ),
),     
 'relationships' => array (
  'resources_assigned_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Resources', 'rhs_table'=> 'resources', 'rhs_key' => 'assigned_user_id',
   'relationship_type'=>'one-to-many')

   ,'resources_modified_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Resources', 'rhs_table'=> 'resources', 'rhs_key' => 'modified_user_id',
   'relationship_type'=>'one-to-many')

   ,'resources_created_by' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'Resources', 'rhs_table'=> 'resources', 'rhs_key' => 'created_by',
   'relationship_type'=>'one-to-many'),
	),
'indices' => array (
       array('name' =>'idx_res_name', 'type' =>'index', 'fields'=>array('name')),
       array('name' =>'idx_res_type', 'type' =>'index', 'fields'=>array('res_type')),
),
//This enables optimistic locking for Saves From EditView
'optimistic_locking'=>true,
);

global $sugar_flavor;
if ($sugar_flavor == "PRO" || $sugar_flavor == "ENT") {
	VardefManager::createVardef('Resources','Resource', array('default', 'assignable',
	'team_security',
	));
} else {
	VardefManager::createVardef('Resources','Resource', array('default', 'assignable',
	));
}
?>
