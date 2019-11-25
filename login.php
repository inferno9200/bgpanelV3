<?php
$page = 'login';


require("configuration.php");
require("include.php");


$title = T_('Client Login');


if (isset($_COOKIE['clientLanguage']))
{
	$cookie = htmlspecialchars($_COOKIE['clientLanguage'], ENT_QUOTES);
	defineLanguage($cookie);
	unset($cookie);
}


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+


include("./bootstrap/header.php");


if (MAINTENANCE == 1)
{
?>
			<div class="alert alert-block">
				<h4 class="alert-heading"><?php echo T_('Maintenance Mode'); ?></h4>
				<?php echo T_('The panel is currently undergoing scheduled maintenance. Please try back in 60 minutes. Sorry for the inconvenience.'); ?>
			</div>
<?php
}
else
{
	if (!empty($_SESSION['lockout']) && ((time() - 60 * 10) < $_SESSION['lockout']))
	{
?>
			<div class="alert alert-block">
				<h4 class="alert-heading"><?php echo T_('Too Many Incorrect Login Attempts'); ?></h4>
				<?php echo T_('Please wait 10 minutes before trying again.'); ?>
			</div>
<?php
	}
	else
	{
		if (@$task == 'password')
		{
			if (!empty($_SESSION['success']) && ($_SESSION['success'] == 'Yes'))
			{
?>
			<div class="alert alert-success">
				<a class="close" data-dismiss="alert">&times;</a>
				<h4 class="alert-heading"><?php echo T_('Password Sent'); ?></h4>
				<?php echo T_('Your password has been reset and emailed to you.'); ?>
			</div>
<?php
			}
			else if (!empty($_SESSION['success']) && ($_SESSION['success'] == 'No'))
			{
?>
			<div class="alert alert-error">
				<a class="close" data-dismiss="alert">&times;</a>
				<h4 class="alert-heading"><?php echo T_('Fail!'); ?></h4>
				<?php echo T_('Your IP'); ?> ("<?php echo $_SERVER['REMOTE_ADDR']; ?>") <?php echo T_('has been logged and admins notified of this failed attempt.'); ?>
			</div>
<?php
				unset($_SESSION['success']);
			}
?>
			<div class="row">
				<div class="span4 offset4">
					<div class="well">
						<legend><?php echo T_('Client Lost Password'); ?></legend>
						<form action="loginprocess.php" method="post">
							<input type="hidden" name="task" value="processpassword" />
							<label><?php echo T_('Username'); ?> :</label>
							<div class="input-prepend">
								<span class="add-on"><i class="icon-user"></i></span>
								<input type="text" name="username" class="span3" placeholder="Login">
							</div>
							<label><?php echo T_('Email'); ?> :</label>
							<div class="input-prepend">
								<span class="add-on"><i class="icon-envelope"></i></span>
								<input type="text" name="email" class="span3" placeholder="Email">
							</div>
							<label>&nbsp;</label>
							<img class="img-polaroid" id="captcha" src="./captcha/securimage_show.php" alt="CAPTCHA Image" />
							<button class="btn btn-primary" type="button" onclick="document.getElementById('captcha').src = './captcha/securimage_show.php?' + Math.random(); return false"><i class="icon-retweet icon-white"></i></button>
							<label></label>
							<div class="input-prepend">
								<span class="add-on"><?php echo T_('Captcha'); ?></span>
								<input type="text" name="captcha_code" class="span2">
							</div>
							<div style="text-align: center; margin-top: 24px;">
								<button type="submit" class="btn btn-block btn-primary"><?php echo T_('Send Password'); ?></button>
							</div>
						</form>
						<ul class="pager">
							<li>
								<a href="login.php"><?php echo T_('Previous'); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
<?php
		}
		else
		{
			if (isset($_SESSION['loginerror']))
			{
?>
			<div class="alert alert-error">
				<a class="close" data-dismiss="alert">&times;</a>
				<h4 class="alert-heading"><?php echo T_('Login Failed'); ?></h4>
				<?php echo T_('Your IP'); ?> ("<?php echo $_SERVER['REMOTE_ADDR']; ?>") <?php echo T_('has been logged and admins notified of this failed attempt.'); ?>
			</div>
<?php
				unset($_SESSION['loginerror']);
			}
?>
			<div class="row">
				<div class="span4 offset4">
					<div class="well">
						<div style="text-align: center; margin-bottom: 24px;">
							<img src="./bootstrap/img/logo.png" alt="Bright Game Panel Logo">
						</div>
						<legend><?php echo T_('Sign In'); ?></legend>
						<form action="loginprocess.php" method="post">
							<input type="hidden" name="task" value="processlogin" />
							<input type="hidden" name="return" value="<?php
			if (isset($_GET['return']))
			{
				echo htmlspecialchars($_GET['return'], ENT_QUOTES);
			}
?>" />
							<label><?php echo T_('Username'); ?> :</label>
							<div class="input-prepend">
								<span class="add-on"><i class="icon-user"></i></span>
								<input type="text" name="username" class="span3" <?php
			if (isset($_COOKIE['clientUsername']))
			{
				$cookie = htmlspecialchars($_COOKIE['clientUsername'], ENT_QUOTES);
				echo "value=\"{$cookie}\"";
				unset($cookie);
			}
			else
			{
				echo "placeholder=\"Login\"";
			}
?>>
							</div>
							<label><?php echo T_('Password'); ?> :</label>
							<div class="input-prepend">
								<span class="add-on"><i class="icon-lock"></i></span>
								<input type="password" name="password" class="span3" placeholder="Password">
							</div>
							<label class="checkbox">
								<input type="checkbox" name="rememberMe" checked="checked"><?php echo T_('Remember Me'); ?>
							</label>
							<button class="btn btn-block btn-primary" type="submit"><?php echo T_('Login'); ?></button>
						</form>
						<ul class="pager">
							<li>
								<a href="login.php?task=password"><?php echo T_('Forgot Password?'); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
<?php
		}
	}
}


include("./bootstrap/footer.php");
?>