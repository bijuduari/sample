<?php

//Update Capacity Table
  session_start();


  include ("connection.php");
  $linkid = db_connect();

  $value    = $_REQUEST['value'];
  $column   = $_REQUEST['colHead'];
  $pkeyvalue= $_REQUEST['pkey'];

  $value = preg_replace('/^_/','',$value);
// error_log("DEBUG:$value:colHead:$column:pkey:$pkeyvalue",0);

  $tablename = "voters";
  /* Update a record using information given
  */ 
  $signum = $_SESSION['signum'];
  $email = $_SESSION['email'];
  $location = $_SESSION['location'];
  $mgrid = $_SESSION['manager_corid'];
  $dept = $_SESSION['dept'];
  $PKEY='Booth_ID';

  $row1 = mysql_query("select Booth_ID from $tablename where Signum_ID='$signum' and $PKEY='$pkeyvalue'");
  $rows = mysql_fetch_array($row1,MYSQLI_ASSOC);
  $pattern = '/-.*$/';
  $tmp = explode(':',$value);
  $Informative = preg_replace($pattern,'',$tmp[0]);
  $Innovativeness = preg_replace($pattern,'',$tmp[1]);
  $Overall_Presentability = preg_replace($pattern,'',$tmp[2]);

  $QUERY='';
  if($rows['Booth_ID'] != $pkeyvalue){
  	$QUERY = "insert into $tablename (Signum_ID,Email_ID,Location,Manager_Signum,Department,$PKEY,Informative,Innovativeness,Overall_Presentability) values ('$signum','$email','$location','$mgrid','$dept','$pkeyvalue','$Informative','$Innovativeness','$Overall_Presentability')";
  }else{
  	$QUERY = "update $tablename set Informative='$Informative',Innovativeness='$Innovativeness',Overall_Presentability='$Overall_Presentability' where Signum_ID='$signum' and $PKEY='$pkeyvalue'";
  }
  $RESULT = mysql_query($QUERY);
  if(!$RESULT){
	error_log("Error in updating $QUERY", 0);
  }

  echo $value;
?>
