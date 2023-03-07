<?php
require_once("storage.php");

class Templates
{
    public array $templates;

    public function initialLoad()
    {
        foreach ($this->templates as $templateId => $template) {
            $this->templates[$templateId] = parseObject($template, Template::class);
        }
    }

    public function existsTemplate(string $templateId): bool
    {
        // Does key in array exist?
        if (array_key_exists($templateId, $this->templates)) {
            return true;
        }

        return false;
    }

    public function findValidTemplate(Activity $activity): ?Template
    {
        if ($activity->template != "") {
            if ($this->existsTemplate($activity->template)) {
                return $this->templates[$activity->template];
            } else {
                return null;
            }
        }

        // Check for other validity conditions
        $validTemplate = null;
        foreach ($this->templates as $templateId => $template) {
            if ($template->isValidFor($activity) && ($validTemplate == null || $template->priority > $validTemplate->priority)) {
                $validTemplate = $template;
            }
        }

        return $validTemplate;
    }

    public function loadDefaultTemplates(): void
    {
        $awakeTemplate = new Template();
        $awakeTemplate->title = "Awake";
        $awakeTemplate->description = "";
        $awakeTemplate->expectedDuration = 0;
        $awakeTemplate->available = false;
        $awakeTemplate->working = false;
        $awakeTemplate->priority = 0;
        $awakeTemplate->triggerOnlyOnEmptyTitle = true;

        $this->templates["awake"] = $awakeTemplate;

        $sleepingTemplate = new Template();
        $sleepingTemplate->title = "Sleeping";
        $sleepingTemplate->description = "";
        $sleepingTemplate->expectedDuration = 60 * 60 * 8; // 8 hours
        $sleepingTemplate->available = false;
        $sleepingTemplate->working = false;
        $sleepingTemplate->priority = 10;
        $sleepingTemplate->triggerOnlyOnEmptyTitle = true;
        $sleepingTemplate->triggerAfterTimeout = true;
        $sleepingTemplate->timeout = 60 * 60 * 4; // 4 hours

        $this->templates["sleeping"] = $sleepingTemplate;
    }
}

class Template
{
    public string $title;
    public string $description;
    public int $expectedDuration;
    public bool $available;
    public bool $working;

    // Meta template information
    public int $priority; // Higher priority templates are applied first

    public bool $triggerOnlyOnEmptyTitle; // If true, the template will be only applied if the activity title is empty

    public bool $triggerAfterTimeout; // If true, the template will be applied after the timeout
    public int $timeout; // Timeout for trigger in seconds

    public function __construct()
    {
        $this->title = "";
        $this->description = "";
        $this->expectedDuration = 0;
        $this->available = false;
        $this->working = false;
        $this->priority = 0;
        $this->triggerOnlyOnEmptyTitle = false;
        $this->triggerAfterTimeout = false;
        $this->timeout = 0;
    }

    public function applyTo(Activity $activity): void
    {
        $activity->title = $this->title;
        $activity->description = $this->description;
        $activity->expectedDuration = $this->expectedDuration;
        $activity->available = $this->available;
        $activity->working = $this->working;
    }

    public function isValidFor(Activity $activity): bool
    {
        // Is any trigger active?
        if (!$this->triggerOnlyOnEmptyTitle && !$this->triggerAfterTimeout) {
            return false;
        }

        if ($this->triggerOnlyOnEmptyTitle && $activity->title != "") {
            return false;
        }

        if ($this->triggerAfterTimeout && $activity->getCurrentDuration() < $this->timeout) {
            return false;
        }

        return true;
    }

    public function initialLoad(): void
    {
    }
}
