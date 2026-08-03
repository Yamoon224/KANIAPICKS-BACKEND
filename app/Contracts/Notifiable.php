<?php

namespace App\Contracts;

interface Notifiable
{
    public function notifier(string $canal, array $payload): void;
}
