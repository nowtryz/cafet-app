<?php
namespace cafetapi\webi\pages\views;

interface MessageHolder
{
    public function setMessage(string $message);
    public function getMessage() : string;
}

