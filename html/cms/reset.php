<?php require('includes/config.php');

require(dirname(__DIR__) . '/cms/PHPMailer/src/SMTP.php');
require(dirname(__DIR__) . '/cms/PHPMailer/src/PHPMailer.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: memberpage.php'); exit(); }

//if form has been submitted process it
if(isset($_POST['submit'])){

	//Make sure all POSTS are declared
	if(!isset($_POST['email'])) $error[] = "Please fill out all fields";


	//email validation
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	    $error[] = 'Please enter a valid email address';
	} else {
		$stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(empty($row['email'])) {
			$error[] = 'Email provided is not recognised.';
		}

	}

	//if no errors have been created carry on
	if(!isset($error)) {

		//create the activation code
		$stmt = $db->prepare('SELECT password, email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$token = hash_hmac('SHA256', $user->generate_entropy(8), $row['password']);//Hash and Key the random data
        $storedToken = hash('SHA256', ($token));//Hash the key stored in the database, the normal value is sent to the user

		try {

			$stmt = $db->prepare("UPDATE members SET resetToken = :token, resetComplete='No' WHERE email = :email");
			$stmt->execute(array(
				':email' => $row['email'],
				':token' => $storedToken
			));

			//send email
			$to = $row['email'];
			$subject = "Password Reset";
			$body = "<p>Someone requested that the password be reset.</p>
			<p>If this was a mistake, just ignore this email and nothing will happen.</p>
			<p>To reset your password, visit the following address: <a href='18.191.250.14/cms/resetPassword.php?key=$token'>18.191.250.14/cms/resetPassword.php?key=$token</a></p>";

			$mail = new PHPMailer\PHPMailer\PHPMailer();
			$mail->isSMTP();
			$mail->SMTPDebug = 2;
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;
			$mail->SMTPSecure = 'tls';
			$mail->SMTPAuth = true;
			$mail->Username = "toastersquadgaming@gmail.com";
			$mail->Password = "+U6]9_xYxvvcuvin28na";
			$mail->setFrom('toastersquadgaming@gmail.com', 'Toaster Squad');
			$mail->addAddress($to, 'User');
			$mail->Subject = $subject;
			$mail->msgHTML($body);

			if (!$mail->send()) {
				echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
				echo "Message sent!";
				//Section 2: IMAP
				//Uncomment these to save your message in the 'Sent Mail' folder.
				#if (save_mail($mail)) {
				#    echo "Message saved!";
				#}
			}

			//redirect to index page
			header('Location: login.php?action=reset');
			exit;

		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}
	}
}

//define page title
$title = 'Reset Account';

//include header template
require('includes/header.php');
?>

<div>

	<div>

	    <div>
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Reset Password</h2>
				<p><a href='login.php'>Back to login page</a></p>
				<hr>

				<?php
				//check for any errors
				if(isset($error)) {
					foreach($error as $error) {
						echo '<p>'.$error.'</p>';
					}
				}

				if(isset($_GET['action'])) {

					//check the action
					switch ($_GET['action']) {
						case 'active':
							echo "<h2>Your account is now active you may now log in.</h2>";
							break;
						case 'reset':
							echo "<h2>Please check your inbox for a reset link.</h2>";
							break;
					}
				}
				?>

				<div>
					<input type="email" name="email" id="email" placeholder="Email" value="" tabindex="1">
				</div>

				<hr>
				<div>
					<div><input type="submit" name="submit" value="Sent Reset Link" tabindex="2"></div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
//include header template
require('includes/footer.php');
?>
