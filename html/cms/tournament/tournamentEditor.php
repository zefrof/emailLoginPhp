<?php 
require(dirname(__DIR__) . '/includes/config.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); exit(); }

//define page title
$title = 'Tournament Editor';

$formatQuery = $dbm->query("SELECT * FROM `formats` ORDER BY `formats`.`name` ASC");

$formats = [];
while ($row = $formatQuery->fetch(PDO::FETCH_ASSOC)) {
    $formats[$row['id']] = $row['name'];
}

#SELECT d.* FROM decks d JOIN deckToEvent de ON de.deckId = d.id WHERE de.eventId =

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$records = 20;
$offset = ($page - 1) * $records;

$pageQuery = $dbm->query("SELECT COUNT(*) FROM events");
$temp = $pageQuery->fetch(PDO::FETCH_ASSOC);
$totalRows = $temp['COUNT(*)'];
$totalPages = ceil($totalRows / $records);

$eventQuery = $dbm->query("SELECT e.*, f.name as `format` FROM `events` e JOIN eventToFormat etf ON etf.eventId = e.id JOIN formats f ON f.id = etf.formatId ORDER BY `e`.`date`, `e`.`title` ASC LIMIT $offset, $records");

//include header template
require(dirname(__DIR__) . '/includes/header.php');
require(dirname(__DIR__) . '/includes/nav.php');

?>
<div id="content">

    <div class="tblHead">
        <div class="headCol">Name</div>
        <div class="headCol">Date</div>
        <div class="headCol">Location</div>
        <div class="headCol">Format</div>
    </div>

<?php

    while($row = $eventQuery->fetch(PDO::FETCH_ASSOC)){
?>
        <div class="row">
            <div class="col"><input type="button" value="<?=$row['title']?>" onclick="deckAjax(<?=$row['id']?>);"></div>
            <div class="col"><?=$row['date']?></div>
            <div class="col"><?=$row['location']?></div>
            <div class="col"><?=$row['format']?></div>
        </div>
<?php
}

?>
        <button><a href="<?php if($page <= 1){ echo '#'; } else { echo "?page=".($page - 1); } ?>">Previous</a></button>
        <button><a href="<?php if($page >= $totalPages){ echo '#'; } else { echo "?page=".($page + 1); } ?>">Next</a></button>
</div>
<?php

//include footer template
require(dirname(__DIR__) . '/includes/footer.php'); 
?>