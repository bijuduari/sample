<!DOCTYPE html>
<?php
	session_start();
	error_reporting(E_ERROR);

	if($_POST){
		if($_POST["login_name"])
		{
			$ldapuser   = strtoupper($_POST["login_name"]);
			$ldappass   = $_POST['login_password'];
			$ldapserver = 'ecd.ericsson.se';
			$ldaptree   = "uid=$ldapuser,ou=users,ou=internal,o=ericsson";

			// connect 
			$ldapconn = ldap_connect($ldapserver);
			if($ldapconn) {
			    // binding to ldap server
			    $ldapbind = ldap_bind($ldapconn, $ldaptree, $ldappass);
			    // verify binding
			    if ($ldapbind) {
     				  // echo "LDAP bind successful...<br /><br />";
      				  $result = ldap_search($ldapconn,$ldaptree, "(cn=*)") or die ("Error in search query: ".ldap_error($ldapconn));
				  $data = ldap_get_entries($ldapconn, $result);
			        for ($i=0; $i<$data["count"]; $i++) {
				            if(isset($data[$i]["cn"][0])) {
				            //echo "User: ". $data[$i]["cn"][0] ."<br />";
				            //echo "Name: ". $data[$i]["cn"][1] ."<br />";
						$user = "";
						if($data[$i]["cn"][0] != $ldapuser){
							$user = $data[$i]["cn"][0];
						}else{
							$user = $data[$i]["cn"][1];
						}
						$_SESSION['login_name']		= $user;
						$_SESSION['name'] 		= $user;
						}
				            if(isset($data[$i]["erioporgunitname"][0])) {
				            //echo "Dept: ". $data[$i]["erioporgunitname"][0] ."<br />";
						$_SESSION['dept'] 		= $data[$i]["erioporgunitname"][0];
						}
				            if(isset($data[$i]["eriisopmgr"][0])) {
				            //echo "Category: ". $data[$i]["eriisopmgr"][0] ."<br />";
						if($data[$i]["eriisopmgr"][0] == "N"){
							$_SESSION['login_category']	= "user";
						}else{
							$_SESSION['login_category']	= "manager";
						}
						}
				            if(isset($data[$i]["erioperationalmanager"][0])) {
				            //echo "Manager Signum: ". $data[$i]["erioperationalmanager"][0] ."<br />";
						$_SESSION['manager_corid'] 	= $data[$i]["erioperationalmanager"][0];
						}
				            if(isset($data[$i]["mail"][0])) {
				            //echo "Email: ". $data[$i]["mail"][0] ."<br /><br />";
						$_SESSION['email'] 	        = $data[$i]["mail"][0];
				            }
				            if(isset($data[$i]["employeenumber"][0])) {
				            //echo "Personal Number: ". $data[$i]["employeenumber"][0] ."<br /><br />";
						$_SESSION['pnumber'] 	        = $data[$i]["employeenumber"][0];
				            }
                                            if(isset($data[$i]["physicaldeliveryofficename"][0])) {
                                            //echo "Personal Number: ". $data[$i]["employeenumber"][0] ."<br /><br />";
                                                $_SESSION['location']           = $data[$i]["physicaldeliveryofficename"][0];
                                            }
					
			        }
                                $_SESSION['signum']           = "$ldapuser";
                                $_SESSION['tool']           = "vote";

				include ("connection.php");
				$linkid = db_connect();
				$row1 = mysql_query("select Signum_ID from csiemployees where Signum_ID='$ldapuser'");
				$rows= mysql_fetch_array($row1,MYSQLI_ASSOC);
				if($rows['Signum_ID'] == ''){
					$row1 = mysql_query("select count(Signum_ID) as UPLOADCOUNT from csiemployees");
					$rows= mysql_fetch_array($row1,MYSQLI_ASSOC);
					$reviewersig = $reviewer[$rows['UPLOADCOUNT']%13];
					mysql_query("insert into csiemployees (Signum_ID,Email_ID,Manager_Signum,Department,Location,Reviewer_Signum,Updated_Time) values ('$ldapuser','".$_SESSION['email']."','".$_SESSION['manager_corid']."','".$_SESSION['dept']."','".$_SESSION['location']."','$reviewersig',now())");
				}
                                if($_REQUEST['redir'] == 'review') {
				  header("location:reviewpage.php");
                                } else {
				  header("location:vote.php?signum=$ldapuser");
                                }
			    } else {
			        $error_msg = ldap_error($ldapconn);
			    }
			}else{
			        $error_msg = "LDAP connection failed!";
			}
			ldap_close($ldapconn);
		}
	}
?>
<html>
<head>
	<meta charset="UTF-8">
	<!-- Remove this line if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<meta name="description" content="Ericsson">
	<meta name="author" content="">
	<title>Confluence 3.0 Voting Portal</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/common_style.css" />
	<link rel="stylesheet" href="style.css">
	<script type="text/javascript" src="../js/jquery-1.2.6.min.js" ></script>   
	<script type="text/javascript" src="../js/functions.js"></script>

        <style type='text/css'>
            #loginbox {
      width: 400px;
      height: 280px;
      background-color:#DFDFDF;
    }

    .box_text_style {
      color: #FFFFFF;
    }

    .logintitle {
      font-size: 18px;
      font-weight: 300;
      height: 60px;
      line-height: 60px;
      width: 100%;
      text-align: center;
    }

    .separator {
       border-bottom: 1px solid #FFFFFF;
       width:90%;
       margin:auto; 
       clear:both;
    }

    .subhead {
	margin-left: 50px;		
    }

    .color_line {
	background-image: url("color_line.jpg");
	height: 3px;
	width: 100%;
    background-repeat: repeat;
    display: inline-block;
    float: right;
    opacity: 0.5;
    filter: alpha(opacity = 50);
    cursor: pointer;
   }



</style>
</head>
<body>
<div class="container" style='margin: 0px;'>
<table border=0>
	<tr>
	<td width='70px'>
		<div id="logo" style="float: left;padding:20px 60px 10px;">
			<a class="logo fleft" href="http://www.ericsson.com"><img alt="Ericsson" src="econ-white.jpg"></a>
		</div>
	</td>
	<td width='90%'>
		<div align="left" class='ericssonfont'>
			<strong  style="font-size: 35px;">ACE Of Confluence </strong>
			<strong  style="font-size: 20px;">(Voting Portal)</strong>
		</div>
	</td>
	</tr>
</table>
	<div class="color_line"></div>
	<div class="home-page" style='margin:50px 40px 30px;'>
	<section class="" style='height:350px;'>
		<div id="browser" style='color:red;' align='center'></div>
<table border=0><tr><td>
	<img src='img/ace.jpg'>
	</td><td>
     <div id='loginbox' class='box_text_style'>
        <div class='logintitle ericssonfont'> LOGIN</div>
        <div class='separator'></div>
        <div >
	<div id="error" align='center'><?php echo $error_msg;?>&nbsp;</div>
          <form name="login_form" method="post" class="login_form" id="login_form">
            <table border=0>
              <tr>
		<td style='width:22%'></td>
                <td align=left style='width:25%;vertical-align: bottom;'>Signum ID:</td>
                <td align=left colspan=2 ><input type="text" class="required" name="login_name" id="login_name"></td>
              </tr>
              <tr height='10px'></tr>
              <tr>
		<td style='width:22%'></td>
                <td align=left style='width:25%;vertical-align: bottom;'>LAN Password:</td>
                <td align=left colspan=2 ><input type="password" class="required" name="login_password" id="login_password"></td>
              </tr>
            </table>
            <div style='width:88%;margin:auto'>
		<table><tr><td>
              <a name="login_now" id="login_now" href="#"><button style="padding:5px" class="button fright">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a>
		</td></tr></table>
            </div>
<input type=hidden name='redir' value='<?php echo $_REQUEST['redir'];?>'>
          </form>
        </div>
</div>
</td></tr></table>
		</section>
	</div>
<div class="color_line"></div>
<table border=0>
<tr>
<td width='70px'>
<div id="logo" style="float: left;padding:10px 60px 0px;">
</div>
</td><td width='61%'>
	<p style='color:blue;'>Note: This application is best viewed on Chrome and Firefox with 1366x768 pixels</p>
</td>
<td>
<div>For More details <a target='_blank' href='https://ericoll.internal.ericsson.com/sites/CSI_ADM_CONFLUENCE/Pages/CONFLUENCE_2015.aspx'>Click here</a>
</div>
</td>
</tr></table>
</div>
<script type='text/javascript'>
	$(document).ready(function(){
		var browse = get_browser(); 
//	alert(browse);
		if(browse != 'Firefox' && browse != 'Chrome' && browse != 'Netscape' && browse != 'MSIE'){
			$("#browser").text('You are currently using an unsupported browser. This applicaion was tested in Firefox and Chrome ');
//			alert('This applicaion does not support for this browser!');
//			window.close();
		}
		$("#login_name").focus();
	});
</script>
</body>
</html>
