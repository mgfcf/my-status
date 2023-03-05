<?php

class Activity
{
    public int $startTime;  // Unix timestamp in seconds
    public int $expectedDuration;   // In seconds, 0 if open-ended

    public string $title;
    public string $description; // Additional information, but might be kept empty

    public bool $available; // Am I available for requests and to talk?
    public bool $working;   // Am I working on something?

    public function getCurrentDuration(): int
    {
        return time() - $this->startTime;
    }

    public function getRemainingDuration(): int
    {
        return $this->expectedDuration - $this->getCurrentDuration();
    }

    public function getPublicData(): array
    {
        return array(
            "startTime" => $this->startTime,
            "expectedDuration" => $this->expectedDuration,
            "title" => $this->title,
            "description" => $this->description,
            "available" => $this->available,
            "working" => $this->working,
            "currentDuration" => $this->getCurrentDuration(),
            "remainingDuration" => $this->getRemainingDuration(),
        );
    }
}