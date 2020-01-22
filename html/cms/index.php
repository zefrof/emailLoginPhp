<?php
//include config
require_once('includes/config.php');

//check if already logged in move to home page
if( $user->is_logged_in() ) { header('Location: index.php'); exit(); }

//process login form if submitted
if(isset($_POST['submit'])) {

	if (!isset($_POST['username'])) $error[] = "Please fill out all fields";
	if (!isset($_POST['password'])) $error[] = "Please fill out all fields";

	$username = $_POST['username'];
	if($user->isValidUsername($username)) {
		if(!isset($_POST['password'])) {
			$error[] = 'A password must be entered';
		}
		$password = $_POST['password'];

		if($user->login($username,$password)) {
			$_SESSION['username'] = $username;
			header('Location: dash.php');
			exit;

		} else {
			$error[] = 'Wrong username or password or your account has not been activated.';
		}
	} else {
		$error[] = 'Usernames are required to be Alphanumeric, and between 3-16 characters long';
	}



}//end if submit

//define page title
$title = 'Login';

//include header template
require('includes/header.php'); 
?>

	
<div>
	<div>
	    <div>
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Please Login</h2>
				<p><a href='./'>Back to home page</a></p>
				<hr>

				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<p>'.$error.'</p>';
					}
				}

				if(isset($_GET['action'])){

					//check the action
					switch ($_GET['action']) {
						case 'active':
							echo "<h2>Your account is now active you may now log in.</h2>";
							break;
						case 'reset':
							echo "<h2>Please check your inbox for a reset link.</h2>";
							break;
						case 'resetAccount':
							echo "<h2>Password changed, you may now login.</h2>";
							break;
					}

				}

				
				?>

				<div>
					<input type="text" name="username" id="username" placeholder="User Name" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['username'], ENT_QUOTES); } ?>" tabindex="1">
				</div>

				<div>
					<input type="password" name="password" id="password" placeholder="Password" tabindex="3">
				</div>
				
				<div>
					<div>
						 <a href='reset.php'>Forgot your Password?</a>
					</div>
				</div>
				
				<hr>
				<div>
					<div><input type="submit" name="submit" value="Login" tabindex="5"></div>
				</div>
			</form>
		</div>
	</div>



</div>


<?php 
//include header template
require('includes/footer.php'); 
?>
