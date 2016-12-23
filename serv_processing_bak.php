<?php

include ("connection.php");
include ("jsonwrapper/jsonwrapper.php");

$linkid = db_connect();

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	

	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array('t1.Booth_ID','t1.Zone','t1.Booth_Number','t1.Type','t1.Title','t1.SPOC','t2.Informative','t2.Innovativeness','t2.Overall_Presentability');

	/* DB table to use */
	$sTable = 'booth';

	/* Indexed column (used for fast and accurate table cardinality) */
//	if(preg_match("/_ca/",$sTable)){
		$sIndexColumn = "Booth_ID";
//	} 
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * Local functions
	 */
	function fatal_error ( $sErrorMessage = '' )
	{
		header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
		die( $sErrorMessage );
	}

	
	/* 
	 * Paging
	 */
	$rowcount = 1;
	$sLimit = "";
	if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
	{
		$rowcount = $_REQUEST['iDisplayStart'];
		$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
			intval( $_REQUEST['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_REQUEST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ )
		{
			if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "".$aColumns[ intval( $_REQUEST['iSortCol_'.$i] ) ]." ".
					($_REQUEST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	if($sOrder == ""){
		$sOrder = "ORDER BY t1.$sIndexColumn";

	}
//error_log("$sOrder");	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_REQUEST['sSearch']) && $_REQUEST['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_REQUEST['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && $_REQUEST['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_REQUEST['sSearch_'.$i])."%' ";
		}
	}

	if(isset($_REQUEST['zonename'])){
		$name = $_REQUEST['zonename'];
		if($name != 'All' and $name != ''){	
		if ( $sWhere == "" ){
			$sWhere = "WHERE ";
		}else{
			$sWhere .= " AND ";
		}
		$sWhere .= "Zone='$name'";
		}
	}

//error_log($_REQUEST['pres_filter']." ".$_REQUEST['cont_filter']." ".$_REQUEST['reviewer_signum']);
/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM $sTable t1 left join voters t2 on t1.Booth_id = t2.Booth_ID and t2.Signum_ID='".$_REQUEST['signum']."' $sWhere $sOrder $sLimit";
//error_log("DEBUG $sQuery",0);
	$rResult = mysql_query( $sQuery, $linkid ) or fatal_error( 'MySQL Error: 1 ' . mysql_errno() );
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $linkid ) or fatal_error( 'MySQL Error: 2 ' . mysql_errno() );
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
//echo $sQuery;
	$rResultTotal = mysql_query( $sQuery,  $linkid ) or fatal_error( 'MySQL Error: 3 ' . mysql_errno() );
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_REQUEST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
	//	$row = array(++$rowcount);
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$aColumns[$i]=preg_replace('/t1\.|t2\./','',$aColumns[$i]);
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );

?>
