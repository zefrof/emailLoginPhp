<?php

#TRUNCATE TABLE `cardToDeck`;
#TRUNCATE TABLE decks;
#TRUNCATE TABLE deckToEvent;
#TRUNCATE TABLE events;
#TRUNCATE TABLE eventToFormat; 
#TRUNCATE TABLE archetypeToDeck;

require(dirname(__DIR__) . '/includes/config.php');

$curTime = time();

$title = trim($_POST['title']);
$location = trim($_POST['location']);
$date = trim($_POST['date']);

//Insert tournament into DB
$stmt = $dbm->prepare("INSERT INTO events (title, `location`, `date`, dateAdded) VALUES (:title, :loc, :date, :dateAdded)");
$stmt->execute(array(
    ':title' => $title,
    ':loc' => $location,
    ':date' => $date,
    ':dateAdded' => $curTime
));

$fd = $dbm->lastInsertId();

$stmt = $dbm->prepare("INSERT INTO eventToFormat (eventId, formatId) VALUES (:eventId, :formatId)");
$stmt->execute(array(
    ':eventId' => $fd,
    ':formatId' => $_POST['format']
));

//Insert decks in DB

for($i = 0; $i <= $_POST['deckNumber']; $i += 1) {

    if($i == 0) {
        $add = "";
    } else {
        $add = $i;
    }
    
    $deckTitle = trim($_POST['deckTitle' . $add]);
    $pilot = trim($_POST['pilot' . $add]);
    $place = trim($_POST['place' . $add]);

    $stmt = $dbm->prepare("INSERT INTO decks (title, pilot, place, dateAdded) VALUES (:title, :pilot, :place, :dateAdded)");
    $stmt->execute(array(
        ':title' => $deckTitle,
        ':pilot' => $pilot,
        ':place' => $place,
        ':dateAdded' => $curTime
    ));

    $fp = $dbm->lastInsertId();

    $stmt = $dbm->prepare("INSERT INTO archetypeToDeck (archetypeId, deckId) VALUES (:archetype, :deck)");
    $stmt->execute(array(
        ':archetype' => $_POST['archetype' . $add],
        ':deck' => $fp
    ));

    $stmt = $dbm->prepare("INSERT INTO deckToEvent (deckId, eventId) VALUES (:deck, :eventId)");
    $stmt->execute(array(
        ':deck' => $fp,
        ':eventId' => $fd
    ));

    //echo $_POST['cardList' . $add];

    $cards = explode("\n", $_POST['cardList' . $add]);

    foreach($cards as $card) {

        $amount = preg_replace('/\D/', '', trim($card));
        $cardName = preg_replace('/\d/', '', trim($card));
        $cardName = trim($cardName);

        //echo $amount . " " . $cardName . "|";

        $stmt = $dbm->prepare("SELECT id FROM `cards` WHERE `name` = :cardName");
        $stmt->bindParam(":cardName", $cardName);
        $stmt->execute();

        //echo $stmt->rowCount();

        if($stmt->rowCount() > 0) {
            $cardId = $stmt->fetch(PDO::FETCH_ASSOC);
            //echo $cardId['id'];
        } else {
            $cardName = '%' . $cardName . '%';

            $stmt = $dbm->prepare("SELECT * FROM `cards` WHERE `name` LIKE :cardName AND name LIKE '%//%' ");
            $stmt->bindParam(":cardName", $cardName);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $cardId = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                echo $cardName . " not found!";
            }
        }

        $stmt = $dbm->prepare("INSERT INTO cardToDeck (cardId, deckId, quantity, sideboard) VALUES (:cardId, :deckId, :quantity, '0')");
        $stmt->execute(array(
            ':cardId' => $cardId['id'],
            ':deckId' => $fp,
            ':quantity' => $amount,
        ));
    }

    $sideboard = explode("\n", $_POST['sideboard' . $add]);

    foreach($sideboard as $card) {

        $amount = preg_replace('/\D/', '', trim($card));
        $cardName = preg_replace('/\d/', '', trim($card));
        $cardName = trim($cardName);

        //echo $amount . " " . $cardName . "|";

        $stmt = $dbm->prepare("SELECT id FROM `cards` WHERE `name` = :cardName");
        $stmt->bindParam(":cardName", $cardName);
        $stmt->execute();

        //echo $stmt->rowCount();

        if($stmt->rowCount() > 0) {
            $cardId = $stmt->fetch(PDO::FETCH_ASSOC);
            //echo $cardId['id'];
        } else {
            $cardName = '%' . $cardName . '%';

            $stmt = $dbm->prepare("SELECT * FROM `cards` WHERE `name` LIKE :cardName AND name LIKE '%//%' ");
            $stmt->bindParam(":cardName", $cardName);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $cardId = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                echo $cardName . " not found!";
            }
        }

        $stmt = $dbm->prepare("INSERT INTO cardToDeck (cardId, deckId, quantity, sideboard) VALUES (:cardId, :deckId, :quantity, '1')");
        $stmt->execute(array(
            ':cardId' => $cardId['id'],
            ':deckId' => $fp,
            ':quantity' => $amount,
        ));
    }

    echo $i;
}
