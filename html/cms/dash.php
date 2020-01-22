<?php 
require(dirname(__FILE__) . '/includes/config.php'); 

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Dashboard';

//include header template
require(dirname(__FILE__) . '/includes/header.php');
require(dirname(__FILE__) . '/includes/nav.php');
?>

<div>
	<div>
	    <div>
            <h2>Welcome <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES); ?></h2>
            <p><a href='logout.php'>Logout</a></p>
            <hr>
		</div>
	</div>
</div>

<?php 
//include footer template
require(dirname(__FILE__) . '/includes/footer.php'); 
?>
