<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);

	if($_SESSION['login_name'] == "" || $_SESSION['tool'] != 'vote'){
		header("location:login.php?redir=vote");
	}

include ("connection.php");
$linkid = db_connect();

?>
<!DOCTYPE html>
<html lang = "en">
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
	<meta name="description" content="ERI Tracker">
	<meta name="author" content="">
	<meta http-equiv="x-ua-compatible" content="IE=8">
	<title>ACE Of Confluence - Voting Portal</title>
   	<link rel="stylesheet" type="text/css" href="../css/common_style.css" />
   	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables_themeroller.css" />   
	<link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.8.4.custom.css" />  
	<link rel="stylesheet" type="text/css" href="../css/popup_content.css">
	<link rel="stylesheet" type="text/css" href="multiple-select.css">
	<link rel="stylesheet" type="text/css" href="style_datatable.css">
	<style type='text/css'>
    .color_line {
    background: url("color_line.jpg") repeat-x scroll 0 0 rgba(0, 0, 0, 0);
    clear: both;
    display: block;
    height: 3px;
    margin: 0 auto;
 	  }
	th.editable {
		background:#B45F04 !important;
	}
	td.highlight:hover {
      			background-color: #DBFF70;
	}
	div.floatright {
		float: right;
		padding: 9px;
	}
	th {
		text-align:left
	}
	.tools{
	padding: 9px;
	}
	</style>
	<script type="text/javascript" src="../js/jquery-1.2.6.min.js"></script>
   	<script type="text/javascript" src="../js/jquery.dataTables.min.js" charset="utf-8" ></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="jquery.jeditable_cust.js"></script>
        <script type="text/javascript" src="jquery.dataTables.editable.js"></script>
        <script type="text/javascript" src="jquery.multiple.select.custom.js"></script>
   	<script type="text/javascript" src="../js/functions.js"></script>
     
	<!-- Inclusion of shadowbox plugin for fancy popus-->
	<link rel="stylesheet" type="text/css" href="../plugins/shadowbox/shadowbox.css">
	<script type="text/javascript" src="../plugins/shadowbox/shadowbox.js"></script>
   	<script type="text/javascript">
		$.editable.addInputType('ratepicker', {
					/* Create input element. */
			element : function(settings, original) {
			        var InformativeL   = $('<label>Useful/Informative:</label>');
			        var Informative = $('<select id="Informative" />');
			        var InnovativenessL  = $('<label>Innovative:</label>');
			        var Innovativeness  = $('<select id="Innovativeness" />');
			        var PresentabilityL   = $('<label>Presentability/Appeal Factor:</label>');
			        var Presentability  = $('<select id="Presentability" />');
        
 			       	var option1 = $('<option />').val('').append('--Select--');
			     	Informative.append(option1);
 			       	var option2 = $('<option />').val('').append('--Select--');
			     	Innovativeness.append(option2);
 			       	var option3 = $('<option />').val('').append('--Select--');
			     	Presentability.append(option3);

				var options = ['1-Fair','2-Average','3-Good','4-Very Good','5-Excellent'];
				for(var per in options){
 			        	var option1 = $('<option />').val(options[per]).append(options[per]);
			     		Informative.append(option1);
 			        	var option2 = $('<option />').val(options[per]).append(options[per]);
			     		Innovativeness.append(option2);
 			        	var option3 = $('<option />').val(options[per]).append(options[per]);
			     		Presentability.append(option3);
				}
			        $(this).append(InformativeL);
			        $(this).append(Informative);
			        $(this).append('<br>');
			        $(this).append(InnovativenessL);
				$(this).append(Innovativeness);
			        $(this).append('<br>');
			        $(this).append(PresentabilityL);
				$(this).append(Presentability);
			        $(this).append('<br>');

			        /* Last create an hidden input. This is returned to plugin. It will */
			        /* later hold the actual value which will be submitted to server.   */
			        var hidden = $('<input type="hidden" />');
			        $(this).append(hidden);
			        return(hidden);
   		 	},
			plugin : function(settings, original) {
       				/* Workaround for missing parentNode in IE */
			    	var form = this;
		 	   	settings.onblur = 'ignore';
			    	$(this).find('input[type=button]').bind('click', function() {
				        return false;
        			});
    			},
			submit: function (settings, original) {
    				/* Call before submit hook. */
				if($('#Informative').val() == '' || $('#Innovativeness').val() == '' || $('#Presentability').val() == ''){
					alert('Please vote for all ratings!');
				        $('input', this).val('');
				}else{
			        var value = $('#Informative').val() + ':' + $('#Innovativeness').val() + ':' + $('#Presentability').val();
				        $('input', this).val(value);
				}
                                var overall_sum = parseInt($('#Informative').val()) +
                                                  parseInt($('#Innovativeness').val()) +
                                                  parseInt($('#Presentability').val());
                                if(overall_sum <= 7) {
                                  if(! confirm('You gave a lower rating for this booth. Are your sure to submit this score ?')) {
                                      $('input', this).val('');
                                  }
                                }
    			}
		});


	var oTable;
	Shadowbox.init({
	});
	$(document).ready(function(){
			oTable = $('#employee_details').dataTable({
					"sScrollY":"500px",
					"sScrollX":"100%",
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"bScrollCollapse": true,
					"bPaginate": true, 
					"bFilter": true, 
					"bSort": false, 
					"bInfo": true, 
					"bLengthChange": true, 
					"bAutoWidth": true,
					"bProcessing": true,
				        "bServerSide": true,
					"sDom": '<"H"<"tools"><"floatright"f>r>t<"F"ip>',
				        "sAjaxSource": "serv_processing.php",
					"fnServerData": function ( sSource, aoData, fnCallback ) {
						$.ajax( {
							"dataType": 'json',
							"type": "POST",
							"url": sSource,
							"data": aoData,
							"success": fnCallback
						} )
					},
					"fnServerParams": function ( aoData ){
                                                aoData.push({ "name": "signum","value":"<?php echo $_SESSION['signum']; ?>" } );
                                                aoData.push({ "name": "zonename","value":"<?php echo "$zonename"; ?>" } );
					},
				        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
						<?php
						 	foreach($RATEEDITABLECOLINX as $cellid){
								echo "if(aData[$cellid] == '' || aData[$cellid] == '--' || aData[$cellid] == null){ \n";
								echo "$('td:eq($cellid)', nRow).html('');\n";
								echo "$('td:eq($cellid)', nRow).addClass('rating_td highlight');}\n";
							}
						 	foreach($ZONECOLINX as $cellid){
								echo "if(aData[$cellid] == 'Live Lean'){ \n";
								echo "$(nRow).css('background-color', '#FFCFCF');\n";
								echo "}else if(aData[$cellid] == 'Think Big'){ \n";
								echo "$(nRow).css('background-color', '#CFEEFF');\n";
								echo "}else{ \n";
								echo "$(nRow).css('background-color', '#C3DFC7');}\n";
							}
						?>
			               		 return nRow;
          				},
					"fnDrawCallback": function() {
						$('td.rating_td').editable('serv_update.php', {
						            "submitdata": function ( value, settings ) {
									var aPos    = oTable.fnGetPosition( this );
									var pkey  = oTable.fnSettings().aoData[aPos[0]]._aData[0];
									var colhead = oTable.fnSettings().aoColumns[aPos[2]].sTitle;
							                return {
							                    "pkey": pkey,
									    "colHead" : colhead,
							                };
						            },
							    "type"   : 'ratepicker',
							    "submit" : 'OK',
							    "cancel" : 'Cancel',
							    "tooltip": 'Click to edit!',
						}, function( sValue, y ) { 
                                                                var aPos = oTable.fnGetPosition( this );
                                                                oTable.fnUpdate( sValue, aPos[0], aPos[1], false );
								if(sValue != ''){
							//		$(this).removeClass('valuerequired');
								}else{
							//		$(this).addClass('valuerequired');
								}
								oTable.fnAdjustColumnSizing();
						} );


   					}
			});

			$("div.tools").html('Select Zone: <select width="50px" id="zonename" name="zonename"><option value="All">All</option>' +
<?php
						if($zonename == 'Live Lean'){
							$aselect = 'selected';
						}elseif($zonename == 'Think Big'){
							$bselect = 'selected';
						}elseif($zonename == 'Go Green'){
							$cselect = 'selected';
						}
						echo "'<option $aselect value=\"Live Lean\">Live Lean</option>'+";
						echo "'<option $bselect value=\"Think Big\">Think Big</option>'+";
						echo "'<option $cselect value=\"Go Green\">Go Green</option>'+";
?>
				'</select>'); 
<!--				'</select> Search <input style="width:200px" type=text id="cfilter"/>');  -->
			$("div.tools").css({float:"left"});
			$('#cfilter').keyup( function() {
				oTable.fnFilter(this.value);
   			});
			$("#zonename").change( function() { document.supply_form.submit();});

	});

	</script>
</head>
<body class="ex_highlight_row">
	<div class="container">
	<header id="navtop">
	<table border=0 style="height: 80px;width: 100%; ">
	<tbody><tr align="left" valign="middle"><td width="10%">
		<div id="logo" style="float: left;">
			<a class="logo fleft" href="/"><img alt="Ericsson" src="econ-white.jpg"></a>
		</div>
		</td>
	<td width="60%">
		<div align="center" class='ericssonfont'>
			<strong  style="font-size: 35px;">ACE Of Confluence</strong>
			<strong  style="font-size: 20px;">(Voting Portal)</strong>
		</div>
	</td>
	<td width="35%">
		<table><td>Welcome:</td><td><strong> <?php echo $_SESSION['name']; ?> </strong></td></tr>
		<tr><td>Department:</td><td><strong> <?php echo $_SESSION['dept']; ?> </strong></tr>
		<tr><td colspan=2 align='right'>
			<a href="logout.php">Logout</a>
		</td></tr>
		</table>
	</td></tr>
	</tbody>
	</table>
	</header>
	<div class="color_line"></div>
	<div class="home-page main">
	<section class="grid-wrap" >
		<div id="browser" style='color:red;' align='center'></div>
		<div class="grid col-full  mq2-col-full">
<div  align=center class="fleft" style='width:100%;align:center;' ><table style='display: inline;'>
<tr><th style="font-size:10px" >** One User is allowed to vote for a maximum of 3 solutions!</th></tr>
</table></div>
		<form name="supply_form" id="supply_form" method="post" enctype="multipart/form-data">
			<table cellpadding="0" cellspacing="0" border="0" id="employee_details" class="employee_details_tbl" width="100%" >
				<thead>
					<tr>
                                          <td>S.No</td>
                                          <td>Title</td>
                                          <td>Vote</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</form>
         </div>
        </section>
	<label id="wait"></label>
    </div> <!--main-->
<div class="color_line"></div>
<p style='color:blue;padding-left: 0em;'>This application is best viewed on Chrome and Firefox with 1366x768 pixels</p>
    </div>
<script type='text/javascript'>
	$(document).ready(function(){
		var browse = get_browser(); 
		if(browse != 'Firefox' && browse != 'Chrome' && browse != 'Netscape' && browse != 'MSIE'){
			$("#browser").text('You are currently using an unsupported browser. This applicaion was tested in Firefox and Chrome ');
//			alert('This applicaion does not support for this browser!');
//			window.close();
		}
	});
</script>

</body>
</html>
