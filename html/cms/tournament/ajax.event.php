<?php

require(dirname(__DIR__) . '/includes/config.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

switch(filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING)) {

    case "editEvent":
        $eventQuery = $dbm->query("SELECT e.id, e.title, e.location, e.date, e.active, f.id as formatId FROM events e JOIN eventToFormat etf ON etf.eventId = e.id JOIN formats f ON f.id = etf.formatId WHERE e.id = '$id' ");
        $event = $eventQuery->fetch(PDO::FETCH_ASSOC);

        $deckQuery = $dbm->query("SELECT d.* FROM decks d JOIN deckToEvent dte ON dte.deckId = d.id JOIN events e ON e.id = dte.eventId WHERE e.id = '$id' ");

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
?>
        <form id="eventForm" action="../tournament/update.php?fd=<?=$event['id']?>" method="post" onsubmit="return xhrHandler(this)" target="_blank">

            <h1>Event Details</h1>
                <h2>Title</h2>
                    <input type="text" name="title" value="<?=$event['title']?>">
                <h2>Location</h2>
                    <input type="text" name="location" value="<?=$event['location']?>">
                <h2>Date</h2>
                    <input type="text" name="date" value="<?=$event['date']?>">
                <h2>Format</h2>
                    <select name="format">
                        <option value="">Select Format</option>
<?php					
                        foreach($formats as $key=>$value) {
?>					
                            <option value="<?=$key?>" <?=$event['formatId'] == $key ? 'selected="selected"' : ""?>><?=$value?></option>
<?php
                        }
?>
                    </select>
<?php
            $count = 0;
            while($row = $deckQuery->fetch(PDO::FETCH_ASSOC)) {

                if($count == 0) {
                    $add = "";
                } else {
                    $add = $count;
                }

                $count++;

                $deckId = $row['id'];

                $cardQuery = $dbm->query("SELECT c.id, c.name, ctd.quantity, ctd.sideboard FROM cards c JOIN cardToDeck ctd ON ctd.cardId = c.id JOIN decks d ON d.id = ctd.deckId WHERE d.id = '$deckId' ");
                $mainBoard = "";
                $sideboard = "";
                while($cards = $cardQuery->fetch(PDO::FETCH_ASSOC)) {
                    if($cards['sideboard'] == '1') {
                        $sideboard .= $cards['quantity'] . " " . $cards['name'] . "\n";
                    } else {
                        $mainBoard .= $cards['quantity'] . " " . $cards['name'] . "\n";
                    }
                }
?>            
                <div id="deck">
                    <h1>Deck Details</h1>
                        <h2>Title</h2>
                            <input type="text" name="<?='deckTitle' . $add?>" value="<?=$row['title']?>">
                        <h2>Pilot</h2>
                            <input type="text" name="<?='pilot' . $add?>" value="<?=$row['pilot']?>">
                        <h2>Place</h2>
                            <input type="text" name="<?='place' . $add?>" value="<?=$row['place']?>">
                        <h2>Archetype</h2>
                            <select name="<?='archetype' . $add?>">
                                <option value="">Select Archetype</option>
<?php					
                                foreach($arch as $key=>$value) {
?>					
                                    <option value="<?=$key?>" <?=$row['archId'] == $key ? 'selected="selected"' : ""?>><?=$value?></option>
<?php
                                }
?>
                            </select>

                    <h1>Main Deck</h1>
                        <textarea name="<?='cardList' . $add?>"><?=$mainBoard?></textarea>
                    <h1>Sideboard</h1>
                        <textarea name="<?='sideboard' . $add?>"><?=$sideboard?></textarea>

                    <input type="hidden" name="<?='deckId' . $add?>" value="<?=$row['id']?>">

                </div>
<?php            
            }
?>
            <input type="hidden" id="deckNumber" name="deckNumber" value="<?=$deckQuery->rowCount() - 1?>">
        </form>
        <input type="submit" form="eventForm" value="Update Deck">
<?php
    break;
    
}