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
require_once('modules/Calendar2/Calendar2.php');
require_once('modules/Resources/Resource.php');
require_once('modules/vCals/HTTP_WebDAV_Server_vCal.php');

    
/**
 * Filesystem access using WebDAV
 *
 * @access public
 */
class HTTP_WebDAV_Server_vCal_cal2 extends HTTP_WebDAV_Server_vCal
{

    function HTTP_WebDAV_Server_vCal_cal2()
    {
       $this->vcal_focus = new vCal2();
       $this->user_focus = new User();
    }


    /**
     * Serve a webdav request
     *
     * @access public
     * @param  string  
     */
    function ServeRequest($base = false) 
    {

       global $sugar_config,$current_language;

        if(empty($sugar_config) && isset($dbconfig['db_host_name']))
        {
          make_sugar_config($sugar_config);
        }

        if (!empty($sugar_config['session_dir'])) 
        {
           session_save_path($sugar_config['session_dir']);
        }

        session_start();

        // clean_incoming_data();


        $current_language = $sugar_config['default_language'];

        // special treatment for litmus compliance test
        // reply on its identifier header
        // not needed for the test itself but eases debugging
/*
        foreach(apache_request_headers() as $key => $value) {
            if(stristr($key,"litmus")) {
                error_log("Litmus test $value");
                header("X-Litmus-reply: ".$value);
            }
        }
*/

        // set root directory, defaults to webserver document root if not set
        if ($base) { 
            $this->base = realpath($base); // TODO throw if not a directory
            } else if(!$this->base) {
            $this->base = $_SERVER['DOCUMENT_ROOT'];
        }


        $query_arr =  array();
        $isUser = true;
         // set path
        if ( empty($_SERVER["PATH_INFO"]))
        {
			$this->path = "/";
			if(strtolower($_SERVER["REQUEST_METHOD"]) == 'get'){
				$query_arr = $_REQUEST;
			}else{
				parse_str($_REQUEST['parms'],$query_arr);
			}
        } else{
          $this->path = $this->_urldecode( $_SERVER["PATH_INFO"]);

          if(ini_get("magic_quotes_gpc")) {
           $this->path = stripslashes($this->path);
          }

          $query_str = preg_replace('/^\//','',$this->path);
          $query_arr =  array();
          parse_str($query_str,$query_arr);
        }


        if ( ! empty($query_arr['type']))
        {
          $this->vcal_type = $query_arr['type'];
        }
        else {
          $this->vcal_type = 'vfb';
        }

        if ( ! empty($query_arr['source']))
        {
          $this->source = $query_arr['source'];
        }
        else {
          $this->source = 'outlook';
        }

        if ( ! empty($query_arr['key']))
        {
          $this->publish_key = $query_arr['key'];
        }

        // select user by email
        if ( ! empty($query_arr['email']))
        {
        	
        
          // clean the string!
          $query_arr['email'] = clean_string($query_arr['email']);
          //get user info
          $this->user_focus->retrieve_by_email_address( $query_arr['email']);

        }
        // else select user by user_name
        else if ( ! empty($query_arr['user_name']))
        {
          // clean the string!
          $query_arr['user_name'] = clean_string($query_arr['user_name']);

          //get user info
          $arr = array('user_name'=>$query_arr['user_name']);
          $this->user_focus->retrieve_by_string_fields($arr);
        }
        // else select user by user id
        else if ( ! empty($query_arr['user_id']))
        {
            if (!$this->user_focus->retrieve($query_arr['user_id'])) {
            	$isUser = false;
            	$GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 user_id ='.$query_arr['user_id']);
           		unset($this->user_focus);
           		$this->user_focus = new Resource();
            	$GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 user_focus_object ='.$this->user_focus->object_name);
            	$this->user_focus->disable_row_level_security = true;
           		$this->user_focus->retrieve($query_arr['user_id']);
           	}
        }
        $GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 user_focus_id ='.$this->user_focus->id);

        // if we haven't found a user, then return 404
        if ( empty($this->user_focus->id) || $this->user_focus->id == -1)
        {
            $this->http_status("404 Not Found");
            return;
        }

//            if(empty($this->user_focus->user_preferences))
//            {
                 if ($isUser) $this->user_focus->loadPreferences();
//            }
            
        // let the base class do all the work
        //parent::ServeRequest();
        
        //Copy from HTTP_WebDAV_Server::ServeRequest()
        
        // identify ourselves
        if (empty($this->dav_powered_by)) {
            header("X-Dav-Powered-By: PHP class: ".get_class($this));
        } else {
            header("X-Dav-Powered-By: ".$this->dav_powered_by );
        }

        // check authentication
        if (!$this->_check_auth()) {
            $this->http_status('401 Unauthorized');
        	$GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 401 Unauthorized');

            // RFC2518 says we must use Digest instead of Basic
            // but Microsoft Clients do not support Digest
            // and we don't support NTLM and Kerberos
            // so we are stuck with Basic here
            header('WWW-Authenticate: Basic realm="'.($this->http_auth_realm).'"');

            return;
        }

        // check
        if(! $this->_check_if_header_conditions()) {
        	$GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 412 Precondition failed');
            $this->http_status("412 Precondition failed");
            return;
        }

        // set path
        $this->path = $this->_urldecode(!empty($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/");
        if(ini_get("magic_quotes_gpc")) {
            $this->path = stripslashes($this->path);
        }
        $GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 path ='.$this->path);


        // detect requested method names
        $method = strtolower($_SERVER["REQUEST_METHOD"]);
        $wrapper = "http_".$method;

        // activate HEAD emulation by GET if no HEAD method found
        if ($method == "head" && !method_exists($this, "head")) {
            $method = "get";
        }
        $GLOBALS['log']->debug('HTTP_WebDAV_Server_vCal_cal2 wrapper ='.$wrapper);

        if (method_exists($this, $wrapper) && ($method == "options" || method_exists($this, $method))) {
			switch($method) {
			case "get":
            	$this->http_get();
            	break;
            case "post":
            	$this->http_post();
            	break;
            case "options":
            	$this->http_options();
            	break;
            case "profind":
            	$this->http_profind();
            	break;
            case "proppatch":
            	$this->http_proppatch();
            	break;
            case "mkcol":
            	$this->http_mkcol();
            	break;
            case "head":
            	$this->http_head();
            	break;
            case "put":
            	$this->http_put();
            	break;
            case "delete":
            	$this->http_delete();
            	break;
            case "copy":
            	$this->http_copy();
            	break;
            case "move":
            	$this->http_move();
            	break;
            default:
            	break;
            }
            //$this->$wrapper();  // call method by name
        } else { // method not found/implemented
            if ($_SERVER["REQUEST_METHOD"] == "LOCK") {
                $this->http_status("412 Precondition failed");
            } else {
                $this->http_status("405 Method not allowed");
                header("Allow: ".join(", ", $this->_allow()));  // tell client what's allowed
            }
        }

        
     }

}
?>
