<?php

function respondWithJson($data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    die();
}

function respondAndDie(string $message): void
{
    echo $message;
    die();
}
