<?php
namespace webi_min\pages\views;

interface MessageHolder
{
    public function setMessage(string $message);
    public function getMessage() : string;
}

