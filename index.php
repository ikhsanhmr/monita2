<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Monita</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script src="js/modernizr.custom.63321.js"></script>
	<script type="text/javascript">
		function mylogin() {
			if (document.getElementById("nip").value == "") {
				return false;
			}
			if (document.getElementById("password").value == "") {
				return false;
			}
		}
	</script>

	<!--<script type="text/javascript">
			function mylogin() {
				var nip      = document.getElementById("nip").value;
				var password = document.getElementById("password").value;		
				if (nip != "" && password!="") {
					return true;
				}else{
					alert('Nip dan Password harus di isi !');
					return false;
				}
			}		 
		</script>-->

	<!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->
	<style>
		body {
			background: #e1c192 url(images/wood_pattern.jpg);
		}
	</style>
</head>

<body>

	<div class="container">
		<header>
			<h1> <strong>Monita</strong></h1>
			<h2>Monitoring Anggaran</h2>
		</header>
		<section class="main">
			<form class="form-2" method="post" action="ceklogin.php" onsubmit="return mylogin()">
				<h1><span class="log-in">Log in</span> or <span class="sign-up">sign up</span></h1>
				<p class="float">
					<label for="login"><i class="icon-user"></i>NIP</label>
					<input type="text" name="nip" id="nip" placeholder="NIP" value="">
				</p>
				<p class="float">
					<label for="password"><i class="icon-lock"></i>Password</label>
					<input type="password" name="password" id="password" placeholder="Password" class="showpassword" value="">
				</p>
				<p>
					<input type="submit" name="submit" value="Log in">
				</p>

				</p>
			</form>
			<p align="center">Manual Book Dapat Didownload di<a href="images/Manual Book Monita.pdf" target="_blank">Sini</a></p>
		</section>

	</div>
	<!-- jQuery if needed -->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript">
		$(function() {
			$(".showpassword").each(function(index, input) {
				var $input = $(input);
				$("<p class='opt'/>").append(
					$("<input type='checkbox' class='showpasswordcheckbox' id='showPassword' />").click(function() {
						var change = $(this).is(":checked") ? "text" : "password";
						var rep = $("<input placeholder='Password' type='" + change + "' />")
							.attr("id", $input.attr("id"))
							.attr("name", $input.attr("name"))
							.attr('class', $input.attr('class'))
							.val($input.val())
							.insertBefore($input);
						$input.remove();
						$input = rep;
					})
				).append($("<label for='showPassword'/>").text("Show password")).insertAfter($input.parent());
			});

			$('#showPassword').click(function() {
				if ($("#showPassword").is(":checked")) {
					$('.icon-lock').addClass('icon-unlock');
					$('.icon-unlock').removeClass('icon-lock');
				} else {
					$('.icon-unlock').addClass('icon-lock');
					$('.icon-lock').removeClass('icon-unlock');
				}
			});
		});
	</script>
</body>

</html>