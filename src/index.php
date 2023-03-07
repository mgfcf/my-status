<?php
// Temporary error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("config.php");
require_once("storage.php");
require_once("activity.php");
require_once("http.php");

function loadGivenParameters(Activity $activity): void
{
    if (isset($_GET["title"])) {
        $activity->title = $_GET["title"];
    }
    if (isset($_GET["description"])) {
        $activity->description = $_GET["description"];
    }
    if (isset($_GET["duration"])) {
        $activity->expectedDuration = (int)$_GET["duration"];
    }
    if (isset($_GET["available"])) {
        $activity->available = filter_var($_GET["available"], FILTER_VALIDATE_BOOLEAN);
    }
    if (isset($_GET["working"])) {
        $activity->working = filter_var($_GET["working"], FILTER_VALIDATE_BOOLEAN);
    }
    if (isset($_GET["template"])) {
        $activity->template = $_GET["template"];
    }
}

function handleTemplate(Activity $activity)
{
    // Load and find template
    try {
        $templates = loadTemplates();
    } catch (Exception $e) {
        respondAndDie($e->getMessage());
    }

    $template = $templates->findValidTemplate($activity);
    if ($template == null) {
        // No applicable template found
        return;
    }

    // Apply template
    $template->applyTo($activity);

    // Overwrite parameters if template was specified on activity
    if ($activity->template != "") {
        loadGivenParameters($activity);
    }
}

// If no secret key is provided, respond with the current activity
global $SECRET_KEY;
if (!isset($_GET["secret"]) || $_GET["secret"] != $SECRET_KEY) {
    // Respond with the current activity
    try {
        $activity = loadActivity();
    } catch (Exception $e) {
        respondAndDie($e->getMessage());
    }
    handleTemplate($activity);
    respondWithJson($activity->getPublicData());
}

// Update activity
$activity = new Activity();
loadGivenParameters($activity);
storeActivity($activity);
respondAndDie("Activity updated");
