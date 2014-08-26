<?php
session_start();
include_once "credentials.php";

// Request var handle
try {
    print "DB: ".$db.", User: ".$user.", Password: ".$pass;
    $dbh = new PDO($db, $user, $pass);
    $preparedStatement = $dbh->prepare('INSERT INTO `sessions`(`session_id`, `handle`, `connected`) VALUES (:sid, :handle, NOW())');
    // You're not doing anything more complex than recording the submitted handle in the database with the session_id().
    $preparedStatement->execute(array(':sid' => session_id(), ':handle' => $_POST["handle"]));
    $rows = $preparedStatement->fetchAll();
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br />";
    die();
}
// Redirect to index.php when finished.
header("Location: index.php");
?>