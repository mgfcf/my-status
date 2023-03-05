<?php
require_once("config.php");

function storeActivity(Activity $activity): void
{
    global $STORAGE_FILE;
    $file = fopen($STORAGE_FILE, "w");
    fwrite($file, json_encode($activity));
    fclose($file);
}

function loadActivity(): Activity
{
    global $STORAGE_FILE;
    $file = fopen($STORAGE_FILE, "r");
    $line = fgets($file);
    fclose($file);
    return json_decode($line);
}
