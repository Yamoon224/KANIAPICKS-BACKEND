<?php

namespace App\Modules\Trading\Actions;

use App\Contracts\Encaissable;
use App\Contracts\Versable;
use App\Models\User;
use App\Modules\Marches\Models\Marche;
use App\Modules\Portefeuille\Models\Portefeuille;
use App\Modules\Trading\Contracts\MoteurCotationContract;
use App\Modules\Trading\Exceptions\PositionInsuffisanteException;
use App\Modules\Trading\Models\Ordre;
use App\Modules\Trading\Models\Position;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Exécute un ordre d'achat ou de vente pour un utilisateur sur un marché.
 * Ne dépend que d'interfaces (moteur de cotation, ledger) afin de rester
 * indépendante du mécanisme de cotation retenu (AMM ou carnet d'ordres).
 *
 * Le compte plateforme sert de contrepartie comptable à l'AMM et perçoit les
 * frais, afin que le ledger reste en partie double (aucun montant créé ni
 * perdu).
 */
class ExecuterOrdreAction
{
    public function __construct(
        private readonly MoteurCotationContract $moteurCotation,
        private readonly Encaissable $encaissable,
        private readonly Versable $versable,
    ) {}

    public function executer(User $user, Marche $marche, string $issue, string $sens, int $quantite, int $slippageMaxPct): Ordre
    {
        if ($marche->statut !== 'publie') {
            throw new RuntimeException("Ce marché n'est pas ouvert au trading.");
        }

        return DB::transaction(function () use ($user, $marche, $issue, $sens, $quantite, $slippageMaxPct) {
            $portefeuille = Portefeuille::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $comptePlateforme = Portefeuille::compteplateforme();

            $position = Position::firstOrCreate(
                ['user_id' => $user->id, 'marche_id' => $marche->id, 'issue' => $issue],
                ['quantite' => 0, 'prix_revient_total_cents' => 0],
            );

            if ($sens === 'vente' && $position->quantite < $quantite) {
                throw new PositionInsuffisanteException($position->quantite, $quantite);
            }

            $montantBrutCents = $this->moteurCotation->executer($marche, $issue, $sens, $quantite, $slippageMaxPct);
            $fraisCents = (int) round($montantBrutCents * config('kaniapicks.frais_trading_pct') / 100);
            $reference = "ordre:marche-{$marche->id}:user-{$user->id}:".now()->valueOf();

            if ($sens === 'achat') {
                $this->versable->debiter($portefeuille->id, $montantBrutCents + $fraisCents, 'achat', $reference);
                $this->encaissable->crediter($comptePlateforme->id, $montantBrutCents, 'achat', $reference);

                $position->quantite += $quantite;
                $position->prix_revient_total_cents += $montantBrutCents;
            } else {
                $this->versable->debiter($comptePlateforme->id, $montantBrutCents, 'vente', $reference);
                $this->encaissable->crediter($portefeuille->id, $montantBrutCents - $fraisCents, 'vente', $reference);

                $coutMoyenUnitaireCents = $position->quantite > 0
                    ? intdiv($position->prix_revient_total_cents, $position->quantite)
                    : 0;
                $position->quantite -= $quantite;
                $position->prix_revient_total_cents -= $coutMoyenUnitaireCents * $quantite;
            }

            if ($fraisCents > 0) {
                $this->encaissable->crediter($comptePlateforme->id, $fraisCents, 'frais', $reference);
            }

            $position->save();

            return Ordre::create([
                'user_id' => $user->id,
                'marche_id' => $marche->id,
                'sens' => $sens,
                'issue' => $issue,
                'quantite' => $quantite,
                'prix_cents' => intdiv($montantBrutCents, $quantite),
                'frais_cents' => $fraisCents,
            ]);
        });
    }
}
