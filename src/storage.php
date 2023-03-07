<?php
require_once("config.php");
require_once("templates.php");
require_once("activity.php");

function storeActivity(Activity $activity): void
{
    global $ACTIVITY_FILE;
    storeObject($activity, $ACTIVITY_FILE);
}

function loadActivity(): Activity
{
    global $ACTIVITY_FILE;
    return loadObject($ACTIVITY_FILE, Activity::class);
}

function storeTemplates(Templates $templates): void
{
    global $TEMPLATES_FILE;
    storeObject($templates, $TEMPLATES_FILE);
}

function loadTemplates(): Templates
{
    global $TEMPLATES_FILE;
    if (!file_exists($TEMPLATES_FILE)) {
        // Create initial template file
        $templates = new Templates();
        $templates->loadDefaultTemplates();
        storeTemplates($templates);
        return $templates;
    }

    return loadObject($TEMPLATES_FILE, Templates::class);
}

function storeObject(object $object, string $file): void
{
    $file = fopen($file, "w");
    fwrite($file, json_encode($object, JSON_PRETTY_PRINT));
    fclose($file);
}

/**
 * @throws ReflectionException
 * @throws JsonException
 */
function loadObject(string $file, string $class): object
{
    $json = file_get_contents($file);
    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    return parseObject($data, $class);
}

/**
 * @throws ReflectionException
 */
function parseObject(array $data, string $class): object
{
    $reflection = new ReflectionClass($class);
    $instance = $reflection->newInstanceWithoutConstructor();
    foreach ($data as $property => $value) {
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($instance, $value);
    }

    try {
        $instance->initialLoad();
    } catch (Exception $e) {
        // Ignore
    }

    return $instance;
}
