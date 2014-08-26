<?php
session_start();

include_once "credentials.php";
include_once "functions.php";

try {
    $dbh = new PDO($db, $user, $pass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br />";
    die();
}

// Set the correct content-type
header('Content-Type: text/event-stream');
// Ensure the stream isn't cached
header('Cache-Control: no-cache');

$uid = $_REQUEST["uid"];
// setlocale(LC_ALL, 'en_GB');
// date_default_timezone_set('Europe/London');
$lastUpdate = time();
$startedAt = time();

// A quirk of PHP is that the session is single threaded; if you leave
// it open in this script, it'll block any other pages using it.
//Note: IRL, should lock this down by IP address too
session_write_close();

while (is_logged_on($dbh, $uid)) {
    // Loop here until the user logs out. Nearly all web server configurations
    // limit execution time between 30 and 90 seconds to allow the script to
    // time out, but the browser will automatically reconnect.
    // In a real application, you'd refactor this inline SQL into a function.
    // This example tries to keep all the logic visible.
    //IRL you'd use the same functions as being used to build the initial page here
    $getChat = $dbh->prepare('SELECT `timestamp`, `handle`, `message` FROM `log` WHERE `timestamp` >= :lastupdate ORDER BY `timestamp`');
    $getChat->execute(array(':lastupdate' => strftime("%Y-%m-%d %H:%M:%S", $lastUpdate)));

    // Fetch all chat messages added to the database since the last update;
    // to keep things simple you'll worry about only the message event for now.
    $rows = $getChat->fetchAll();
    foreach ($rows as $row) {
        echo "event: message\n";
        // Send the data as HTML. you could also send it as JSON-encoded object.
        echo "data: <time datetime=\"".$row['timestamp']."\">".strftime("%H:%M", strtotime($row['timestamp']))."</time> <b>".$row['handle']."</b> <span>".$row['message']."</span>\n\n";
        ob_flush();
        flush();
    }

    //The client should reconnect when terminated, most servers are configured to limit script execution time to between 30-90 seconds
    if ((time() - $startedAt) > 60) {
        session_start();
        die();
    }

    $lastUpdate = time();
    // Stores the last time you updated, and sleeps for two seconds. This is necessary in
    // in this example because the MySQL timestamp column is only accurate to the closeset
    // second. Implementing a millisecond-accurate time field in MySQL is possible but
    // has been avoided here to keep the code simple.
    //IRL, don't use timestamps
    sleep(2);
}
?>