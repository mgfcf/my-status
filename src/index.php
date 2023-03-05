<?php
require_once("config.php");
require_once("storage.php");
require_once("activity.php");
require_once("http.php");

// If no secret key is provided, respond with the current activity
global $SECRET_KEY;
if (!isset($_GET["secret"]) || $_GET["secret"] != $SECRET_KEY) {
    // Respond with the current activity
    $activity = loadActivity();
    respondWithJson($activity->getPublicData());
}

// Update activity
$activity = new Activity();
$activity->startTime = time();


if (isset($_GET["title"])) {
    $activity->title = $_GET["title"];
} else {
    $activity->title = "Awake";
}
if (isset($_GET["description"])) {
    $activity->description = $_GET["description"];
} else {
    $activity->description = "";
}
if (isset($_GET["duration"])) {
    $activity->expectedDuration = (int)$_GET["duration"];
} else {
    $activity->expectedDuration = 0;
}
if (isset($_GET["available"])) {
    $activity->available = filter_var($_GET["available"], FILTER_VALIDATE_BOOLEAN);
} else {
    $activity->available = false;
}
if (isset($_GET["working"])) {
    $activity->working = filter_var($_GET["working"], FILTER_VALIDATE_BOOLEAN);
} else {
    $activity->working = false;
}

storeActivity($activity);
respondAndDie("Activity updated");
