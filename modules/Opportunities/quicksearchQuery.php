<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

 require_once('include/SugarObjects/templates/person/Person.php');
 require_once('data/SugarBean.php'); 

/**
 * quicksearchQuery class, handles AJAX calls from quicksearch.js
 *
 * @copyright  2004-2007 SugarCRM Inc.
 * @license    http://www.sugarcrm.com/crm/products/sugar-professional-eula.html  SugarCRM Professional End User License
 * @since      Class available since Release 4.5.1
 */
class quicksearchQuery {
    /**
     * Internal function to construct where clauses
     */
    function constructWhere(&$query_obj, $focus) {
        $table = $focus->getTableName();
    	if (! empty($table)) {
            $table .= ".";
        }
        $cond_arr = array();
    
        if (! is_array($query_obj['conditions'])) {
            $query_obj['conditions'] = array();
        }
        
        foreach($query_obj['conditions'] as $condition) {
        	if($condition['op'] == 'contains') {
                array_push($cond_arr,$GLOBALS['db']->quote($table.$condition['name'])." like '%".$GLOBALS['db']->quote($condition['value'])."%'");
             }
             if($condition['op'] == 'like_custom') {
                $like = '';
                if(!empty($condition['begin'])) $like .= $GLOBALS['db']->quote($condition['begin']);
                $like .= $GLOBALS['db']->quote($condition['value']);
                if(!empty($condition['end'])) $like .= $GLOBALS['db']->quote($condition['end']);
               
                if ($focus instanceof Person)
                {
                	if ($condition['name'] == 'name') {
                    	array_push($cond_arr,db_concat(rtrim($table,'.'),array('first_name')) . " like '$like'");
                    	array_push($cond_arr,db_concat(rtrim($table,'.'),array('last_name')) . " like '$like'");
                    } else {
                    	array_push($cond_arr,db_concat(rtrim($table,'.'),array($condition['name'])) . " like '$like'");
                   	}
                }
                else 
                {
                	array_push($cond_arr,$GLOBALS['db']->quote($table.$condition['name'])." like '$like'");
                }
             } else { // starts_with
                array_push($cond_arr,$GLOBALS['db']->quote($table.$condition['name'])." like '".$GLOBALS['db']->quote($condition['value'])."%'");
             }
        }
        
        if($table == 'users.') {
            array_push($cond_arr,$table."status='Active'");
        }
        return implode(" {$query_obj['group']} ",$cond_arr)." |GROUP BY ".$table."name";
    }
    
    /**
     * Query a module for a list of items
     * 
     * @param array $args
     * example for querying Account module with 'a':
     * array ('modules' => array('Accounts'), // module to use
     *        'field_list' => array('name', 'id'), // fields to select
     *        'group' => 'or', // how the conditions should be combined
     *        'conditions' => array(array( // array of where conditions to use
     *                              'name' => 'name', // field 
     *                              'op' => 'like_custom', // operation
     *                              'end' => '%', // end of the query
     *                              'value' => 'a',  // query value
     *                              )
     *                        ),
     *        'order' => 'name', // order by
     *        'limit' => '30', // limit, number of records to return 
     *       )
     * @return array list of elements returned
     */
    function query($args) {
        
        $json = getJSONobj();
        global $sugar_config;
        global $beanFiles, $beanList;

        if($sugar_config['list_max_entries_per_page'] < ($args['limit'] + 1)) // override query limits
            $sugar_config['list_max_entries_per_page'] = ($args['limit'] + 1);
        
        $list_return = array();
        
        foreach($args['modules'] as $module) {
            require_once($beanFiles[$beanList[$module]]);
            $focus = new $beanList[$module];
            
            $query_orderby = '';
            if (!empty($args['order'])) {
                $query_orderby = $args['order'];
                if ($focus instanceof Person && $args['order'] == 'name') {
                	$query_orderby = 'last_name';
                }
            }
            $query_limit = '';
            $query_offset = 0;
            if (!empty($_POST['limit'])) {
                $query_limit = $_POST['limit'];
            }
            if (!empty($_POST['start'])) {
                $query_offset = $_POST['start'];
            }
            $query_where = $this->constructWhere($args, $focus);
            $list_arr = array();
            if($focus->ACLAccess('ListView', true)) {
                $curlist = $focus->get_list($query_orderby, $query_where, $query_offset, $query_limit, -1, 0);
                $list_return = array_merge($list_return,$curlist['list']);
            }
        }
        
        $app_list_strings = null;
        
        if($query_where != "" && strstr($query_where, '|GROUP BY')){
            $group = explode("|", $query_where); 
            $where = $group[0];
        }
        $total_query = "SELECT count(t.c) row_count from (SELECT count(*) c FROM opportunities 
            LEFT JOIN users
            ON opportunities.assigned_user_id=users.id 
            LEFT JOIN accounts_opportunities
            ON opportunities.id=accounts_opportunities.opportunity_id
            LEFT JOIN accounts
            ON accounts_opportunities.account_id=accounts.id  
            LEFT JOIN opportunities_cstm 
            ON opportunities.id = opportunities_cstm.id_c 

            where ($where) AND 
                            (accounts_opportunities.deleted is null OR accounts_opportunities.deleted=0)
                                    AND (accounts.deleted is null OR accounts.deleted=0)  
                                    AND opportunities.deleted=0 
            GROUP BY opportunities.name) t";
        $db = DBManagerFactory::getInstance();
        $result = $db->query($total_query, true,0);
	$total_list = $db->fetchByAssoc($result);
        $list_arr['totalCount']=$total_list['row_count'];
        $list_arr['fields']= array();
        for($i = 0; $i < count($list_return); $i++) {
            $list_arr['fields'][$i]= array();
            $list_arr['fields'][$i]['module']= $list_return[$i]->object_name;
            
            foreach($args['field_list'] as $field) {
                
                // handle enums
                if( (isset($list_return[$i]->field_name_map[$field]['type']) && $list_return[$i]->field_name_map[$field]['type'] == 'enum') || 
                    (isset($list_return[$i]->field_name_map[$field]['custom_type']) && $list_return[$i]->field_name_map[$field]['custom_type'] == 'enum')) {
                    
                    // get fields to match enum vals
                    if(empty($app_list_strings)) {
                        if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') $current_language = $_SESSION['authenticated_user_language'];
                        else $current_language = $sugar_config['default_language'];
                        $app_list_strings = return_app_list_strings_language($current_language);
                    }
                    
                    // match enum vals to text vals in language pack for return
                    if(!empty($app_list_strings[$list_return[$i]->field_name_map[$field]['options']])) {
                        $list_return[$i]->$field = $app_list_strings[$list_return[$i]->field_name_map[$field]['options']][$list_return[$i]->$field];
                    }
                }
                //Match name field for People
             	if ($list_return[$i] instanceof Person) {
                	$list_return[$i]->_create_proper_name_field();
                }
                
                $list_arr['fields'][$i][$field] = str_replace("&#039;", "'", $list_return[$i]->$field);
            }
        }
     
        return $json->encode($list_arr);
    }
    
    /**
     * get_contacts_array
     * 
     */
    function get_contacts_array($args) {
        $json = getJSONobj();
        global $sugar_config, $beanFiles, $beanList, $locale;
        
        if($sugar_config['list_max_entries_per_page'] < ($args['limit'] + 1)) // override query limits
            $sugar_config['list_max_entries_per_page'] = ($args['limit'] + 1);
        
        $list_return = array();
        
        foreach($args['modules'] as $module) {
            require_once($beanFiles[$beanList[$module]]);
            $focus = new $beanList[$module];
            
            $query_orderby = '';
            if (!empty($args['order'])) {
                $query_orderby = $args['order'];
            }
            $query_limit = '';
            if (!empty($args['limit'])) {
                $query_limit = $args['limit'];
            }
            $query_where = $this->constructWhere($args, $focus);
            $list_arr = array();
            if($focus->ACLAccess('ListView', true)) {
                $curlist = $focus->get_list($query_orderby, $query_where, 0, $query_limit, -1, 0);
                $list_return = array_merge($list_return,$curlist['list']);
            }
        }
        $list_arr['totalCount']=count($list_return);
        $list_arr['fields']= array();
        for($i = 0; $i < count($list_return); $i++) {
            $list_arr['fields'][$i]= array();
            $list_arr['fields'][$i]['module']= $list_return[$i]->object_name;
            $contactName = "";
            foreach($args['field_list'] as $field) {
                // We are overriding the contact_id param and the reports_to_id param to change to 'id'
                if(preg_match('/reports_to_id$/s',$field) || preg_match('/contact_id$/s',$field)) {  // We are overriding the reports_to_id param to change to 'id'
                    $list_arr['fields'][$i][$field] = $list_return[$i]->id;
                }
                else {
                    $list_arr['fields'][$i][$field] = $list_return[$i]->$field;
                }
            } //foreach
            
            $contactName = $locale->getLocaleFormattedName($list_arr['fields'][$i]['first_name'], 
                                                           $list_arr['fields'][$i]['last_name'],
                                                           $list_arr['fields'][$i]['salutation']);
                                                         
            $list_arr['fields'][$i][$args['field_list'][0]] = $contactName;
        } //for
       
        $str = $json->encode($list_arr); 
        return $str;    
    }
    
    /**
     * Returns the list of users, faster than using query method for Users module
     * 
     * @param array $args arguments used to construct query, see query() for example
     * 
     * @return array list of users returned
     */
    function get_user_array($args) {
        global $json;
        $json = getJSONobj();

        $response = array();
        
        if(showFullName()) { // utils.php, if system is configured to show full name
            $user_array = getUserArrayFromFullName($args['conditions'][0]['value']);
        } else {
            $user_array = get_user_array(false, "Active", '', false, $args['conditions'][0]['value']);
        }
        $response['totalCount']=count($user_array);
        $response['fields']=array();
        $i=0;
        foreach($user_array as $id=>$name) {
            array_push($response['fields'], array('id' => $id, 'user_name' => $name, 'module' => 'Users'));
            $i++;
        }
    
        return $json->encode($response);
    }
}

$json = getJSONobj();
$data = $json->decode(html_entity_decode($_REQUEST['data']));
if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){
    foreach($data['conditions'] as $k=>$v){
        $data['conditions'][$k]['value']=$_REQUEST['query'];
    }
}
$quicksearchQuery = new quicksearchQuery();
 
switch($data['method']) {
    case 'query':
        echo $quicksearchQuery->query($data);
        break;
    case 'get_user_array':
        echo $quicksearchQuery->get_user_array($data);
        break;
    case 'get_contact_array':
        echo $quicksearchQuery->get_contacts_array($data);
        break;
}

?>
