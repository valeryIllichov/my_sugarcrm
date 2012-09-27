<?
/* Sales reporting logic for 5-21 screen */
class FMPSales {
	
	const sessionSavePath = '/var/lib/php5';
	const crmurl='http://sugarcrm.loc/';
	const dbhost='localhost';
	const dbname='sugarcrm1604';
	const dbuser='root';
	const dbpass='123';
	const tblPrefix = 'dsls';
	/*const sessionSavePath = '/var/SugarCRM/session';
	const crmurl='https://crm.fmpco.com/';
	const dbhost='sugardb';
	const dbname='sugarcrm520f';
	const dbuser='sugaradmin';
	const dbpass='crmpass123';
	const tblPrefix = 'dsls';*/
	
	static function sugarDBConnect() {
		$dbcon = mysql_connect ( self::dbhost, self::dbuser, self::dbpass );
		if ($dbcon === false) {
			die ( "Connection failed: " . mysql_error () );
		}
		if (mysql_select_db ( self::dbname, $dbcon ) === false) {
			die ( "Could not select database: " . mysql_error () );
		}
	}
	
	static function initialize521() {
		session_save_path(self::sessionSavePath);
		session_start();
		if (! isset ( $_SESSION ['authenticated_user_id'] )) {
			die("Not logged in as a sugar user");
		}
		
		$_SESSION ['fmp_regions_locations'] = self::getRegionLocationRollup(); /* required to build fmp_acl */
		$_SESSION ['fmp_slsm'] = self::getSlsmRollup(); /* complete slsm tree */
		$_SESSION ['fmp_userid'] = $_SESSION ['authenticated_user_id'];
		$_SESSION ['fmp_acl'] = self::build521ACL ( $_SESSION ['fmp_userid'] );
	}
	
	/* build sets of region, location, slsm and dealertype this user has access to */
	static function build521ACL($userid) {
		//$userGroupList = self::getUserSecurityGroupMembership ( $userid );
		
		$acl = array ();
		$user_slsm = self::getUserTwoSlsmRollup($userid);
		$user_area = self::getUserAreaRollup($userid, false); /* only include full Region and Location accesses */
		$user_dt = self::getUserDealerTypeAccess($userid);
		$user_cg = self::getUserCustomerGroups($userid);
		
		foreach ($user_slsm as $slsm) {
			$slsm_below_array = self::getSlsmBelow($_SESSION['fmp_slsm'], $slsm['slsm']); /* result includes original slsmno */
			foreach($slsm_below_array as $subslsm) {
				$acl [] = array ('region' => NULL, 'location' => NULL, 'slsm' => $subslsm, 'dealertype' => NULL, 'custid' => NULL );
			}
                        ///$acl [] = array ('region' => NULL, 'location' => NULL, 'slsm' => $slsm['slsm'], 'dealertype' => NULL, 'custid' => NULL );
		}
	
		foreach ($user_area as $area) {
			if($area['type'] == 'location') {
				$acl [] = array ('region' => NULL, 'location' => $area['number'], 'slsm' => NULL, 'dealertype' => NULL, 'custid' => NULL );
			} elseif($area['type'] == 'region') {
				$acl [] = array ('region' => $area['number'], 'location' => NULL, 'slsm' => NULL, 'dealertype' => NULL, 'custid' => NULL );
				/* shouldn't need this as we can just select by region */
				/*foreach($area['locations'] as $locArray) {  
					$acl [] = array('region' => NULL, 'location' => $locArray['number'], 'slsm' => NULL, 'dealertype' => NULL, 'custid' => NULL );
				}*/
			}
		}
		
		foreach($user_dt as $dtarray) {  /*'dealertype'=>'dealertypeno','area'=>'location|region', 'number'=>'area/regionnumber' */
			$region=null;
			$location=null;
			if($dtarray['area'] == 'location') {
				$location=$dtarray['number'];
			}
			if($dtarray['area'] == 'region') {
				$region=$dtarray['number'];
			}
			if($location !== null or $region !== null) {
				$acl[] = array('region' => $region, 'location' => $location, 'slsm' => NULL, 'dealertype' => $dtarray['dealertype'], 'custid' => NULL );
			}
		}

		foreach($user_cg as $cgarray) { /* 'CustomerARGroup_xxx' => array(custid1, custid2, custid3...) */
			foreach($cgarray as $custid) {
				$acl[] = array('region' => NULL, 'location' => NULL, 'slsm' => NULL, 'dealertype' => NULL, 'custid' => $custid);
			}
		}
		return $acl;	
	}
	
	/*returns an array of array('dealertype'=>'dealertypeno','area'=>'location|region', 'number'=>'area/regionnumber'_) */
	static function getUserDealerTypeAccess($userid) {
		
		self::sugarDBConnect ();
		
		$sql = "select sg.name from users u
				inner join securitygroups_users sgu on u.id = sgu.user_id
				inner join securitygroups sg on sgu.securitygroup_id = sg.id
				where u.id = '$userid' and sgu.deleted = 0 and sg.deleted = 0 and sg.name like 'DealerType_%'
				order by sg.name";
		
		if (($results = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		$groupList = array ();
		$dt_regions_found = array();
		$dt_locations_found = array();
		while ( ($row = mysql_fetch_assoc ( $results )) !== false ) {
			$s = explode('_', $row['name']);  /* DealerType_#_#Name_Region|Location_#_abbrv */
			$dealertype = $s[1];
			$area_name = strtolower($s[3]);
			$area_num = strval(intval($s[4]));
			
			switch($area_name) {
				case 'region':
					if(!isset($dt_regions_found[$dealertype])) {
						$dt_regions_found[$dealertype] = array();
					}
					$dt_regions_found[$dealertype][] = $area_num;
					break;
				case 'location':
					if(!isset($dt_locations_found[$dealertype])) {
						$dt_locations_found[$dealertype] = array();
					}
					$dt_locations_found[$dealertype][] = $area_num;
					break;
			}
		}
		
		$fmp_region_location=$_SESSION['fmp_regions_locations'];

		/* for each dt=>region, make sure all regions' locations are in $dt_locations_found */
		/* on second thought, don't need this -- selecting sales with region = 'x' will find what we need */
		/*foreach($dt_regions_found as $dealertype => $region_array) {
			if(!isset($dt_locations_found[$dealertype])) {
				$dt_locations_found[$dealertype] = array();
			}
			foreach($region_array as $region) {
				foreach($fmp_region_location[$region]['locations'] as $location) {
					$locnum = $location['loc'];
					if(!isset($dt_locations_found[$dealertype][$locnum])) {
						$dt_locations_found[$dealertype][] = $locnum;
					}
				}
			}
		}*/
		
		/* finally, create the group list */
		
		foreach($dt_regions_found as $dealertype => $region_array) {
			foreach($region_array as $region) {
				$groupList [] = array('dealertype' => $dealertype, 'area' => 'region', 'number' => $region);
			}
		}
		
		foreach($dt_locations_found as $dealertype => $location_array) {
			foreach($location_array as $location) {
				$groupList [] = array('dealertype' => $dealertype, 'area' => 'location', 'number' => $location);
			}
		}
		
		return $groupList;
	}
	
	static function getUserSecurityGroupMembership($userid) {
		
		self::sugarDBConnect ();
		
		$sql = "select sg.name from users u
				inner join securitygroups_users sgu on u.id = sgu.user_id
				inner join securitygroups sg on sgu.securitygroup_id = sg.id
				where u.id = '$userid' and sgu.deleted = 0 and sg.deleted = 0
				order by sg.name";
		
		if (($results = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		$groupList = array ();
		while ( ($row = mysql_fetch_assoc ( $results )) !== false ) {
			$groupList [] = $row ['name'];
		}
		
		return $groupList;
	}
	
	/* return array('CustomerGroupName1' => array(custid1, custid2, custid3, ...),
	 * 				'CustomerGroupName2' => ...,
	 * );
	 * 
	 */
	static function getUserCustomerGroups($userid) {
		$results = array();
		self::sugarDBConnect();
		/* could do this in one query, but found it is really slow.  If we don't have to look at securitygroups_records
		 * (which we shouldn't for 90+% of users), let's not. */
		$sql = "select sg.id as id, sg.name as name from securitygroups sg 
				inner join securitygroups_users sgu
				on sg.id = sgu.securitygroup_id and sg.deleted = 0
				where sg.name like 'CustomerARGroup%'
				and sgu.deleted = 0
				and sgu.user_id = '".mysql_real_escape_string($userid)."'";
		if (($groupid_results = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		while(($group_row = mysql_fetch_assoc($groupid_results)) !== false) {
			$sql = "select a.custid_c 
				from securitygroups_records sgr 
				inner join accounts a on sgr.record_id = a.id and sgr.deleted = 0 
				and sgr.module = 'Accounts'
				where a.deleted = 0 and sgr.securitygroup_id = '".mysql_real_escape_string($group_row['id'])."'";
		
			if (($custid_results = mysql_query ( $sql )) === false) {
				echo $sql;
				die ( "Error querying database: " . mysql_error () );
			}
			$results[$group_row['name']] = array();
			while ( ($row = mysql_fetch_assoc ( $custid_results )) !== false ) {
				if($row['custid_c']!==NULL and $row['custid_c']!=='') {
					$results[$group_row['name']][] = $row['custid_c'];
				}			
			}
		}
		
		return $results;
	}
	
	static function getRegionLocationRollup($region = null) {
		
		self::sugarDBConnect ();
		
		$sql = "SELECT r.region, r.rgnname, l.loc, l.city FROM " . self::tblPrefix . "_regions r 
		        INNER JOIN " . self::tblPrefix . "_locations l
		        ON r.region = l.region ORDER BY r.region, l.loc";
		
		if (($results = mysql_query ( $sql )) === false) {
			die ( "Error querying database: " . mysql_error () );
		}
		
		$curRegion = '';
		$resultArray = array ();
		while ( ($row = mysql_fetch_assoc ( $results )) !== false ) {
			if ($curRegion == '' or $row ['region'] != $curRegion) {
				$curRegion = $row ['region'];
				$resultArray [$curRegion] = array ('region' => $row ['region'], 'regionname' => $row ['rgnname'] );
			}
			$resultArray [$curRegion] ['locations'] [] = array ('loc' => $row ['loc'], 'locname' => $row ['city'] );
		}
		return $resultArray;
	}
	
	static function getUserAreaRollup($userid, $includeDealerTypeGroups=true) {
		$areaRollup = self::getRegionLocationRollup ();
		
		self::SugarDBConnect ();
		
		$sql = "select sg.name from users u
				inner join securitygroups_users sgu on u.id = sgu.user_id
				inner join securitygroups sg on sgu.securitygroup_id = sg.id
				where u.id = '$userid' and sgu.deleted = 0 and sg.deleted = 0 and (sg.name like 'L0%' or sg.name like 'Region%'";


				
		if($includeDealerTypeGroups) {
			$sql .= " or sg.name like 'DealerType%'";
		}
		$sql .= ") order by sg.name";
		
		if (($results = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		/* populate $regionArray and $locArray with what to show based on various groups */
		$locArray = array ();
		$regionArray = array ();
		
		while ( ($row = mysql_fetch_assoc ( $results )) !== false ) {
			switch (substr ( $row ['name'], 0, 2 )) {
				case 'L0' :
					$value = intval ( substr ( $row ['name'], 2, 2 ) );
					if(!in_array($value, $locArray)) {
						$locArray [] = $value;
					}
					break;
				case 'Re' :
					$value = intval ( substr ( $row ['name'], 6, 2 ) );
					if(!in_array($value, $regionArray)) {
						$regionArray [] = $value;
					}
					break;
				case 'De' : /* DealerType_x_DTName_[Region|Location]_x_CityName */
					$exprow=explode('_',$row['name']);
					$value=intval($exprow[4]);
					switch($exprow[3]) {
						case 'Location':
							if(!in_array($value, $locArray)) {
								$locArray[] = $value;
							}
							break;
						case 'Region':
							if(!in_array($value, $regionArray)) {
								$regionArray [] = $value;
							}
							break;
					}
					break;
						
			}
		}
		
		$userAreaRollup = array ();
		foreach ( $regionArray as $region ) {
			foreach ( $areaRollup as $r ) {
				if ($region == $r ['region']) {
					$newregion = array ('type' => 'region', 'number' => $r ['region'], 'name' => $r ['regionname'] );
					foreach ( $r ['locations'] as $loc ) {
						$newregion ['locations'] [] = array ('type' => 'location', 'number' => $loc ['loc'], 'name' => $loc ['locname'] );
                        $i=array_search($loc['loc'], $locArray); /* remove locations covered by a region from the location list */
                        if($i!==FALSE) {
							unset($locArray[$i]);
                        }
					}
					$userAreaRollup [] = $newregion;
				}
			}
		}
		
		foreach ( $locArray as $locNo ) {
			foreach ( $areaRollup as $r ) {
				foreach ( $r ['locations'] as $loc ) {
					if ($locNo == $loc ['loc']) {
						$userAreaRollup [] = array ('type' => 'location', 'number' => $loc ['loc'], 'name' => $loc ['locname'] );
					}
				}
			}
		}
		
		return $userAreaRollup;
	
	}
	
	/*returns array of all slsm security groups this user has access to */
	function getUserSlsmRollup($userid) {
		
		$slsmRollup = self::getSlsmRollup ();
		
		self::sugarDBConnect ();
		
		$sql = "select sg.name from users u
				inner join securitygroups_users sgu on u.id = sgu.user_id
				inner join securitygroups sg on sgu.securitygroup_id = sg.id
				where u.id = '$userid' and sgu.deleted = 0 and sg.deleted = 0 and sg.name like 'SLSM_%'
				order by sg.name";
		if (($results = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		while ( ($row = mysql_fetch_assoc ( $results )) !== false ) {
			$slsmList [] = substr ( $row ['name'], 5 );
		}
		
		/* Traverse the tree to find slsm they can see -- could be more than one */
		$userSlsmRollup = array ();
		foreach ( $slsmRollup as &$slsmRollupBranch ) {
			
			$slsmArray = self::getUserSlsmRollupRecur ( $slsmList, $slsmRollupBranch );
			if ($slsmArray != null) {
				foreach ( $slsmArray as $slsm ) {
					$userSlsmRollup [] = $slsm;
				}
			}
		}
		
		return $userSlsmRollup;
	}
        
	 function getUserTwoSlsmRollup($user_id){
            self::sugarDBConnect ();
            $two_slsmRollup = self::getTwoSlsmRollup();
            $FMP_slsmRollup =  $two_slsmRollup['FMP'];
            $Splash_slsmRollup = $two_slsmRollup['Splash'];
            //pr($FMP_slsmRollup);

            $q_fmp = ''
                . 'SELECT '
                    . 'x_sg.name '
                . 'FROM users AS x_u '
                    . 'INNER JOIN securitygroups_users AS x_sgu ON x_u.id = x_sgu.user_id '
                    . 'INNER JOIN securitygroups AS x_sg ON x_sgu.securitygroup_id = x_sg.id '
                . 'WHERE x_u.id = \'' . $user_id . '\' '
                    . ' AND x_sgu.deleted = 0 '
                    . 'AND x_sg.deleted = 0 '
                    . 'AND x_sg.name LIKE \'SLSM_%\' '
                . 'ORDER BY x_sg.name';


            if (($rs_fmp = mysql_query ( $q_fmp )) === false) {
                    echo $q_fmp;
                    die ( "Error querying database: " . mysql_error () );
            }
            while($row = mysql_fetch_assoc ($rs_fmp)) {
                $FMP_slsmList[]=substr($row['name'],5);
            }        
            //pr($FMP_slsmList);

            $userSlsmRollup=array();
            foreach($FMP_slsmRollup as &$slsmRollupBranch) {

                $slsmArray=self::getUserSlsmRollupRecur($FMP_slsmList, $slsmRollupBranch);
                if($slsmArray != null) {
                    foreach($slsmArray as $slsm) {
                        $userSlsmRollup[] = $slsm;
                    }
                }
            }

        // pr($userSlsmRollup);

            $q_splash = ''
                . 'SELECT '
                    . 'x_sg.name '
                . 'FROM users AS x_u '
                    . 'INNER JOIN securitygroups_users AS x_sgu ON x_u.id = x_sgu.user_id '
                    . 'INNER JOIN securitygroups AS x_sg ON x_sgu.securitygroup_id = x_sg.id '
                . 'WHERE x_u.id = \'' . $user_id . '\' '
                    . ' AND x_sgu.deleted = 0 '
                    . 'AND x_sg.deleted = 0 '
                    . 'AND x_sg.name  LIKE \'Splash_Slsm_%\' '
                . 'ORDER BY x_sg.name';
            
            if (($rs_splash = mysql_query ( $q_splash )) === false) {
                    echo $q_splash;
                    die ( "Error querying database: " . mysql_error () );
            }
            while($row = mysql_fetch_assoc ($rs_splash)) {
                $Splash_slsmList[]=substr($row['name'],12);
            }

            foreach($Splash_slsmRollup as &$slsmRollupBranch) {

                $slsmArray=self::getUserSlsmRollupRecur($Splash_slsmList, $slsmRollupBranch);
                if($slsmArray != null) {
                    foreach($slsmArray as $slsm) {
                            $userSlsmRollup[] = $slsm;
                    }
                }
            }

            //pr($userSlsmRollup);
            return $userSlsmRollup;
        }
        
        function getTwoSlsmRollup(){
            self::sugarDBConnect ();
                    $q = ''
                . 'SELECT '
                    . ' company, slsm, dsls_slsm_combined.name, manager_company, manager_slsm, x_u.id AS user_id, x_u.user_name AS username '
                . ' FROM dsls_slsm_combined '
                    . ' LEFT JOIN users AS x_u ON (dsls_slsm_combined.empid = x_u.empid '
                    . '  AND dsls_slsm_combined.empid <> \' \') '
                . ' ORDER BY slsm'
                ;

            if (($rs = mysql_query ( $q )) === false) {
                    echo $q;
                    die ( "Error querying database: " . mysql_error () );
            }
            $FMP_slsmByMgr = array();
            $Splash_slsmByMgr = array();
            while($row = mysql_fetch_assoc ($rs)) {
            if ($row['manager_slsm'] == '') $row['manager_slsm'] = '0';
            if($row['company'] == 'FMP') $FMP_slsmByMgr[$row['manager_slsm']][] = $row;
            if($row['company'] == 'Splash') $Splash_slsmByMgr[$row['manager_slsm']][] = $row;
            }

            $FMP_slsmTree = array();
            foreach($FMP_slsmByMgr['0'] as $parentSlsm) {
                $children=self::getSlsmChildren($parentSlsm['slsm'], $FMP_slsmByMgr, 0);
                if(count($children) > 0) {
                    $parentSlsm['children']=$children;
                }
                $FMP_slsmTree[]=$parentSlsm;
            }
            $Splash_slsmTree = array();
            foreach($Splash_slsmByMgr['0'] as $parentSlsm) {
                $children=self::getSlsmChildren($parentSlsm['slsm'], $Splash_slsmByMgr, 0);
                if(count($children) > 0) {
                    $parentSlsm['children']=$children;
                }
                $Splash_slsmTree[]=$parentSlsm;
            }

            return array(
                'FMP' => $FMP_slsmTree,
                'Splash' => $Splash_slsmTree
            );


        }

	/* return slsm and all below if match is found in slsmList.  
	     Otherwise, try children or return null if no children 
	*/
	function getUserSlsmRollupRecur($slsmList, $slsmRollupBranch) {
		if (count ( $slsmList ) == 0) {
			return null;
		}
		
		foreach ( $slsmList as &$slsmNo ) {
			if ($slsmRollupBranch ['slsm'] == $slsmNo) { /* match -- return it and all slsm under */
				return array ($slsmRollupBranch );
			}
		}
		
		if (array_key_exists ( 'children', $slsmRollupBranch ) && $slsmRollupBranch ['children'] != '') { /* not found, so try to find in children */
			$userSlsmRollup = null;
			foreach ( $slsmRollupBranch ['children'] as &$child ) {
				$slsmArray = self::getUserSlsmRollupRecur ( $slsmList, $child );
				if ($slsmArray != null) {
					foreach ( $slsmArray as $slsm ) {
						$userSlsmRollup [] = $slsm;
					}
				}
			}
			return $userSlsmRollup;
		} else {
			return null;
		}
	}
	
	function getSlsmRollup() {
		self::sugarDBConnect ();
		
		$sql = "select slsm, firstname, lastname, mgr from " . self::tblPrefix . "_mgrgroups order by slsm";
		
		if (($result = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		$slsmByMgr = array ();
		while ( ($row = mysql_fetch_assoc ( $result )) !== false ) {
			$slsmByMgr [$row ['mgr']] [] = $row;
		}
		
		$slsmTree = array ();
		foreach ( $slsmByMgr ['0'] as $parentSlsm ) {
			$children = self::getSlsmChildren ( $parentSlsm ['slsm'], $slsmByMgr, 0 );
			if (count ( $children ) > 0) {
				$parentSlsm ['children'] = $children;
			}
			$slsmTree [] = $parentSlsm;
		}
		return $slsmTree;
	}
	
	private function getSlsmChildren($slsmno, &$slsmByMgr) {
		$slsmTree = '';
		if (key_exists ( $slsmno, $slsmByMgr )) {
			$slsmTree = $slsmByMgr [$slsmno];
		}
		if ($slsmTree != '') {
			foreach ( $slsmTree as &$slsm ) {
				$slsm ['children'] = self::getSlsmChildren ( $slsm ['slsm'], $slsmByMgr );
			}
		}
		return $slsmTree;
	}
	
	/* uses slsm rollup built by getSlsmRollup to return an array of slsmno including and beneath $slsmno 
	 * typical call: getSlsmBelow($_SESSION['fmp_slsm'], '100'); */
	private function getSlsmBelow($slsmtree, $slsmno, $matchfoundinparent=false) {
		$result = array();
		foreach($slsmtree as $slsm) {
			if($slsm['slsm'] === $slsmno or $matchfoundinparent) { /* found above me or we have a match, so return all below */
				$result[] = $slsm['slsm'];
				if($slsm['children']!=='') {
					$result = array_merge($result, self::getSlsmBelow($slsm['children'], $slsmno, true));
				}
			} else {
				if($slsm['children']!=='') {
					$result = array_merge($result, self::getSlsmBelow($slsm['children'], $slsmno, false));
				}
			}			
		}
		return $result;
	}
	
	function getARTotals($selectMethod, $location, $region, $slsm, $dealerType) {
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		$qryCorporate = "SELECT Sum(ds.MTD_PROJECTED) AS MTD_PROJECTED, 
			Sum(ds.MTD_SALES) AS MTD_SALES,
			Sum(ds.MTD_GP) AS MTD_GP,
			Sum(ds.MTD_BUDGET_SALES) AS MTD_BUDGET_SALES,
			Sum(ds.MTD_BUDGET_GP) AS MTD_BUDGET_GP,
			Sum(ds.MLY_SLS) AS MLY_SLS,
			Sum(ds.PENDING_ORDERS) AS PENDING_ORDERS,
			Sum(ds.PENDING_CREDITS) AS PENDING_CREDITS,
			Sum(ds.TODAYS_ORDERS) AS TODAYS_ORDERS,
			Sum(ds.TODAYS_CREDITS) AS TODAYS_CREDITS,
			Sum(ds.MTD_SLS_NOEM) AS MTD_SLS_NOEM,
			Sum(ds.MTD_GP_NOEM) AS MTD_GP_NOEM,
			Sum(ds.MTD_BUDGET_NOEM_SALES) AS MTD_BUDGET_NOEM_SALES,
			Sum(ds.MTD_PROJECTED_NOEM) AS MTD_PROJECTED_NOEM,
			Sum(ds.LY_SLS_NOEM) AS LY_SLS_NOEM,
			Sum(ds.LY_GP_NOEM) AS LY_GP_NOEM,
			Sum(ds.YTD_PROJECTED) AS YTD_PROJECTED,
			Sum(ds.YTD_SALES) AS YTD_SALES,
			Sum(ds.YTD_GP) AS YTD_GP,
			Sum(ds.YTD_BUDGET_SALES) AS YTD_BUDGET_SALES,
			Sum(ds.YTD_BUDGET_GP) AS YTD_BUDGET_GP,
			Sum(ds.LYTD_SALES) AS LYTD_SALES,
			Sum(ds.LY_SALES) AS LY_SALES,
			Sum(ds.LY_GP) AS LY_GP,
			Sum(ds.YTD_SLS_NOEM) AS YTD_SLS_NOEM,
			Sum(ds.YTD_GP_NOEM) AS YTD_GP_NOEM,
			Sum(ds.YTD_BUDGET_NOEM_SALES) AS YTD_BUDGET_NOEM_SALES,
			Sum(ds.YTD_PROJECTED_NOEM) AS YTD_PROJECTED_NOEM
			FROM " . self::tblPrefix . "_dailysales ds
			INNER JOIN accounts a ON ds.CUSTID = a.custid_c
			WHERE (a.deleted = 0 and ((a.slsm_c) Not In (20,232)) AND ((a.custtype_c) Not In ('AFFL','TRAV')) %s);";
		
		$qryDealerType = "SELECT
			 case substring(a.dealertype_c from 1 for 1)
			    when '0' then 'eTailor'
				when '1' then 'Ford Dealers'
				when '2' then 'GM Dealers'
				when '3' then 'Other Dealers'
				when '4' then 'Asian / European'
				when '5' then 'Retailers'
				when '6' then 'Jobber Whse'
				when '7' then 'Installers'
				when '8' then 'Fleet'
				else 'Cash / COD / Other'
				end as DT,
			Sum(ds.MTD_PROJECTED) AS MTD_PROJECTED,
			Sum(ds.MTD_SALES) AS MTD_SALES,
			Sum(ds.MTD_GP) AS MTD_GP,
			Sum(ds.MTD_BUDGET_SALES) AS MTD_BUDGET_SALES,
			Sum(ds.MTD_BUDGET_GP) AS MTD_BUDGET_GP,
			Sum(ds.MLY_SLS) AS MLY_SLS,
			Sum(ds.PENDING_ORDERS) AS PENDING_ORDERS,
			Sum(ds.PENDING_CREDITS) AS PENDING_CREDITS,
			Sum(ds.TODAYS_ORDERS) AS TODAYS_ORDERS,
			Sum(ds.TODAYS_CREDITS) AS TODAYS_CREDITS,
			Sum(ds.MTD_SLS_NOEM) AS MTD_SLS_NOEM,
			Sum(ds.MTD_GP_NOEM) AS MTD_GP_NOEM,
			Sum(ds.MTD_BUDGET_NOEM_SALES) AS MTD_BUDGET_NOEM_SALES,
			Sum(ds.MTD_PROJECTED_NOEM) AS MTD_PROJECTED_NOEM,
			Sum(ds.LY_SLS_NOEM) AS LY_SLS_NOEM,
			Sum(ds.LY_GP_NOEM) AS LY_GP_NOEM,
			Sum(ds.YTD_PROJECTED) AS YTD_PROJECTED,
			Sum(ds.YTD_SALES) AS YTD_SALES,
			Sum(ds.YTD_GP) AS YTD_GP,
			Sum(ds.YTD_BUDGET_SALES) AS YTD_BUDGET_SALES,
			Sum(ds.YTD_BUDGET_GP) AS YTD_BUDGET_GP,
			Sum(ds.LYTD_SALES) AS LYTD_SALES,
			Sum(ds.LY_SALES) AS LY_SALES,
			Sum(ds.LY_GP) AS LY_GP,
			Sum(ds.YTD_SLS_NOEM) AS YTD_SLS_NOEM,
			Sum(ds.YTD_GP_NOEM) AS YTD_GP_NOEM,
			Sum(ds.YTD_BUDGET_NOEM_SALES) AS YTD_BUDGET_NOEM_SALES,
			Sum(ds.YTD_PROJECTED_NOEM) AS YTD_PROJECTED_NOEM
			FROM " . self::tblPrefix . "_dailysales ds
			INNER JOIN accounts a ON ds.custid = a.custid_c
			WHERE (a.deleted = 0 and ((a.slsm_c) Not In (20,232)) AND ((a.custtype_c) Not In (\"AFFL\",\"TRAV\")) %s) 
			GROUP BY DT
			HAVING DT = '%s';";
		
		$sql = $qryCorporate;
		$extraWhere = "";
		
		/*		if(!is_null($dealerType)) {
			$sql = $qryDealerType;
			$sql = sprintf($sql, '%s', $dealerType);*/
		/* put dealertype into HAVING clause */
		//}
		

		if (! is_null ( $location )) {
			if (! is_array ( $location )) {
				$extraWhere .= " $primaryOp ds.loc = $location";
			}
		}
		
		if (! is_null ( $region )) {
			$extraWhere .= " $primaryOp ds.region = $region";
		}
		
		if (! is_null ( $slsm )) {
			if (! is_array ( $slsm )) {
				$extraWhere .= " $primaryOp a.slsm_c = '$slsm'";
			} else {
				$extraWhere .= " $primaryOp a.slsm_c IN (";
				$bFirstSlsm = true;
				foreach ( $slsm as $slsmno ) {
					if ($bFirstSlsm) {
						$bFirstSlsm = false;
					} else {
						$extraWhere .= ",";
					}
					$extraWhere .= "'$slsmno'";
				}
				$extraWhere .= ")";
			}
		}
		$extraWhere .= ")";
		
		/* always AND these.  otherwise, everyone could see a dealertype corporate wide */
		if (! is_null ( $dealerType )) {
			if (! is_array ( $dealerType )) {
				$extraWhere .= " AND a.dealertype_c = '$dealerType'";
			} else {
				$extraWhere .= " AND a.dealertype_c IN (";
				$bFirstDealerType = true;
				foreach ( $dealerType as $dealerTypeNo ) {
					if ($bFirstDealerType) {
						$bFirstDealerType = false;
					} else {
						$extraWhere .= ",";
					}
					$extraWhere .= "'$dealerTypeNo'";
				}
				$extraWhere .= ")";
			}
		}
		
		$sql = sprintf ( $sql, $extraWhere );
		
		if (($result = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		$returnData = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$returnData [] = $row;
		}
		return $returnData;
	
	}
	
	static function getSalesTotals($selectMethod, $location, $region, $slsm, $dealerType, $accountIDs = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		
		self::sugarDBConnect ();
		
		$qryCorporate = "SELECT Sum(ds.MTD_PROJECTED) AS MTD_PROJECTED, 
			Sum(ds.MTD_SALES) AS MTD_SALES,
			Sum(ds.MTD_GP) AS MTD_GP,
			Sum(ds.MTD_BUDGET_SALES) AS MTD_BUDGET_SALES,
			Sum(ds.MTD_BUDGET_GP) AS MTD_BUDGET_GP,
			Sum(ds.MLY_SLS) AS MLY_SLS,
			Sum(ds.PENDING_ORDERS) AS PENDING_ORDERS,
			Sum(ds.PENDING_CREDITS) AS PENDING_CREDITS,
			Sum(ds.TODAYS_ORDERS) AS TODAYS_ORDERS,
			Sum(ds.TODAYS_CREDITS) AS TODAYS_CREDITS,
			Sum(ds.MTD_SLS_NOEM) AS MTD_SLS_NOEM,
			Sum(ds.MTD_GP_NOEM) AS MTD_GP_NOEM,
			Sum(ds.MTD_BUDGET_NOEM_SALES) AS MTD_BUDGET_NOEM_SALES,
			Sum(ds.MTD_PROJECTED_NOEM) AS MTD_PROJECTED_NOEM,
			Sum(ds.LY_SLS_NOEM) AS LY_SLS_NOEM,
			Sum(ds.LY_GP_NOEM) AS LY_GP_NOEM,
			Sum(ds.YTD_PROJECTED) AS YTD_PROJECTED,
			Sum(ds.YTD_SALES) AS YTD_SALES,
			Sum(ds.YTD_GP) AS YTD_GP,
			Sum(ds.YTD_BUDGET_SALES) AS YTD_BUDGET_SALES,
			Sum(ds.YTD_BUDGET_GP) AS YTD_BUDGET_GP,
			Sum(ds.LYTD_SALES) AS LYTD_SALES,
			Sum(ds.LY_SALES) AS LY_SALES,
			Sum(ds.LY_GP) AS LY_GP,
			Sum(ds.YTD_SLS_NOEM) AS YTD_SLS_NOEM,
			Sum(ds.YTD_GP_NOEM) AS YTD_GP_NOEM,
			Sum(ds.YTD_BUDGET_NOEM_SALES) AS YTD_BUDGET_NOEM_SALES,
			Sum(ds.YTD_PROJECTED_NOEM) AS YTD_PROJECTED_NOEM,
			Sum(ds.MTD_PROJECTED_UNDERCAR) as MTD_PROJECTED_UNDERCAR,
			Sum(ds.MTD_BUDGET_UNDERCAR_SALES) as MTD_BUDGET_UNDERCAR_SALES,
			Sum(ds.MTD_BUDGET_UNDERCAR_GP) as MTD_BUDGET_UNDERCAR_GP,
			Sum(ds.YTD_PROJECTED_UNDERCAR) as YTD_PROJECTED_UNDERCAR,
			Sum(ds.YTD_BUDGET_UNDERCAR_SALES) AS YTD_BUDGET_UNDERCAR_SALES,
			Sum(ds.MTD_SLS_UNDERCAR) AS MTD_SLS_UNDERCAR,
			Sum(ds.MTD_GP_UNDERCAR) as MTD_GP_UNDERCAR,
			Sum(ds.YTD_SLS_UNDERCAR) as YTD_SLS_UNDERCAR,
			Sum(ds.YTD_GP_UNDERCAR) as YTD_GP_UNDERCAR,
			Sum(ds.LY_SLS_UNDERCAR) as LY_SLS_UNDERCAR,
			Sum(ds.LY_GP_UNDERCAR) as LY_GP_UNDERCAR
			FROM " . self::tblPrefix . "_dailysales ds
			INNER JOIN accounts a ON ds.CUSTID = a.custid_c
			%s;";
		
	
		$sql = $qryCorporate;

		$sql = sprintf ( $sql, self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod) );
                                    
		if (($result = mysql_query ( $sql )) === false) {
			echo $sql;
			die ( "Error querying database: " . mysql_error () );
		}
		
		$returnData = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$returnData [] = $row;
		}
		return $returnData;
	
	}
                  
	/* Customer AR
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to AR from this location
	* $region -- restrict to AR from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	* returns multirow array:
	* totalRecords => total rows available
	*  data => array of selected rows: custno, custname, mtd_sales, mtd_gp, ytd_sales, ytd_gp, ly_sales, ly_gp from startIndex for maxRecords
	*/
	function getCustomerAR($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType,$accountIDs = array(), $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,
                                a.avg_days_c AS avg_days,
                                a.termscode_c AS termscode,
                                a.creditcode_c AS creditcode,
                                a.creditlimit_c AS creditlimit,
                                if(coalesce(a.aarbal_c,0) = 0, 0, round(100*a.aarbal_c / a.creditlimit_c,2)) as aarbal_to_creditlimit,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			(select name from accounts sa
				where sa.custno_c = a.custno_c
				order by sa.custid_c limit 1) as custname,   
			a.arfuture_c as future,
			a.arcurrent_c as current,
			a.ar30_60_c as ar30_60,
			a.ar60_90_c as ar60_90,
			a.over_90_c as over_90,
			a.aarbal_c as aarbal 
			from accounts a
                                                      left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by a.slsm_c, slsmname, custno, custname ";
		
		$sqlcount = "SELECT count(distinct a.custno_c) 
			from accounts a 
                                                      left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
		
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
                
		$sql = sprintf ( $sql, $sqlWhere );
		
		$sqlcount = sprintf($sqlcount, $sqlWhere);
		
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                    // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                        
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}

		if (! ($result = mysql_query ( $sql ))) {
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	}

                
	/* Customer sales
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	* $specialType -- restrict to either noem or undercar sales
	* returns multirow array:
	* totalRecords => total rows available
	*  data => array of selected rows: custno, custname, mtd_sales, mtd_gp, ytd_sales, ytd_gp, ly_sales, ly_gp from startIndex for maxRecords
	*/
	function getCustomerSales($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType, $specialType,$accountIDs, $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,";
		
		$specialType = strtolower ( $specialType );
		switch ($specialType) {
			case "nonoe" :
				$sql .= " coalesce(sum(ds.mtd_sls_noem),0) as mtd_sales,
					coalesce(sum(ds.mtd_gp_noem),0) as mtd_gp,
					if(sum(ds.mtd_sls_noem) = 0, null, round(100*sum(ds.mtd_gp_noem) / sum(ds.mtd_sls_noem),2)) as mtd_gpp,
					coalesce(sum(ds.ytd_sls_noem),0) as ytd_sales,
					coalesce(sum(ds.ytd_gp_noem),0) as ytd_gp,
					if(sum(ds.ytd_sls_noem) = 0, null, round(100*sum(ds.ytd_gp_noem) / sum(ds.ytd_sls_noem),2)) as ytd_gpp,
					coalesce(sum(ds.ly_sls_noem),0) as ly_sales,
					coalesce(sum(ds.ly_gp_noem),0) as ly_gp,
					if(sum(ds.ly_sls_noem) = 0, null, round(100*sum(ds.ly_gp_noem) / sum(ds.ly_sls_noem),2)) as ly_gpp,
                                        coalesce(sum(ds.lm_sales),0) as lm_sales,
coalesce(sum(ds.lm_gp),0) as lm_gp,
if(coalesce(sum(ds.lm_sales),0) = 0, 0, round(100*coalesce(sum(ds.lm_gp),0) / sum(ds.lm_sales),2)) as lm_gpp,

coalesce(sum(ds.lytm_sales),0) as lytm_sales,
coalesce(sum(ds.lytm_gp),0) as lytm_gp,
if(coalesce(sum(ds.lytm_sales),0) = 0, 0, round(100*sum(ds.lytm_gp) / sum(ds.lytm_sales),2)) as lytm_gpp,

coalesce(sum(ds.lytd_sales),0) as lytd_sales,
coalesce(sum(ds.lytd_gp),0) as lytd_gp,
if(coalesce(sum(ds.lytd_sales),0) = 0, 0, round(100*sum(ds.lytd_gp) / sum(ds.lytd_sales),2)) as lytd_gpp";
				break;
			case "undercar" :
				$sql .= " coalesce(sum(ds.mtd_sls_undercar),0) as mtd_sales,
					coalesce(sum(ds.mtd_gp_undercar),0) as mtd_gp,
					if(sum(ds.mtd_sls_undercar) = 0, null, round(100*sum(ds.mtd_gp_undercar) / sum(ds.mtd_sls_undercar),2)) as mtd_gpp,
					coalesce(sum(ds.ytd_sls_undercar),0) as ytd_sales,
					coalesce(sum(ds.ytd_gp_undercar),0) as ytd_gp,
					if(sum(ds.ytd_sls_undercar) = 0, null, round(100*sum(ds.ytd_gp_undercar) / sum(ds.ytd_sls_undercar),2)) as ytd_gpp,
					coalesce(sum(ds.ly_sls_undercar),0) as ly_sales,
					coalesce(sum(ds.ly_gp_undercar),0) as ly_gp,
					if(sum(ds.ly_sls_undercar) = 0, null, round(100*sum(ds.ly_gp_undercar) / sum(ds.ly_sls_undercar),2)) as ly_gpp,
                                        coalesce(sum(ds.lm_sales),0) as lm_sales,
coalesce(sum(ds.lm_gp),0) as lm_gp,
if(coalesce(sum(ds.lm_sales),0) = 0, 0, round(100*coalesce(sum(ds.lm_gp),0) / sum(ds.lm_sales),2)) as lm_gpp,

coalesce(sum(ds.lytm_sales),0) as lytm_sales,
coalesce(sum(ds.lytm_gp),0) as lytm_gp,
if(coalesce(sum(ds.lytm_sales),0) = 0, 0, round(100*sum(ds.lytm_gp) / sum(ds.lytm_sales),2)) as lytm_gpp,

coalesce(sum(ds.lytd_sales),0) as lytd_sales,
coalesce(sum(ds.lytd_gp),0) as lytd_gp,
if(coalesce(sum(ds.lytd_sales),0) = 0, 0, round(100*sum(ds.lytd_gp) / sum(ds.lytd_sales),2)) as lytd_gpp";
				break;
			
			default :
				$sql .= " coalesce(sum(ds.mtd_sales),0) as mtd_sales,
					coalesce(sum(ds.mtd_gp),0) as mtd_gp,
					if(sum(ds.mtd_sales) = 0, null, round(100*sum(ds.mtd_gp) / sum(ds.mtd_sales),2)) as mtd_gpp,
					coalesce(sum(ds.ytd_sales),0) as ytd_sales,
					coalesce(sum(ds.ytd_gp),0) as ytd_gp,
					if(sum(ds.ytd_sales) = 0, null, round(100*sum(ds.ytd_gp) / sum(ds.ytd_sales),2)) as ytd_gpp,
					coalesce(sum(ds.ly_sales),0) as ly_sales,
					coalesce(sum(ds.ly_gp),0) as ly_gp,
					if(sum(ds.ly_sales) = 0, null, round(100*sum(ds.ly_gp) / sum(ds.ly_sales),2)) as ly_gpp,
                                        coalesce(sum(ds.lm_sales),0) as lm_sales,
coalesce(sum(ds.lm_gp),0) as lm_gp,
if(coalesce(sum(ds.lm_sales),0) = 0, 0, round(100*coalesce(sum(ds.lm_gp),0) / sum(ds.lm_sales),2)) as lm_gpp,

coalesce(sum(ds.lytm_sales),0) as lytm_sales,
coalesce(sum(ds.lytm_gp),0) as lytm_gp,
if(coalesce(sum(ds.lytm_sales),0) = 0, 0, round(100*sum(ds.lytm_gp) / sum(ds.lytm_sales),2)) as lytm_gpp,

coalesce(sum(ds.lytd_sales),0) as lytd_sales,
coalesce(sum(ds.lytd_gp),0) as lytd_gp,
if(coalesce(sum(ds.lytd_sales),0) = 0, 0, round(100*sum(ds.lytd_gp) / sum(ds.lytd_sales),2)) as lytd_gpp";
		}
		
		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
                
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);
                
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                    // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                    
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}
		
		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	}
	
	/* Customer sales budget
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	* returns multirow array:
	* totalRecords => total rows available
	*  data => array of selected rows: custno, custname, 
	*  mtd_sales, mtd_budget_sales, mtd_sales_budgetp, mtd_gp, mtd_budget_gp, mtd_budget_gpp
	*  ytd_sales, ytd_budget_sales, ytd_budget_salesp,
	*  ytd_gp, ytd_budget_gp, ytd_budget_gpp
	*  from startIndex for maxRecords
	*/
	function getCustomerSalesBudget($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,";
		
		$sql .= " coalesce(sum(ds.mtd_sales),0) as mtd_sales,
					coalesce(sum(ds.mtd_budget_sales),0) as mtd_budget_sales,
					if(sum(ds.mtd_budget_sales) = 0, null, round(100*sum(ds.mtd_sales) / sum(ds.mtd_budget_sales),2)) as mtd_budget_salesp,
					coalesce(sum(ds.mtd_gp),0) as mtd_gp,
					coalesce(sum(ds.mtd_budget_gp),0) as mtd_budget_gp,
					if(sum(ds.mtd_budget_gp) = 0, null, round(100*sum(ds.mtd_gp) / sum(ds.mtd_budget_gp),2)) as mtd_budget_gpp,
					coalesce(sum(ds.ytd_sales),0) as ytd_sales,
					coalesce(sum(ds.ytd_budget_sales),0) as ytd_budget_sales,
					if(sum(ds.ytd_budget_sales) = 0, null, round(100*sum(ds.ytd_sales) / sum(ds.ytd_budget_sales),2)) as ytd_budget_salesp,
					coalesce(sum(ds.ytd_gp),0) as ytd_gp,
					coalesce(sum(ds.ytd_budget_gp),0) as ytd_budget_gp,
					if(sum(ds.ytd_budget_gp) = 0, null, round(100*sum(ds.ytd_gp) / sum(ds.ytd_budget_gp),2)) as ytd_budget_gpp";
		
		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm WHERE (a.deleted = 0) and (%s) group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm WHERE (a.deleted = 0) and (%s)";
		
		$sqlWhere = "(";
		
		/*not used anymore */
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}

		/* generate where criteria with everything in acl -- including custid */
		$acl_array = $_SESSION['fmp_acl'];
		$bNeedOp = false;
		foreach($acl_array as $acl) {
			$bNeedSubOp = false;
			if($bNeedOp) {
				$sqlWhere .= " OR ";
			}
			$sqlWhere .= "(";
			if(!is_null($acl['location'])) {
				$sqlWhere .= "ds.loc = ".$acl['location'];
				$bNeedSubOp = true;
			}
			if(!is_null($acl['region'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= "ds.region = ".$acl['region'];
				$bNeedSubOp = true;
			}
			if(!is_null($acl['slsm'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= sprintf("a.slsm_c = '%s'",$acl['slsm']);  /* select off accounts table to include those without ds data*/
				$bNeedSubOp = true;
			}
			if(!is_null($acl['dealertype'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= sprintf("a.dealertype_c = '%s'",$acl['dealertype']);
				$bNeedSubOp = true;
			}
			//if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
			if(!is_null($acl['custid'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= sprintf("ds.custid = %d",$acl['custid']);
				$bNeedSubOp = true;
			}
			//}
			$sqlWhere .= ")";
			$bNeedOp = true;
		}
		if(!$bNeedOp) { $sqlWhere .= "0"; } /* select nothing */ 

		$sqlWhere .= ") AND (";
		
		/* now add on criteria of selection */
		$bNeedOp = false;
		if (! is_null ( $location )) {
			$bNeedOp = true;
			if (! is_array ( $location )) {
				$sqlWhere .= " ds.loc = '$location'";
			} else {
				$sqlWhere .= " ds.loc IN(";
				$bFirstLoc = true;
				foreach ( $location as $locno ) {
					if ($bFirstLoc) {
						$bFirstLoc = false;
					} else {
						$sqlWhere .= ",";
					}
					$sqlWhere .= "'$locno'";
				}
				$sqlWhere .= ")";
			}
		}
		
		if (! is_null ( $region )) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			if (! is_array ( $region )) {
				$sqlWhere .= " ds.region = '$region'";
			} else {
				$sqlWhere .= " ds.region IN (";
				$bFirstReg = true;
				foreach ( $region as $regno ) {
					if ($bFirstReg) {
						$bFirstReg = false;
					} else {
						$sqlWhere .= ",";
					}
					$sqlWhere .= "'$regno'";
				}
				$sqlWhere .= ")";
			}
		}
		
		if (! is_null ( $slsm )) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			if (! is_array ( $slsm )) {
				$sqlWhere .= " a.slsm_c = '$slsm'";
			} else {
				$sqlWhere .= " a.slsm_c IN (";
				$bFirstSlsm = true;
				foreach ( $slsm as $slsmno ) {
					if ($bFirstSlsm) {
						$bFirstSlsm = false;
					} else {
						$sqlWhere .= ",";
					}
					$sqlWhere .= "'$slsmno'";
				}
				$sqlWhere .= ")";
			}
		}
		
		if (! is_null ( $dealerType )) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			if (! is_array ( $dealerType )) {
				$sqlWhere .= " a.dealertype_c = '$dealerType'";
			} else {
				$sqlWhere .= " a.dealertype_c IN (";
				$bFirstDT = true;
				foreach ( $dealerType as $dt ) {
					if ($bFirstDT) {
						$bFirstDT = false;
					} else {
						$sqlWhere .= ",";
					}
					$sqlWhere .= "'$dt'";
				}
				$sqlWhere .= ")";
			}
		}
		
		if(!$bNeedOp) { $sqlWhere .= "1"; } /* no criteria so provide everything already added in ACL */
		$sqlWhere .= ")";
		
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);

		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}
		
		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	}
	
	/* Customer sales budget
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	* returns multirow array:
	* totalRecords => total rows available
	*  data => array of selected rows: custno, custname, 
	*  mtd_vs_budget_sales, mtd_vs_budget_gp, mtd_vs_budget_gp_percent,
	*  ytd_vs_budget_sales, ytd_vs_budget_gp, ytd_vs_budget_gp_percent,
	*  projected_vs_budget_sales, projected_vs_budget_gp, projected_vs_budget_gp_percent,
	*  from startIndex for maxRecords
	*/
	function getCustomerBudgetComparison($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType,$accountIDs, $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,";
		
		$sql .= "coalesce(sum(ds.mtd_sales),0) - coalesce(sum(ds.mtd_budget_sales),0) as mtd_vs_budget_sales,
coalesce(sum(ds.mtd_gp),0) - coalesce(sum(ds.mtd_budget_gp),0) as mtd_vs_budget_gp,
if(coalesce(sum(ds.mtd_sales),0) = 0, 0, round(100*sum(ds.mtd_gp) / sum(ds.mtd_sales),2)) - 
if(coalesce(sum(ds.mtd_budget_sales),0) = 0, 0, round(100*sum(ds.mtd_budget_gp) / sum(ds.mtd_budget_sales),2)) as mtd_vs_budget_gp_percent,

coalesce(sum(ds.mtd_projected),0) - coalesce(sum(ds.mtd_budget_sales),0) as cm_proj_vs_budget_sales,
coalesce(sum(ds.mtd_projected_gp),0) - coalesce(sum(ds.mtd_budget_gp),0) as cm_proj_vs_budget_gp,
if(coalesce(sum(ds.mtd_projected),0) = 0, 0, round(100*sum(ds.mtd_projected_gp) / sum(ds.mtd_projected),2)) - 
if(coalesce(sum(ds.mtd_budget_sales),0) = 0, 0, round(100*sum(ds.mtd_budget_gp) / sum(ds.mtd_budget_sales),2)) as cm_proj_vs_budget_gp_percent,

coalesce(sum(ds.ytd_sales),0) - coalesce(sum(ds.ytd_budget_sales),0) as ytd_vs_budget_sales,
coalesce(sum(ds.ytd_gp),0) - coalesce(sum(ds.ytd_budget_gp),0) as ytd_vs_budget_gp,
if(coalesce(sum(ds.ytd_sales),0) = 0, 0, round(100*sum(ds.ytd_gp) / sum(ds.ytd_sales),2)) - 
if(coalesce(sum(ds.ytd_budget_sales),0) = 0, 0, round(100*sum(ds.ytd_budget_gp) / sum(ds.ytd_budget_sales),2)) as ytd_vs_budget_gp_percent,

coalesce(sum(ds.ytd_projected),0) - coalesce(sum(ds.ytd_budget_sales),0) as projected_vs_budget_sales,
coalesce(sum(ds.ytd_projected_gp),0) - coalesce(sum(ds.ytd_budget_gp),0) as projected_vs_budget_gp,
if(coalesce(sum(ds.ytd_projected),0) = 0, 0, round(100*sum(ds.ytd_projected_gp) / sum(ds.ytd_projected),2)) - 
if(coalesce(sum(ds.ytd_budget_sales),0) = 0, 0, round(100*sum(ds.ytd_budget_gp) / sum(ds.ytd_budget_sales),2)) as projected_vs_budget_gp_percent";
		
		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
		
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
                
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);
		
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                     // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                    
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}

		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	}
	
	/* Customer sales budget
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	* returns multirow array:
	* totalRecords => total rows available
	*  data => array of selected rows: custno, custname, 
	*  mtd_vs_lm_sales, mtd_vs_lm_gp, mtd_vs_lm_gp_percent,
	*  mtd_vs_lytm_sales, mtd_vs_lytm_gp, mtd_vs_lytm_gp_percent,
	*  mtd_vs_budget_sales, mtd_vs_budget_gp, mtd_vs_budget_gp_percent,
	*  ytd_vs_budget_sales, ytd_vs_budget_gp, ytd_vs_budget_gp_percent,
	*  ytd_vs_lytd_sales, ytd_vs_lytd_gp, ytd_vs_lytd_gp_percent,
	*  projected_vs_budget_sales, projected_vs_budget_gp, projected_vs_budget_gp_percent,
	*  projected_vs_ly_sales, projected_vs_ly_gp, proejected_vs_ly_gp_percent
	*  from startIndex for maxRecords
	*/
	function getCustomerSalesComparison($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType,$accountIDs, $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,";
		
		$sql .= "coalesce(sum(ds.mtd_projected),0) - coalesce(sum(ds.lm_sales),0) as mtd_projected_vs_lm_sales,
coalesce(sum(ds.mtd_projected_gp),0) - coalesce(sum(ds.lm_gp),0) as mtd_projected_vs_lm_gp,
if(coalesce(sum(ds.mtd_projected),0) = 0, 0, round(100*sum(ds.mtd_projected_gp) / sum(ds.mtd_projected),2)) - 
if(coalesce(sum(ds.lm_sales),0) = 0, 0, round(100*coalesce(sum(ds.lm_gp),0) / sum(ds.lm_sales),2)) as mtd_projected_vs_lm_gp_percent,

coalesce(sum(ds.mtd_projected),0) - coalesce(sum(ds.lytm_sales),0) as mtd_projected_vs_lytm_sales,
coalesce(sum(ds.mtd_projected_gp),0) - coalesce(sum(ds.lytm_gp),0) as mtd_projected_vs_lytm_gp,
if(coalesce(sum(ds.mtd_projected),0) = 0, 0, round(100*sum(ds.mtd_projected_gp) / sum(ds.mtd_projected),2)) - 
if(coalesce(sum(ds.lytm_sales),0) = 0, 0, round(100*sum(ds.lytm_gp) / sum(ds.lytm_sales),2)) as mtd_projected_vs_lytm_gp_percent,

coalesce(sum(ds.mtd_sales),0) - coalesce(sum(ds.lm_sales),0) as mtd_vs_lm_sales,
coalesce(sum(ds.mtd_gp),0) - coalesce(sum(ds.lm_gp),0) as mtd_vs_lm_gp,
if(coalesce(sum(ds.mtd_sales),0) = 0, 0, round(100*sum(ds.mtd_gp) / sum(ds.mtd_sales),2)) - 
if(coalesce(sum(ds.lm_sales),0) = 0, 0, round(100*coalesce(sum(ds.lm_gp),0) / sum(ds.lm_sales),2)) as mtd_vs_lm_gp_percent,

coalesce(sum(ds.mtd_sales),0) - coalesce(sum(ds.lytm_sales),0) as mtd_vs_lytm_sales,
coalesce(sum(ds.mtd_gp),0) - coalesce(sum(ds.lytm_gp),0) as mtd_vs_lytm_gp,
if(coalesce(sum(ds.mtd_sales),0) = 0, 0, round(100*sum(ds.mtd_gp) / sum(ds.mtd_sales),2)) - 
if(coalesce(sum(ds.lytm_sales),0) = 0, 0, round(100*sum(ds.lytm_gp) / sum(ds.lytm_sales),2)) as mtd_vs_lytm_gp_percent,

coalesce(sum(ds.ytd_sales),0) - coalesce(sum(ds.lytd_sales),0) as ytd_vs_lytd_sales,
coalesce(sum(ds.ytd_gp),0) - coalesce(sum(ds.lytd_gp),0) as ytd_vs_lytd_gp,
if(coalesce(sum(ds.ytd_sales),0) = 0, 0, round(100*sum(ds.ytd_gp) / sum(ds.ytd_sales),2)) - 
if(coalesce(sum(ds.ly_sales),0) = 0, 0, round(100*sum(ds.ly_gp) / sum(ds.ly_sales),2)) as ytd_vs_lytd_gp_percent,

coalesce(sum(ds.ytd_projected),0) - coalesce(sum(ds.ly_sales),0) as projected_vs_ly_sales,
coalesce(sum(ds.ytd_projected_gp),0) - coalesce(sum(ds.ly_gp),0) as projected_vs_ly_gp,
if(coalesce(sum(ds.ytd_projected),0) = 0, 0, round(100*sum(ds.ytd_projected_gp) / sum(ds.ytd_projected),2)) - 
if(coalesce(sum(ds.ly_sales),0) = 0, 0, round(100*sum(ds.ly_gp) / sum(ds.ly_sales),2)) as projected_vs_ly_gp_percent";
		
		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
		
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
                
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);
		
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                     // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                    
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}
		
		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	
	}
        
   /* Customer Returns
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	*/
	function getCustomerReturns($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType,$accountIDs, $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,";
		
		$sql .= "coalesce(sum(ds.RTN_NEW_MTD_Total),0) as rtn_new_mtd,
if(coalesce(sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),0) = 0, 0, round(100*sum(ds.RTN_NEW_MTD_Total) / sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),2)) as rtn_new_mtd_precent,
coalesce(sum(ds.RTN_NEW_YTD_Total),0) as rtn_new_ytd,
if(coalesce(sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),0) = 0, 0, round(100*sum(ds.RTN_NEW_YTD_Total) / sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),2)) as rtn_new_ytd_precent,
coalesce(sum(ds.RTN_NEW_LY_MTD_Total),0) as rtn_new_lymtd,
if(coalesce(sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),0) = 0, 0, round(100*sum(ds.RTN_NEW_LY_MTD_Total) / sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),2)) as rtn_new_lymtd_precent,
coalesce(sum(ds.RTN_NEW_LY_YTD_Total),0) as rtn_new_lyytd,
if(coalesce(sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),0) = 0, 0, round(100*sum(ds.RTN_NEW_LY_YTD_Total) / sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),2)) as rtn_new_lyytd_precent,
coalesce(sum(ds.RTN_NEW_LM_Total),0) as rtn_new_lm,
if(coalesce(sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),0) = 0, 0, round(100*sum(ds.RTN_NEW_LM_Total) / sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),2)) as rtn_new_lm_precent,
coalesce(sum(ds.RTN_NEW_LY_LM_Total),0) as rtn_new_lylm,
if(coalesce(sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),0) = 0, 0, round(100*sum(ds.RTN_NEW_LY_LM_Total) / sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),2)) as rtn_new_lylm_precent,

coalesce(sum(ds.RTN_DEF_MTD_Total),0) as rtn_def_mtd,
if(coalesce(sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),0) = 0, 0, round(100*sum(ds.RTN_DEF_MTD_Total) / sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),2)) as rtn_def_mtd_precent,
coalesce(sum(ds.RTN_DEF_YTD_Total),0) as rtn_def_ytd,
if(coalesce(sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),0) = 0, 0, round(100*sum(ds.RTN_DEF_YTD_Total) / sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),2)) as rtn_def_ytd_precent,
coalesce(sum(ds.RTN_DEF_LY_MTD_Total),0) as rtn_def_lymtd,
if(coalesce(sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),0) = 0, 0, round(100*sum(ds.RTN_DEF_LY_MTD_Total) / sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),2)) as rtn_def_lymtd_precent,
coalesce(sum(ds.RTN_DEF_LY_YTD_Total),0) as rtn_def_lyytd,
if(coalesce(sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),0) = 0, 0, round(100*sum(ds.RTN_DEF_LY_YTD_Total) / sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),2)) as rtn_def_lyytd_precent,
coalesce(sum(ds.RTN_DEF_LM_Total),0) as rtn_def_lm,
if(coalesce(sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),0) = 0, 0, round(100*sum(ds.RTN_DEF_LM_Total) / sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),2)) as rtn_def_lm_precent,
coalesce(sum(ds.RTN_DEF_LY_LM_Total),0) as rtn_def_lylm,
if(coalesce(sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),0) = 0, 0, round(100*sum(ds.RTN_DEF_LY_LM_Total) / sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),2)) as rtn_def_lylm_precent,

coalesce(sum(ds.RTN_COR_MTD_Total),0) as rtn_cor_mtd,
if(coalesce(sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),0) = 0, 0, round(100*sum(ds.RTN_COR_MTD_Total) / sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),2)) as rtn_cor_mtd_precent,
coalesce(sum(ds.RTN_COR_YTD_Total),0) as rtn_cor_ytd,
if(coalesce(sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),0) = 0, 0, round(100*sum(ds.RTN_COR_YTD_Total) / sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),2)) as rtn_cor_ytd_precent,
coalesce(sum(ds.RTN_COR_LY_MTD_Total),0) as rtn_cor_lymtd,
if(coalesce(sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),0) = 0, 0, round(100*sum(ds.RTN_COR_LY_MTD_Total) / sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),2)) as rtn_cor_lymtd_precent,
coalesce(sum(ds.RTN_COR_LY_YTD_Total),0) as rtn_cor_lyytd,
if(coalesce(sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),0) = 0, 0, round(100*sum(ds.RTN_COR_LY_YTD_Total) / sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),2)) as rtn_cor_lyytd_precent,
coalesce(sum(ds.RTN_COR_LM_Total),0) as rtn_cor_lm,
if(coalesce(sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),0) = 0, 0, round(100*sum(ds.RTN_COR_LM_Total) / sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),2)) as rtn_cor_lm_precent,
coalesce(sum(ds.RTN_COR_LY_LM_Total),0) as rtn_cor_lylm,
if(coalesce(sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),0) = 0, 0, round(100*sum(ds.RTN_COR_LY_LM_Total) / sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),2)) as rtn_cor_lylm_precent,


coalesce(sum(ds.RTN_NEW_MTD_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_MTD_Total),0) as rtn_mtd_overall,
coalesce(sum(ds.RTN_NEW_YTD_Total+ds.RTN_DEF_YTD_Total+ds.RTN_COR_YTD_Total),0) as rtn_ytd_overall,
coalesce(sum(ds.RTN_NEW_LY_MTD_Total+ds.RTN_DEF_LY_MTD_Total+ds.RTN_COR_LY_MTD_Total),0) as rtn_lymtd_overall,
coalesce(sum(ds.RTN_NEW_LY_YTD_Total+ds.RTN_DEF_LY_YTD_Total+ds.RTN_COR_LY_YTD_Total),0) as rtn_lyytd_overall,
coalesce(sum(ds.RTN_NEW_LM_Total+ds.RTN_DEF_LM_Total+ds.RTN_COR_LM_Total),0) as rtn_lm_overall,
coalesce(sum(ds.RTN_NEW_LY_LM_Total+ds.RTN_DEF_MTD_Total+ds.RTN_COR_LY_LM_Total),0) as rtn_lylm_overall
";

		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
		
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
                
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);
		
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                     // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                    
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}
		
		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	
	}
        
         /* Customer Transactions
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	*/
	function getCustomerTransactions($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType,$accountIDs, $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,";
		
		$sql .= "if(coalesce(sum(ds.ytd_invoices),0) = 0, 0, round(sum(ds.ytd_sales) / sum(ds.ytd_invoices),2)) as ytd_av_per_trans, 
if(coalesce(sum(ds.mtd_invoices),0) = 0, 0, round(sum(ds.mtd_sales) / sum(ds.mtd_invoices),2)) as mtd_av_per_trans, 
coalesce(sum(ds.ytd_invoices),0) AS ytd_invoices,
coalesce(sum(ds.mtd_invoices),0) AS mtd_invoices,
coalesce(sum(ds.wtd_invoices),0) AS wtd_invoices";

		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
		
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
                
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);
		
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                     // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                    
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}
		
		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	
	}
               /* Customer Budget
	* Parameters:
	* $startIndex -- start returning from this row
	* $maxRecords -- max number of rows to return
	* $location -- restrict to sales from this location
	* $region -- restrict to sales from this region
	* $slsm -- restrict to customers with this slsm
	* $dealerType -- restrict to customers with this dealer type
	*/
	function getCustomerBudget($startIndex, $maxRecords, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealerType,$accountIDs, $export = false, $fields = array()) {
		session_save_path(self::sessionSavePath);
		session_start();
		self::sugarDBConnect ();
		
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}
		
		/* form 2 sql statements:  one to count total number of possible records, one to select subset we want */
		$sql = "SELECT
			max(a.id) as id,
			a.slsm_c as slsm,
			concat(slsm.firstname, ' ', slsm.lastname) as slsmname,
			a.custno_c as custno,
			a.name as custname,
			a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone,";
		
                    $sql .= "coalesce(sum(ds.mtd_budget_sales),0) AS mtd_budget_sales,
coalesce(sum(ds.mtd_budget_gp),0) AS mtd_budget_gp,
if(coalesce(sum(ds.mtd_budget_sales),0) = 0, 0, round(100*sum(ds.mtd_budget_gp) / sum(ds.mtd_budget_sales),2)) as mtd_budget_gpp,
coalesce(sum(ds.ytd_budget_sales),0) AS ytd_budget_sales,
coalesce(sum(ds.ytd_budget_gp),0) AS ytd_budget_gp,
if(coalesce(sum(ds.ytd_budget_sales),0) = 0, 0, round(100*sum(ds.ytd_budget_gp) / sum(ds.ytd_budget_sales),2)) as ytd_budget_gpp";

		$sqlcount = "SELECT count(distinct a.custno_c) ";
		
		$sql .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s group by slsm, slsmname, custno, custname";
		
		$sqlcount .= " from accounts a
			left join dsls_dailysales ds on a.custid_c = ds.custid 
			left join dsls_slsm slsm on a.slsm_c = slsm.slsm %s";
		
		$sqlWhere = self::acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod);
                
		$sql = sprintf($sql, $sqlWhere);
		$sqlcount = sprintf($sqlcount, $sqlWhere);
		
		/* only order for subset query -- hopefully that means mysql query cache will be hit more often for $sqlcount */
		if (! is_null ( $sort )) {
			$sql .= " order by $sort";
			if (! is_null ( $sort_dir )) {
				$sql .= " $sort_dir";
			}
		}
		
		if (! is_null ( $maxRecords )) {
			if (is_null ( $startIndex )) {
				$sql .= " limit $maxRecords";
			} else {
				$sql .= " limit $startIndex,$maxRecords;";
			}
		}
                
                                     // export utils
                                    if($export){
                                        return self::buildExportContent($sql,$fields);
                                    }
                                    
		$returnData = array ();
		
		$returnData ['totalRecords'] = 0;
		
		if (! ($result = mysql_query ( $sqlcount ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		while ( $row = mysql_fetch_array ( $result ) ) { /* only 1 really */
			$returnData ['totalRecords'] = $row [0];
		}
		
		if (! ($result = mysql_query ( $sql ))) {
			print ($sql) ;
			die ( "Error in mysql query: " . mysql_error () );
		}
		$returnData ['data'] = array ();
		while ( $row = mysql_fetch_assoc ( $result ) ) {
			
			$returnData ['data'] [] = $row;
		}
		
		return $returnData;
	
	}
        
        	function acl_where($slsm,$region,$location,$dealerType,$accountIDs,$selectMethod) {

                                    //self::initialize521();

//                                    if (count($slsm) == 1) {
//                                            $slsm = self::getSlsmBelow($_SESSION['fmp_slsm'], $slsm);
//                                    }

                                    $sqlWhere = "(";

                                    /*not used anymore */
                                    $primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
                                    if ($selectMethod == 'u') { /* union */
                                            $primaryOp = "OR";
                                    }

                                    /* generate where criteria with everything in acl -- including custid */
                                    $acl_array = $_SESSION['fmp_acl'];
                                    $bNeedOp = false;
                                    foreach($acl_array as $acl) {
                                            $bNeedSubOp = false;
                                            if($bNeedOp) {
                                                    $sqlWhere .= " OR ";
                                            }
                                            $sqlWhere .= "(";
                                            if(!is_null($acl['location'])) {
                                                    $sqlWhere .= "ds.loc = ".$acl['location'];
                                                    $bNeedSubOp = true;
                                            }
                                            if(!is_null($acl['region'])) {
                                                    if($bNeedSubOp) { $sqlWhere .= " AND "; }
                                                    $sqlWhere .= "ds.region = ".$acl['region'];
                                                    $bNeedSubOp = true;
                                            }
                                            if(!is_null($acl['slsm'])) {
                                                    if($bNeedSubOp) { $sqlWhere .= " AND "; }
                                                    $sqlWhere .= sprintf("a.slsm_c = '%s'",$acl['slsm']);  /* select slsm from accounts table to include those with no ds data */
                                                    $bNeedSubOp = true;
                                            }
                                            if(!is_null($acl['dealertype'])) {
                                                    if($bNeedSubOp) { $sqlWhere .= " AND "; }
                                                    $sqlWhere .= sprintf("a.dealertype_c = '%s'",$acl['dealertype']);
                                                    $bNeedSubOp = true;
                                            }
                                            //if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
                                            if(!is_null($acl['custid'])) {
                                                    if($bNeedSubOp) { $sqlWhere .= " AND "; }
                                                    $sqlWhere .= sprintf("ds.custid = %d",$acl['custid']);
                                                    $bNeedSubOp = true;
                                            }
                                            //}
                                            $sqlWhere .= ")";
                                            $bNeedOp = true;
                                    }
                                    if(!$bNeedOp) { $sqlWhere .= "0"; } /* select nothing */ 

                                    $sqlWhere .= ") AND (";

                                    /* now add on criteria of selection */
                                    $bNeedOp = false;
                                    if (! is_null ( $location )) {
                                            $bNeedOp = true;
                                            if (! is_array ( $location )) {
                                                    $sqlWhere .= " ds.loc = '$location'";
                                            } else {
                                                    $sqlWhere .= " ds.loc IN(";
                                                    $bFirstLoc = true;
                                                    foreach ( $location as $locno ) {
                                                            if ($bFirstLoc) {
                                                                    $bFirstLoc = false;
                                                            } else {
                                                                    $sqlWhere .= ",";
                                                            }
                                                            $sqlWhere .= "'$locno'";
                                                    }
                                                    $sqlWhere .= ")";
                                            }
                                    }

                                    if (! is_null ( $region )) {
                                            if ($bNeedOp) {
                                                    $sqlWhere .= " AND";
                                            } else {
                                                    $bNeedOp = true;
                                            }
                                            if (! is_array ( $region )) {
                                                    $sqlWhere .= " ds.region = '$region'";
                                            } else {
                                                    $sqlWhere .= " ds.region IN (";
                                                    $bFirstReg = true;
                                                    foreach ( $region as $regno ) {
                                                            if ($bFirstReg) {
                                                                    $bFirstReg = false;
                                                            } else {
                                                                    $sqlWhere .= ",";
                                                            }
                                                            $sqlWhere .= "'$regno'";
                                                    }
                                                    $sqlWhere .= ")";
                                            }
                                    }

                                    /* if 521.php ever requests just a single slsm instead of a list of slsm below the selected, use this
                                    */
                                    if (!is_null($slsm)) {
                                            if ($bNeedOp) {
                                                    $sqlWhere .= " AND";
                                            } else {
                                                    $bNeedOp = true;
                                            }
                                            $slsm_array=array();
                                            if (! is_array ( $slsm )) {
                                                    $slsm_array = self::getSlsmBelow($_SESSION['fmp_slsm'], $slsm);
                                            } else {
                                                    foreach($slsm as $slsmno) {
                                                            $slsm_array = array_merge($slsm_array, self::getSlsmBelow($_SESSION['fmp_slsm'], $slsmno));
                                                    }
                                            }
                                            $sqlWhere .= " ds.slsm IN (";
                                            $bFirstSlsm = true;
                                            foreach ( $slsm_array as $slsmno ) {
                                                    if ($bFirstSlsm) {
                                                            $bFirstSlsm = false;
                                                    } else {
                                                            $sqlWhere .= ",";
                                                    }
                                                    $sqlWhere .= $slsmno;
                                            }
                                            $sqlWhere .= ")";
                                    }
                                    /*
                                    if (! is_null ( $slsm )) {
                                            if ($bNeedOp) {
                                                    $sqlWhere .= " AND";
                                            } else {
                                                    $bNeedOp = true;
                                            }
                                            if (! is_array ( $slsm )) {
                                                    $sqlWhere .= " a.slsm_c = '$slsm'";
                                            } else {
                                                    $sqlWhere .= " a.slsm_c IN (";
                                                    $bFirstReg = true;
                                                    foreach ( $slsm as $slsmno ) {
                                                            if ($bFirstReg) {
                                                                    $bFirstReg = false;
                                                            } else {
                                                                    $sqlWhere .= ",";
                                                            }
                                                            $sqlWhere .= "'$slsmno'";
                                                    }
                                                    $sqlWhere .= ")";
                                            }
                                    }*/

                                    if (! is_null ( $dealerType )) {
                                            if ($bNeedOp) {
                                                    $sqlWhere .= " AND";
                                            } else {
                                                    $bNeedOp = true;
                                            }
                                            if (! is_array ( $dealerType )) {
                                                    $sqlWhere .= " a.dealertype_c = '$dealerType'";
                                            } else {
                                                    $sqlWhere .= " a.dealertype_c IN (";
                                                    $bFirstDT = true;
                                                    foreach ( $dealerType as $dt ) {
                                                            if ($bFirstDT) {
                                                                    $bFirstDT = false;
                                                            } else {
                                                                    $sqlWhere .= ",";
                                                            }
                                                            $sqlWhere .= "'$dt'";
                                                    }
                                                    $sqlWhere .= ")";
                                            }
                                    }

                                    if(!$bNeedOp) { $sqlWhere .= "1"; } /* no criteria so provide everything already added in ACL */
                                    $sqlWhere .= ")";

                            $h = ''
                    //              . $this->user_add_on($is_user_id)
                                    . ' WHERE a.deleted = 0 AND ((a.slsm_c) Not In (20,232)) AND ((a.custtype_c) Not In (\'AFFL\',\'TRAV\')) AND (' . $sqlWhere . ')';
                            if(!empty($accountIDs) && $accountIDs[0] != '' && $accountIDs[0] != 'undefined' ){
                                $h  .= " AND a.id IN ('".implode("','", $accountIDs)."') ";
                            }
                            return $h;
                  }
                  
                function buildExportContent($sql,$fields){
                        self::sugarDBConnect ();
                        if (! ($result = mysql_query ( $sql ))) {
                                die ( "Error in mysql query: " . mysql_error () );
                        }
                        $fields_array = array();

                        foreach ($fields as $key => $value) {
                            $fields_array[] = $value;
                        }
                        $header = implode("\",\"", $fields_array);
                        $header = "\"" .$header;
                        $header .= "\"\r\n";
                        $content .= $header;
                        while ( $row = mysql_fetch_assoc ( $result ) ) {
                                    $new_arr = array();
                                     foreach ($fields as $key => $value) {
                                          if(array_key_exists($key, $row)){
                                              array_push($new_arr, preg_replace("/\"/","\"\"", $row[$key]));
                                          }
                                     }
                                    $line = implode("\",\"", $new_arr);
                                    $line = "\"" .$line;
                                    $line .= "\"\r\n";

                                    $content .= $line;
                        }
                        return $content;
                    }
                    
                    function getUserName($id){
                        self::sugarDBConnect ();
                        $sql = "select user_name from users where id='$id'";
                        if (! ($result = mysql_query ( $sql ))) {
                                return false;
                        }
                        $row = mysql_fetch_assoc ( $result );
                        return $row['user_name'];
                    }
}



