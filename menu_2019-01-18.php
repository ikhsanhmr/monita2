<html>

<head>
	<title>Ini adalah file menu halaman web</title>
	<link rel="stylesheet" href="css/jquery.treeview.css">
	<link rel="stylesheet" type="text/css" href="css/main.css"/>
	<script src="js/modernizr-2.6.2.min.js"></script>
	<script src="js/jquery-1.9.0.min.js"></script>
	<script src="js/jquery.colorbox.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<base target="content">
	<?php 
		session_start();
		require_once 'config/koneksi.php';
	?>
</head>

<body>
	<div id="boxuser">
		<span class='logo'>
			<br>Monitoring Anggaran (Monita)
		</span>
		
		<span class='jargon1'>
			<a href="logout.php">Log Out</a>
		</span>
		
		<span class='jargon2'>
			Welcome, <?=$_SESSION['nama'];?><br>
		</span>
        
        <span class='menu'>

        	
    <!-- <a data-toggle="collapse" href="#collapse1">a</a>
        
      <div id="collapse1" class="panel-collapse collapse">
        <ul>
          <li>One</li>
          
        </ul>
      </div> -->

			<div id="sidetree">
				<div class="treeheader">&nbsp;</div>
				<br>
				<hr>
				<ul id="tree">
					<?php
						$roleid=$_SESSION["roleid"];
						$org=$_SESSION['org'];
						
						$mn = "";
						$sql = "
							SELECT m.* FROM menu m
							LEFT JOIN menurole mr ON m.menuid = mr.menuid
							WHERE roleid = $_SESSION[roleid] and isheader=1
							GROUP BY menugroup
							ORDER BY sortmenu";
						
						$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
						while ($row = mysqli_fetch_array($result)) {

								$mn .= ($mn==""? "": "</ul></li>"); 
								$mn .= "<li><a data-toggle='collapse' href='#$row[menugroup]'><strong>$row[menuinfo]</strong></a><ul>";
								$mn .= "<div id='$row[menugroup]' class='panel-collapse collapse'>";


								$sql1 = "
									SELECT m.* FROM menu m
									LEFT JOIN menurole mr ON m.menuid = mr.menuid
									WHERE roleid = $_SESSION[roleid]
									and m.menugroup='$row[menugroup]'
									and m.isheader='0'
									ORDER BY sortmenu";
								
								$result1 = mysqli_query($sql1);
								while ($row1 = mysqli_fetch_array($result1)) {

										if($row1["menugroup"]!=2){
											$mn .= "<li><a href='$row1[url]' class='iframe'>" . ($row1["alternateinfo"]==""? $row1["menuinfo"]: (($org>5 && $org<16)? $row1["alternateinfo"]: $row1["menuinfo"])) . "</a></li>";
										} else if($row1["menugroup"]==2) { 
											$mn .= "<li><a data-toggle='collapse' href='#$row1[url]'><strong>$row1[menuinfo]</strong></a><ul>";
											$mn .= "<div id='$row1[url]' class='panel-collapse collapse'>";

											$sql2 = "
											SELECT m.* FROM menu m
											LEFT JOIN menurole mr ON m.menuid = mr.menuid
											WHERE roleid = $_SESSION[roleid]
											and m.menugroup='$row1[url]'
											and m.isheader='0'
											ORDER BY sortmenu";
										
											$result2 = mysqli_query($sql2);
											while ($row2 = mysqli_fetch_array($result2)) {
												$mn .= "<li><a href='$row2[url]' class='iframe'>" . ($row2["alternateinfo"]==""? $row2["menuinfo"]: (($org>5 && $org<16)? $row2["alternateinfo"]: $row2["menuinfo"])) . "</a></li>";
											}
											$mn .= "</div></ul></li>";
										}


								}

								$mn .= "</div>";
						}
						mysqli_free_result($result);
						//$mysqli->close();($link);	  

						$mn .= "</ul></li>";
						echo $mn;
						//echo "<script>alert('$mn')</script>";
					?>
				</ul>
			</div>
		</span>
	</div>
	
	<div id='contentholder'>
		<div id='contentdiventrance'></div>
		
        <script src="js/jquery-1.9.1.min.js"></script>
		<script src="js/jquery.treeview.js"></script>

		<script type="text/javascript">
			$(function() {
				$("#tree").treeview({
					collapsed: false,
					animated: "medium",
					control:"#sidetreecontrol",
					persist: "location"
				});
			})		
		</script>



        <script>window.jQuery || document.write('<script src="js/"></script>')</script>
		
		<script type="text/javascript">
			$(function(){
				$("#username").focus();
			});
		</script>
        
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

	</div>
</body>
</html> 