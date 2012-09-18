<?php
function smarty_function_fmp_activitycustno_value($params, &$smarty)
{
    global $moduleList,$beanList,$beanFiles;

    if (!isset($params['parent_type'])) {
        return ;
    }

    if (strtolower($params['parent_type']) != 'accounts') {
        return ;
    }
    
    if (!isset($params['parent_id'])) {
        return ;
    }
    
    if (!$params['parent_id']) {
        return ;
    }
    
    $parent_id = $params['parent_id'];

    $class_name = $beanList['Accounts'];
    $class_file_path = $beanFiles[$class_name];
    require_once $class_file_path;
    $o = new $class_name();
    $o->retrieve($parent_id);

    return $o->custno_c;
}
