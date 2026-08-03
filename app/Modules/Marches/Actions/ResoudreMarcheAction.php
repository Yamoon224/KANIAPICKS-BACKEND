<?php

namespace App\Modules\Marches\Actions;

use App\Contracts\Encaissable;
use App\Contracts\Notifiable;
use App\Contracts\Versable;
use App\Modules\Marches\Models\Marche;
use App\Modules\Portefeuille\Models\Portefeuille;
use App\Modules\Trading\Models\Position;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Constate l'issue d'un marché sur la base de sa source officielle et
 * déclenche le paiement des parts gagnantes via le ledger (Encaissable),
 * sans jamais manipuler directement le portefeuille de l'utilisateur. Les
 * gains sont versés par le compte plateforme, contrepartie de l'AMM.
 */
class ResoudreMarcheAction
{
    public function __construct(
        private readonly Encaissable $encaissable,
        private readonly Versable $versable,
        private readonly Notifiable $notifications,
    ) {}

    public function executer(Marche $marche, string $issueGagnante, string $preuveUrl): void
    {
        if ($marche->statut === 'resolu') {
            throw new RuntimeException('Ce marché est déjà résolu.');
        }

        DB::transaction(function () use ($marche, $issueGagnante, $preuveUrl) {
            $marcheVerrouillee = Marche::whereKey($marche->id)->lockForUpdate()->firstOrFail();

            $marcheVerrouillee->statut = 'resolu';
            $marcheVerrouillee->issue_gagnante = $issueGagnante;
            $marcheVerrouillee->preuve_url = $preuveUrl;
            $marcheVerrouillee->resolu_at = now();
            $marcheVerrouillee->save();

            $comptePlateforme = Portefeuille::compteplateforme();

            Position::query()
                ->where('marche_id', $marcheVerrouillee->id)
                ->where('issue', $issueGagnante)
                ->where('quantite', '>', 0)
                ->with('user.portefeuille')
                ->chunkById(100, function ($positionsGagnantes) use ($marcheVerrouillee, $comptePlateforme) {
                    foreach ($positionsGagnantes as $position) {
                        $gainCents = $position->quantite * $marcheVerrouillee->valeur_nominale_cents;
                        $reference = "resolution:marche-{$marcheVerrouillee->id}:position-{$position->id}";

                        $this->versable->debiter($comptePlateforme->id, $gainCents, 'gain', $reference);
                        $this->encaissable->crediter($position->user->portefeuille->id, $gainCents, 'gain', $reference);

                        $this->notifications->notifier('push', [
                            'user_id' => $position->user_id,
                            'type' => 'resolution_marche',
                            'marche_id' => $marcheVerrouillee->id,
                            'gain_cents' => $gainCents,
                        ]);
                    }
                });
        });
    }
}
