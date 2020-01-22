<?php require('includes/config.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: dash.php'); exit(); }

//if form has been submitted process it
if(isset($_POST['submit'])){

    if (!isset($_POST['username'])) $error[] = "Please fill out all fields";
    if (!isset($_POST['email'])) $error[] = "Please fill out all fields";
    if (!isset($_POST['password'])) $error[] = "Please fill out all fields";

	$username = $_POST['username'];

	//very basic validation
	if(!$user->isValidUsername($username)) {
		$error[] = 'Usernames must be at least 3 Alphanumeric characters';
	} else {
		$stmt = $db->prepare('SELECT username FROM members WHERE username = :username');
		$stmt->execute(array(':username' => $username));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['username'])) {
			$error[] = 'Username provided is already in use.';
		}

	}

	if(strlen($_POST['password']) < 3) {
		$error[] = 'Password is too short.';
	}

	if(strlen($_POST['passwordConfirm']) < 3) {
		$error[] = 'Confirm password is too short.';
	}

	if($_POST['password'] != $_POST['passwordConfirm']) {
		$error[] = 'Passwords do not match.';
	}

	//email validation
	$email = htmlspecialchars_decode($_POST['email'], ENT_QUOTES);
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	    $error[] = 'Please enter a valid email address';
	} else {
		$stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $email));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['email'])) {
			$error[] = 'Email provided is already in use.';
		}

	}


	//if no errors have been created carry on
	if(!isset($error)) {

		//hash the password
		$hashedpassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

		//create the activasion code
		$activasion = md5(uniqid(rand(),true));

		try {

			//insert into database with a prepared statement
			$stmt = $db->prepare('INSERT INTO members (username,password,email,active) VALUES (:username, :password, :email, :active)');
			$stmt->execute(array(
				':username' => $username,
				':password' => $hashedpassword,
				':email' => $email,
				':active' => 1
			));
			$id = $db->lastInsertId('id');

			//send email
			$to = $_POST['email'];
			$subject = "Registration Confirmation";
			$body = "<p>Thank you for registering at demo site.</p>
			<p>To activate your account, please click on this link: <a href='".DIR."activate.php?x=$id&y=$activasion'>".DIR."activate.php?x=$id&y=$activasion</a></p>
			<p>Regards Site Admin</p>";

			$mail = new Mail();
			$mail->setFrom(SITEEMAIL);
			$mail->addAddress($to);
			$mail->subject($subject);
			$mail->body($body);
			$mail->send();

			//redirect to index page
			header('Location: index.php?action=joined');
			exit;

		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}

	}

}

//define page title
$title = 'CMS';

//include header template
require('includes/header.php');
?>


<div class="container centerParent">

	<div class="eightyPerc centerChild">

	    <div>
			<form role="form" method="post" action="" autocomplete="off">
                <div class= "mainBG">
				    <h2>Please Sign Up</h2>
				    <p>Already a member? <a href='login.php'>Login</a></p>
				</div>

				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}

				//if action is joined show sucess
				if(isset($_GET['action']) && $_GET['action'] == 'joined'){
					echo "<h2>Registration successful.</h2>";
				}
				?>
                <div class="padded">
				    <div>
					   <input type="text" name="username" id="username" placeholder="User Name" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['username'], ENT_QUOTES); } ?>" tabindex="1">
				    </div>
				    <div>
					   <input type="email" name="email" id="email" placeholder="Email Address" value="<?php if(isset($error)){ echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>" tabindex="2">
				    </div>
				    <div>
					   <div>
						  <div>
							 <input type="password" name="password" id="password" placeholder="Password" tabindex="3">
						  </div>
					   </div>
					   <div>
						  <div>
							 <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirm Password" tabindex="4">
						  </div>
					   </div>
				    </div>

				    <div>
					   <!--<div><input type="submit" name="submit" value="Register" tabindex="5"></div> No new users right now-->
				    </div>
                </div>
			</form>
		</div>
	</div>
<!--copyright date-->
    <footer>© <?php echo date(Y) ?> Darren and Justin Wiltse</footer>
</div>

<?php
//include header template
require('includes/footer.php');
?>
