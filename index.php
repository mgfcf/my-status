<?php

// Config
$SECRET_KEY = "ENTER_SECRET_HERE";
$FILENAME = "last_activity.txt";
$SEPARATOR = " ";
$IDLE_DURATION_THRESHOLD = 60 * 30;    // In seconds
$SLEEP_DURATION_THRESHOLD = 60 * 60 * 3;    // In seconds
$EXPECTED_SLEEP_DURATION = 60 * 60 * 9.5;    // In seconds


function storeActivity($time, $activity = "")
{
    global $FILENAME, $SEPARATOR;
    $file = fopen($FILENAME, "w");
    fwrite($file, $time . $SEPARATOR . $activity);
    fclose($file);
}

function getActivity()
{
    global $FILENAME, $SEPARATOR;
    $file = fopen($FILENAME, "r");
    $line = fgets($file);
    fclose($file);
    $parts = explode($SEPARATOR, $line, 2);
    return array(
        "time" => (int)$parts[0],
        "activity" => $parts[1]
    );
}


// Is secret given?
if (isset($_GET['secret']) && $_GET['secret'] == $SECRET_KEY) {
    $activity = isset($_GET['activity']) ? $_GET['activity'] : "";

    // Update the file
    storeActivity(time(), $activity);
    echo "Last activity updated";
    die();
}

// Current status requested
$last_activity = getActivity();
$response = array(
    "last_activity" => $last_activity['time'],
    "status" => $last_activity['activity'],
);

// Estimate current activity
$now = time();
$diff = $now - $last_activity["time"];  // DIFFERENCE IN SECONDS

if ($response['status'] == "") {
    if ($diff >= $SLEEP_DURATION_THRESHOLD) {
        $response['status'] = "Sleeping";
    } else if ($diff >= $IDLE_DURATION_THRESHOLD) {
        $response['status'] = "Awake";
    } else {
        $response['status'] = "Busy";
    }
}

// Estimated time until wake up
if (strtolower($response['status']) == "sleeping") {
    $response['time_until_wake'] = $EXPECTED_SLEEP_DURATION - $diff;
} else {
    $response['time_until_wake'] = 0;
}

// Respond with JSON
header('Content-Type: application/json');
echo json_encode($response);


