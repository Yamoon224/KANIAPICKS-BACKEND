<?php

namespace App\Services;

use App\Contracts\Notifiable;
use Illuminate\Support\Facades\Log;

/**
 * Implémentation par défaut de Notifiable : journalise la notification.
 * À remplacer par les passerelles réelles (push web/PWA, SMS, WhatsApp,
 * e-mail — cf. cahier des charges, section 5.8) sans modifier les appelants,
 * qui ne dépendent que du contrat Notifiable.
 */
class NotificationDispatcherService implements Notifiable
{
    public function notifier(string $canal, array $payload): void
    {
        Log::info("notification.{$canal}", $payload);
    }
}
