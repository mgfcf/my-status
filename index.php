<?php

// Config
$SECRET_KEY = "ENTER_SECRET_HERE";
$FILENAME = "last_activity.txt";
$IDLE_DURATION_THRESHOLD = 60 * 30;    // In seconds
$SLEEP_DURATION_THRESHOLD = 60 * 60 * 4;    // In seconds
$EXPECTED_SLEEP_DURATION = 60 * 60 * 9.5;    // In seconds


function storeTime($time)
{
    global $FILENAME;
    $file = fopen($FILENAME, "w");
    fwrite($file, $time);
    fclose($file);
}

function getTime()
{
    global $FILENAME;
    $file = fopen($FILENAME, "r");
    $time = fread($file, filesize($FILENAME));
    fclose($file);
    return $time;
}


// Is secret given?
if (isset($_GET['secret']) && $_GET['secret'] == $SECRET_KEY) {
    // Update the file
    storeTime(time());
    echo "Last activity udpated";
    die();
}

// Current status requested
$last_activity = getTime();
$response = array(
    "last_activity" => date(DATE_ATOM, $last_activity)
);

// Estimate current activity
$now = time();
$diff = $now - $last_activity;  // DIFFERENCE IN SECONDS
if ($diff >= $SLEEP_DURATION_THRESHOLD) {
    $response['status'] = "Sleeping";
} else if ($diff >= $IDLE_DURATION_THRESHOLD) {
    $response['status'] = "Idle";
} else {
    $response['status'] = "Active";
}

// Estimated time until wake up
if ($response['status'] == "Sleeping") {
    $response['time_until_wake'] = $EXPECTED_SLEEP_DURATION - $diff;
} else {
    $response['time_until_wake'] = 0;
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode($response);


