<?php 
require(dirname(__DIR__) . '/includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Tournament Entry';

$formatQuery = $dbm->query("SELECT * FROM `formats` ORDER BY `formats`.`name` ASC");

$formats = [];
while ($row = $formatQuery->fetch(PDO::FETCH_ASSOC)) {
    $formats[$row['id']] = $row['name'];
}

$archQuery = $dbm->query("SELECT * FROM `archetypes` ORDER BY `archetypes`.`name` ASC"); 

$arch = [];
while($row = $archQuery->fetch(PDO::FETCH_ASSOC)) {
    $arch[$row['id']] = $row['name'];
}

//include header template
require(dirname(__DIR__) . '/includes/header.php');
require(dirname(__DIR__) . '/includes/nav.php');
?>
    <div>
        <form id="eventForm" action="../tournament/commit.php" method="post" onsubmit="return xhrHandler(this)" target="_blank">
            <!--<textarea id="scrapeUrls" name="scrapeUrls" rows="10" cols="100" placeholder="Paste links here to enter MTG tournament results. Seperate multiple links by a comma."></textarea>-->

            <h1>Event Details</h1>
                <h2>Title</h2>
                    <input type="text" name="title" value="">
                <h2>Location</h2>
                    <input type="text" name="location" value="">
                <h2>Date</h2>
                    <input type="text" name="date" value="">
                <h2>Format</h2>
                    <select name="format">
                        <option value="">Select Format</option>
<?php					
                        foreach($formats as $key=>$value) {
?>					
                            <option value="<?=$key?>"><?=$value?></option>
<?php
                        }
?>
                    </select>

            <div id="deck">
                <h1>Deck Details</h1>
                    <h2>Title</h2>
                        <input type="text" name="deckTitle" value="">
                    <h2>Pilot</h2>
                        <input type="text" name="pilot" value="">
                    <h2>Place</h2>
                        <input type="text" name="place" value="">
                    <h2>Archetype</h2>
                        <select name="archetype">
                            <option value="">Select Archetype</option>
<?php					
                            foreach($arch as $key=>$value) {
?>					
                                <option value="<?=$key?>"><?=$value?></option>
<?php
                            }
?>
                        </select>

                <h1>Main Deck</h1>
                    <textarea name="cardList" placeholder="Enter cards newline seperated. Place the number of cards before the name. Ex: 3 Lightning Bolt \n 1 Cryptic Command"></textarea>
                <h1>Sideboard</h1>
                    <textarea name="sideboard" placeholder="Do the same as above here, but for the sideboard"></textarea>

            </div>

            <input type="hidden" id="deckNumber" name="deckNumber" value="0">
        </form>
        <input type="button" value="Add Deck" onclick="duplicate();">
        <input type="button" value="Clear" onclick="location.reload();">
        <input type="submit" form="eventForm" value="Submit Tournaments">
    </div>

<?php 
//include footer template
require(dirname(__DIR__) . '/includes/footer.php'); 
?>
