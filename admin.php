<?php require("lib.php"); ?>
<html>
<head>
	<link href="css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui-1.10.3.custom.js"></script>
	<script>
	$(function() {
		$( "#accordionGroups").accordion({ heightStyle: "content", collapsible: true });
	});

	$(function() {
    	$( "#tabs" ).tabs({
      		collapsible: true
    	});
  	});
	
	$(function() {
    	$( "a.logout" )
      		.button()
      		.click(function( event ) {
			window.location.href = '.';
        	//event.preventDefault();
      	});
  	});
	</script>

			<?php
			if(AdminCorrect()>0){ 
				$c = Connect();
				// get a list of all the group id's the admin is related to and go through them one by one
				// get admin id	
				$q = "SELECT id FROM admins WHERE user = '" . $_POST['txtAdmUser'] . "'";
				$result = mysql_query($q) or die (mysql_error());
				$admin_id = mysql_result($result, 0, "id");
				
				// get all groups associated with the admin
				$q = "SELECT DISTINCT id_groups FROM adminthathasgroups WHERE id_admins=$admin_id";
				$result = mysql_query($q) or die (mysql_error());
				$num = mysql_numrows($result);
				$i=0;
				$teltrips = 0;
				$telgroups = 0;
				
				while ($i<$num){
					$athg_id = mysql_result($result, $i, "id_groups");
					
					// get a list of all the trip id's related to every group and go through them one by one
					$q_idtrips = "SELECT DISTINCT id_trips FROM groupthathastrips WHERE id_groups=$athg_id";
					$res_idtrips = mysql_query($q_idtrips) or die (mysql_error());
					$num2 = mysql_numrows($res_idtrips);
					$i2=0;
					
					while ($i2<$num2) {
						$gtht_id = mysql_result($res_idtrips, $i2, "id_trips");
						// get the id of the trip
						$q_tripname = "SELECT id, tripname, tripdate FROM trips WHERE id=$gtht_id";
						$res_tripname = mysql_query($q_tripname) or die (mysql_error());
						$trip_id = mysql_result($res_tripname, 0, "id");
	
						?>
						<script>
						$(function() {
							$( "#accordion<?php echo $teltrips; ?>" ).accordion({ heightStyle: 'content', collapsible: true });
						});
						</script>					
						<?php
						$teltrips++;
						$i2++;
					}
					?>
					<script>
					$(function() {
						$( "#accordionG<?php echo $telgroups; ?>" ).accordion({ heightStyle: 'content', collapsible: true });
					});
					</script>
					<?php
					$telgroups++;	
					$i++;
				}
				DisConnect($c);
			} else {
				echo ("Admingegevens niet correct");
			}
			?>	

<style>
  #accordion-resizer {
    padding: 10px;
    width: 350px;
    height: 220px;
  }
 </style>

<style type="text/css">
p.vraag
{
font-size: 12px;
border-bottom-style: dotted;
border-width: 1px;
}

.answer
{
font-size: 10px;
}

#tabs
{
font-size: 10px;
}

td.comment
{
font-size: 10px;
}
h2
{
border-style:solid;
border-width:2px; 
background-color: #6666FF;
}
h4
{
/*border-style: dashed;
border-width: 1.5px; */
background-color: #9999FF;
}

#loading {
  width: 100%;
  height: 100%;
  top: 0px;
  left: 0px;
  position: fixed;
  display: block;
  opacity: 0.7;
  background-color: #fff;
  z-index: 99;
  text-align: center;
}

#loading-image {
  position: relative;
  top: 30%;
  left: 5%;
  z-index: 100;
}
</style>
<title>Fieldtrip administrator</title>
<script type="text/javascript" src="datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="datepickercontrol.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript">
<!--

function submitcomment(id)
{
  document.forms['comment' + id].submit();
  // send email notifying student of comment
}
function hide(id)
{
	if (document.getElementById(id).style.display == "none"){
		document.getElementById(id).style.display = "block";
	} else {
		document.getElementById(id).style.display = "none";
	}
} 

function makevisible(id)
{
	if (document.getElementById(id).style.display == "none"){
		document.getElementById(id).style.display = "block";
	}
}
-->
</script>
</head>

<body>

<div id="loading">
  <img id="loading-image" src="images/ajax-loader.gif" alt="Loading..." />
</div>

<?php 
	
	if (strlen($_POST['txtComment']) > 0) {
		$c = Connect();
			$q = "INSERT INTO comments(comment, commenter, id_questions) VALUES ('" . $_POST['txtComment'] . "', '" . $_POST['txtAdmUser'] . "', '" . $_POST['question_id'] . "')";
			$result = mysql_query($q) or die (mysql_error());
		DisConnect($c);
	}
	
	if (strlen($_POST['btnGroepLijst']) >0) {
		$c = Connect();
		
		// get code of all admins associated with the group
		$string = $_POST['txtAdminsForGroup'];
		if (strlen($string)>0) {
			$token = strtok($string, ";");
			$i = 'A';
			while ($token != false) {
				$adminlist[$i]=$token;			
  				$token = strtok(";");
				$i++;
  			}
		} 
		
		// then create group
		$q = "INSERT INTO groups (groupname) VALUES ('" . $_POST['txtGroepNaam'] . "')";
		$result = mysql_query($q) or die (mysql_error());
		
		// then get group id
		$q = "SELECT id FROM groups WHERE groupname='" . $_POST['txtGroepNaam'] . "'";
		$result = mysql_query($q) or die (mysql_error());
		$group_id = mysql_result($result, 0, "id");
		
		// then create adminthathasgroups: for every admin id the group id is added
		foreach($adminlist as $admin) {
			//eerst admin id achterhalen
			$q = "SELECT id FROM admins WHERE user = '$admin'";
			$result = mysql_query($q) or die (mysql_error());
			$admin_id = mysql_result($result, 0, "id");
			$q = "INSERT INTO adminthathasgroups(id_groups, id_admins) VALUES ('$group_id' , '$admin_id')";
			
			$result = mysql_query($q) or die (mysql_error());
		}
				
		// HERE: must adjust to the new structure with studentthathasgroups
		// >>>>>		
		// then add every student to the table students with group id and generate a password (user is email address)
		// exclude all that dont contain '@' to avoid errors
		$string = $_POST['txtGroepLijst'];
		$token = strtok($string, "\n");
		while ($token != false) {
			if (strlen(stristr($token, '@')) > 0) {
				$token = trim($token);
				$p = chr(rand(65,90)) . chr(rand(97,122)) . chr(rand(65,90)) . chr(rand(97,122));
  				$q = "INSERT INTO students(user, pasw, id_groups) VALUES ('$token', '$p', '$group_id')";
				$result = mysql_query($q) or die (mysql_error());
				$gn = $_POST['txtGroepNaam'];
				// email the pasw to every student that has that group id
				$message = "u werd via de site fieldtrips.hogent.be toegevoegd \n";
				$message .= "aan de groep $gn . Uw logingegevens: \n";
				$message .= "user : " . $token . "\n";
				$message .= "pasw : " . $p . "\n\n";
				$message .= "OPGELET: voor elke aangemaakte groep krijgt u een nieuw paswoord, \n";
				$message .= "toch kunt u elk van deze paswoorden gebruiken om in te loggen en alle \n";
				$message .= "uitstappen te zien. U hoeft dus maar één paswoord te onthouden \n";
				mail($token, "logingegevens voor groep " . $_POST['txtGroepNaam'], $message);							
  				$token = strtok("\n");
			}
  		}	
		DisConnect($c);
	} 
	
	if (strlen($_POST['btnAddTrip']) >0) {
		$c = Connect();
		
		// get the group id from table groups
		$groupname = $_POST['slctGroup'];
		$q = "SELECT id FROM groups WHERE groupname='$groupname'";
		$result = mysql_query($q) or die (mysql_error());
		$group_id = mysql_result($result, 0, "id");
		// add trip to table trips
		$q = "INSERT INTO trips (tripname, tripdate) VALUES ('" . $_POST['txtTripName'] . "','" . $_POST['txtTripDate'] . "')";
		$result = mysql_query($q) or die (mysql_error());
		// get trip id
		$q = "SELECT id FROM trips WHERE tripname='" . $_POST['txtTripName'] . "'";
		$result = mysql_query($q) or die (mysql_error());
		$trip_id = mysql_result($result, 0, "id");
		// add entry to groupthathastrips
		$q = "INSERT INTO groupthathastrips (id_groups, id_trips) VALUES ('$group_id','$trip_id')";
		$result = mysql_query($q) or die (mysql_error());
		
		DisConnect($c);
	}
	
?>
<div align="center">
  <table width="75%" border="0" align="center">
    <tr>  
		<td bgcolor="#E9E9E9"> 
		  <blockquote> 
			<h1><font color="#0080C0" size="7" face="Georgia, Times New Roman, Times, serif">FieldTrips</font> 
			</h1>
			<h1 align="right"><font color="#0099CC" size="4">administrator</font> </h1>
			<p class="answer" align="right"><a class="logout" href="#">Logout</a></p>
		  </blockquote>
		</td>
    </tr>
	<tr>
	<!--
    <tr bgcolor="#A4C1FF"> 
      <td bgcolor="#99CCFF"> <p>&nbsp;</p>
        <blockquote>  -->
	  <td><p>&nbsp;</p>
          <?php if(AdminCorrect()>0){ 
		    		$comment_teller=1;
			    	$c = Connect();
		  ?>
          <p align="left"><font size="+2" face="Georgia, Times New Roman, Times, serif"><strong>Administrator</strong></font></p>
          

		  <div id="tabs">
			<ul>
				<li><a href="#tabs-1">Add group</a></li>
				<li><a href="#tabs-2">Add trip</a></li>
			</ul>
			<div id="tabs-1">
				<form action="admin.php" method="post" name="frmGoep" target="_self" id="frmGoep">
				<p>Groupname : <input name="txtGroepNaam" type="text" id="txtGroepNaam"></p>
				<p>List of students:</p>
				<p> 
					<textarea name="txtGroepLijst" cols="50" rows="6" id="txtGroepLijst"></textarea>
				</p>
				<p>All admins (including yourself) that are connected to this group (separate with semicolon):</p>
				<p><textarea name="txtAdminsForGroup" cols="100" rows="3" id="txtAdminsForGroup"></textarea></p>
				<p> 
					<input name="txtAdmUser" type="hidden" value="<?php echo $_POST['txtAdmUser']; ?>" >
					<input name="txtAdmPasw" type="hidden" value="<?php echo $_POST['txtAdmPasw']; ?>" >
					<input name="btnGroepLijst" type="submit" id="btnGroepLijst" value="Submit">
				</p>
				</form>
			</div>
			<div id="tabs-2">
			  <form action="" method="post" name="frmUitstap" id="frmUitstap">
				<p>Trip name and location</p>
				<p><textarea name="txtTripName" cols="100" rows="2" id="txtTripName"></textarea></p>
				<p>Date: 
					<input type="text" name="txtTripDate" id="txtTripDate" datepicker="true" onFocus="makevisible('addtrip'); makevisible('addtrip2');">
				</p> 
				<p><font face="Courier New, Courier, mono">Group: 
				  <select name="slctGroup" id="slctGroup">
					<?php 
						// get admin id
						$q = "SELECT id FROM admins WHERE user = '" . $_POST['txtAdmUser']. "'";
						$result = mysql_query($q) or die (mysql_error());
						$admin_id = mysql_result($result, 0, "id");
						//get all groupnames the admin is linked to and add to the option list
						$q = "SELECT id_groups FROM adminthathasgroups WHERE id_admins = '$admin_id'";
						$result = mysql_query($q) or die (mysql_error());
						if ($result) {
							$num = mysql_numrows($result);
							$i=0;
							while ($i<$num){
								$groep_id = mysql_result($result, $i, "id_groups");
								$q = "SELECT groupname FROM groups WHERE id='$groep_id'";
								$res = mysql_query($q) or die (mysql_error());
								$groepnaam = mysql_result($res, 0, "groupname");
								echo "<option value='$groepnaam'>$groepnaam</option>"; 
								$i++;
							}
						}
					?>
				  </select>
				  </font></p>
				<input name="txtAdmUser" type="hidden" value="<?php echo $_POST['txtAdmUser']; ?>" >
				<input name="txtAdmPasw" type="hidden" value="<?php echo $_POST['txtAdmPasw']; ?>" >
				<p> 
				  <input name="btnAddTrip" type="submit" id="btnAddTrip" value="Submit">
				</p>
			  </form>
			</div>
		</div>

		  
		  
		 <!-- 
		  <h4 onClick="hide('addgroup')">Add group</h4>
			<div id="addgroup" style="display: none">
          <form action="admin.php" method="post" name="frmGoep" target="_self" id="frmGoep" style="background-color: #CCCCCC">
            <p><font face="Courier New, Courier, mono">Groupname : 
              <input name="txtGroepNaam" type="text" id="txtGroepNaam">
              </font></p>
            <p><font face="Courier New, Courier, mono">List of students:</font></p>
            <p> 
              <textarea name="txtGroepLijst" cols="50" rows="6" id="txtGroepLijst"></textarea>
            </p>
            <p><font face="Courier New, Courier, mono">All admins (including yourself) 
              that are connected to this group (separate with semicolon):</font></p>
            <p><font face="Courier New, Courier, mono"> 
              <input name="txtAdminsForGroup" type="text" id="txtAdminsForGroup" size="150">
              </font></p>
            <p> 
              <input name="txtAdmUser" type="hidden" value="<?php //echo $_POST['txtAdmUser']; ?>" >
              <input name="txtAdmPasw" type="hidden" value="<?php //echo $_POST['txtAdmPasw']; ?>" >
              <input name="btnGroepLijst" type="submit" id="btnGroepLijst" value="Submit">
            </p>
          </form>
		  </div>
          <p align="left"> </p>
          <h4 onClick="hide('addtrip'); hide('addtrip2');">Add trip</h4>
			
          <form action="" method="post" name="frmUitstap" id="frmUitstap" style="background-color: #CCCCCC"><div id="addtrip" style="display: none"> 
            <p><font face="Courier New, Courier, mono">Trip name and location</font></p>
            <p><font face="Courier New, Courier, mono"> 
              <textarea name="txtTripName" cols="140" rows="2" id="txtTripName"></textarea>
              </font> </p></div>
            <p><font face="Courier New, Courier, mono">Date:</font> 
              <input type="text" name="txtTripDate" id="txtTripDate" datepicker="true" onFocus="makevisible('addtrip'); makevisible('addtrip2');">
			 
            </p> <div id="addtrip2" style="display: none">
            <p><font face="Courier New, Courier, mono">Group: 
              <select name="slctGroup" id="slctGroup">
                <?php /*
					// get admin id
					$q = "SELECT id FROM admins WHERE user = '" . $_POST['txtAdmUser']. "'";
					$result = mysql_query($q) or die (mysql_error());
					$admin_id = mysql_result($result, 0, "id");
					//get all groupnames the admin is linked to and add to the option list
					$q = "SELECT id_groups FROM adminthathasgroups WHERE id_admins = '$admin_id'";
					$result = mysql_query($q) or die (mysql_error());
					if ($result) {
    					$num = mysql_numrows($result);
    					$i=0;
    					while ($i<$num){
      						$groep_id = mysql_result($result, $i, "id_groups");
							$q = "SELECT groupname FROM groups WHERE id='$groep_id'";
							$res = mysql_query($q) or die (mysql_error());
							$groepnaam = mysql_result($res, 0, "groupname");
							echo "<option value='$groepnaam'>$groepnaam</option>"; 
      						$i++;
						}
  					}
				*/ ?>
              </select>
              </font></p>
            <input name="txtAdmUser" type="hidden" value="<?php //echo $_POST['txtAdmUser']; ?>" >
            <input name="txtAdmPasw" type="hidden" value="<?php //echo $_POST['txtAdmPasw']; ?>" >
            <p> 
              <input name="btnAddTrip" type="submit" id="btnAddTrip" value="Submit">
            </p></div>
          </form>
		  -->
          
          <p>&nbsp;</p>
          <p align="left"><font face="Georgia, Times New Roman, Times, serif"><strong>Uitstappen</strong></font></p>
          <hr>
          <br>
          <?php
		  // get a list of all the group id's the admin is related to and go through them one by one
		  	// get admin id	
			$q = "SELECT id FROM admins WHERE user = '" . $_POST['txtAdmUser'] . "'";
			$result = mysql_query($q) or die (mysql_error());
			$admin_id = mysql_result($result, 0, "id");
			
			// get all groups associated with the admin
			$q = "SELECT DISTINCT id_groups FROM adminthathasgroups WHERE id_admins=$admin_id";
			$result = mysql_query($q) or die (mysql_error());
			$num = mysql_numrows($result);
			$i=0;
			$telhidden = 0;
			$teltrips = 0;
			$telgroups = 0;

			echo "<div id='accordionGroups'>";
			while ($i<$num){
				$athg_id = mysql_result($result, $i, "id_groups");
				// echo the name of the group
				$q_groupname = "SELECT groupname FROM groups WHERE id=$athg_id";
				$res_groupname = mysql_query($q_groupname) or die (mysql_error());
				$groupname = mysql_result($res_groupname, 0, "groupname");

				echo "<h3>$groupname </h3>";
				echo "<div>";
		  		// get a list of all the trip id's related to every group and go through them one by one
				$q_idtrips = "SELECT DISTINCT id_trips FROM groupthathastrips WHERE id_groups=$athg_id";
				$res_idtrips = mysql_query($q_idtrips) or die (mysql_error());
				$num2 = mysql_numrows($res_idtrips);
				$i2=0;
				
				echo "<div id='accordionG" . $telgroups . "'>";
				while ($i2<$num2) {
					$gtht_id = mysql_result($res_idtrips, $i2, "id_trips");
					// echo the name of the trip
					$q_tripname = "SELECT id, tripname, tripdate FROM trips WHERE id=$gtht_id";
					$res_tripname = mysql_query($q_tripname) or die (mysql_error());
					$tripdate = mysql_result($res_tripname, 0, "tripdate");
					$tripname = mysql_result($res_tripname, 0, "tripname");
					$trip_id = mysql_result($res_tripname, 0, "id");
					$telhidden++;
					
					
					//echo "<h4 onClick=\"hide('tripname" . $telhidden. "')\">$tripname - $tripdate</h4>";	//$i2 vervangen dr telhidden
					//echo "<div id='tripname" . $telhidden . "' style='display: none'>";						// idem
					echo "<h3>$tripname - $tripdate</h3>";
					echo "<div>";
					echo "<div id='tripname" . $telhidden . "'>";						// idem
																						// --> eens checken : deze div allicht overbodig?
																						
		  			// get a list of all students that didnt submit a question and list them
					$q_noquestion = "select user, id from students where id_groups = $athg_id and id not in (select id_students from questions where id_trips = $gtht_id)";
					$res_noquestion = mysql_query($q_noquestion) or die (mysql_error());
					$num_noquestion = mysql_numrows($res_noquestion);
					$i_noquestion = 0;
					if ($num_noquestion > 0) echo "<b><p>No question submitted:</p></b>";
					while ($i_noquestion <$num_noquestion) {
						$stud_user = mysql_result($res_noquestion, $i_noquestion, "user");
						echo "<li>$stud_user</li>";
						$i_noquestion++;
					}
					
					// show all questions by student related to this trip
					$q_questions = "select user, id from students where id_groups = $athg_id and id in (select id_students from questions where id_trips = $gtht_id)";
					$res_questions = mysql_query($q_questions) or die (mysql_error());
					$num_questions = mysql_numrows($res_questions);
					$i_questions = 0;
					if ($num_questions>0)echo "<br /><b><p>Questions submitted:</p></b>";
					echo "<div id='accordion" . $teltrips . "'>";
					while ($i_questions <$num_questions) {
						$stud_user = mysql_result($res_questions, $i_questions, "user");
						echo "<h3>$stud_user</h3>";
						echo "<div>";
							$stud_id = mysql_result($res_questions, $i_questions, "id");
						
							// get the questions and answers from questions where the stud id and trip id match
							$q_questansw = "SELECT id, question, answer FROM questions WHERE id_students=$stud_id AND id_trips=$trip_id";
							$res_questansw = mysql_query($q_questansw) or die (mysql_error());
							$num_questansw = mysql_numrows($res_questansw);
							$i_questansw = 0;
							while ($i_questansw < $num_questansw) {
								$question = mysql_result($res_questansw, $i_questansw, "question");
								$answer = mysql_result($res_questansw, $i_questansw, "answer");
								$question_id = mysql_result($res_questansw, $i_questansw, "id");
								echo "<p class='vraag'>$question</p><p class='answer'>$answer</p><br />";
								$i_questansw++;
								?>
             					<form name ="<?php echo 'comment' . $comment_teller; $comment_teller++; ?>" action="" method="post">
            					<br />
            					<table width="45%" border="2" cellpadding="0" cellspacing="0" bordercolor="#A4C1FF" bgcolor="#CCCCCC" style="margin-left:30 px;">
              					<?php
								// get every comment, commenter associated with this question and display in table
									$q_comment = "SELECT id, comment, commenter FROM comments WHERE id_questions=$question_id ORDER BY id";
									$res_comment = mysql_query($q_comment) or die (mysql_error());
									$num_comment = mysql_numrows($res_comment);
									$i_comment = 0;
									while ($i_comment < $num_comment) {
										$comment = mysql_result($res_comment, $i_comment, "comment");
										$commenter = mysql_result($res_comment, $i_comment, "commenter");
								?>
              								<tr> 
                								<td class = 'comment' height="21"><b><?php echo $commenter . " wrote: "; ?></b><?php echo $comment; ?></td>
              								</tr>
              					<?php 
										$i_comment++;
									}			
			   					?>
              						<tr> 
                						<td> <textarea name="txtComment" cols="70" rows="2"></textarea> 
                  						<input name="txtAdmUser" type="hidden" value="<?php echo $_POST['txtAdmUser']; ?>" > 
                  						<input name="txtAdmPasw" type="hidden" value="<?php echo $_POST['txtAdmPasw']; ?>" > 
                  						<input name="question_id" type="hidden" value="<?php echo $question_id; ?>" > 
                						</td>
              						</tr>
              						<tr> 
										<td><div align="center"><a href="javascript:submitcomment(<?php echo ($comment_teller-1); ?>)">Comment</a></div></td>
              						</tr>
            					</table>
          						</form>
          					<?php
							}
							echo "</div>";
							$i_questions++;
						}
						echo "</div>";
						$i2++;
						$teltrips++;
						echo "</div>";
						echo "</div>";					
					}
					echo "</div>";
					$telgroups++;
					$i++;
					echo "</div>";
					//echo "</div>";
				}
				echo "</div>";
		 	} else { 
				echo "Logingegevens niet correct"; 
			} 
			DisConnect($c);
			?>
			</p>
          <!-- </blockquote> -->
        <p>&nbsp;</p></td>
    </tr>
  </table>
</div>
</body>
<script language="javascript" type="text/javascript">
  $(window).load(function() {
    $('#loading').hide();
  });
</script>
</html>
