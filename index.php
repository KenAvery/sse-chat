<?php
// This enables the standard PHP session tracking.
session_start();
// Common variables and functions are included from separate files.
include_once "credentials.php";
include_once "functions.php";

try {
    // You'll be using PHP Data Objects (PDO) to connect to the database
    $dbh = new PDO($db, $user, $pass);
} catch (PDOException $e) {
    print "Error!: ".$e->getMessage()."<br />";
    die();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>SSE Chat</title>
        <link href="css/style.css" rel="stylesheet">
        <script src="_js/jquery-1.7.1.min.js"></script>
        <!-- <script src="_js/raf-polyfill.js></script> -->
        <!-- Make the PHP Session ID easily available to JavaScript (saves reading the cookie). -->
        <script>var uid =   '<?php print session_id(); ?> ' ;</script>
        <!-- chat.js is the file where you'll later implement the client-side code for SSE -->
        <script src="_js/chat.js"></script>
</head>
<body>
    <?php
try {
// Lookup all the sessions in the database with a session_id equal to the corrent session_id().
$checkOnline = $dbh->prepare('SELECT * FROM sessions WHERE session_id = :sid');
$checkOnline->execute(array(':sid' => session_id()));
$rows = $checkOnline->fetchAll();
} catch (PDOException $e) {
print "Error!: " . $e->getMessage() . "<br />";
die ();
}

// If one is found, assume the user is logged in. (This is intended to be the simplest
// code that will work - it's not best practice, sercure PHP).
if (count($rows) > 0) {
?>
    <strong>Online now:</strong>
    <ul class="chatusers">
        <!-- The print_user_list function outputs ah unordered list (the HTML element) of currently logged-on users. -->
        <?php print_user_list($dbh); ?>
    </ul>
    <div class="chatwindow">
        <ul class="chatlog">
            <!-- The print_chat_lot function outputs an ordered list of chat messages. -->
            <?php print_chat_log($dbh); ?>
        </ul>
    </div>
    <!-- The chatform has an action defined that allows it to work, in a limited sense, without JavaScript -->
    <!-- enabled, but JavaScript will be used to override the default action in listing 4.6 -->
    <form id="chat" class="chatform" method="post" action="add-chat.php">
        <label for="message">Share your thoughts:</label>
        <input name="message" id="message" maxlength="512" autofocus>
        <input type="submit" value="Chat">
        </form>
        <?php
        } else {
        ?>
    <form id="login" class="chatlogin" method="post" action="add-session.php">
        <!-- The add-session.php file will deal with inserting the user into the database -->
        <label for="handle">Enter your handle</label>
        <input name="handle" id="handle" maxlength="127" autofocus>
        <input type="submit" value="Join">
    </form>
    <?php
    }
?>
</body>
</html>
<?php $dbh = null; ?>