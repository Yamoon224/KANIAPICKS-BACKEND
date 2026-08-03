<?php

namespace App\Modules\Trading\Services;

use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Contracts\MoteurCotationContract;
use App\Modules\Trading\Exceptions\SlippageDepasseException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Teneur de marché automatisé fondé sur une LMSR (Logarithmic Market Scoring
 * Rule) à deux issues : C(q) = b · ln(exp(q_oui/b) + exp(q_non/b)).
 *
 * Le prix d'une issue (probabilité implicite) est sa dérivée partielle,
 * exp(q_i/b) / Σexp(q_j/b), garantie comprise entre 0 et 1 et sommant à 1.
 * Le coût d'un ordre est C(q_après) - C(q_avant), toujours positif pour un
 * achat (le marché encaisse) et négatif pour une vente (le marché reverse).
 * La perte maximale du marché est bornée à b·ln(2), portée par le fonds de
 * liquidité de la plateforme — cf. cahier des charges, section 5.3.
 */
class AmmMoteurCotationService implements MoteurCotationContract
{
    public function prixActuel(Marche $marche, string $issue): int
    {
        [$prixOui, $prixNon] = $this->prix($marche->q_oui, $marche->q_non, $marche->liquidite_b, $marche->valeur_nominale_cents);

        return $issue === 'oui' ? $prixOui : $prixNon;
    }

    public function executer(Marche $marche, string $issue, string $sens, int $quantite, int $slippageMaxPct): int
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException('La quantité doit être positive.');
        }

        return DB::transaction(function () use ($marche, $issue, $sens, $quantite, $slippageMaxPct) {
            $marcheVerrouillee = Marche::whereKey($marche->id)->lockForUpdate()->firstOrFail();

            $prixAvantCents = $this->prixActuel($marcheVerrouillee, $issue);

            $qOuiAvant = $marcheVerrouillee->q_oui;
            $qNonAvant = $marcheVerrouillee->q_non;
            $delta = $sens === 'achat' ? $quantite : -$quantite;

            $qOuiApres = $issue === 'oui' ? $qOuiAvant + $delta : $qOuiAvant;
            $qNonApres = $issue === 'non' ? $qNonAvant + $delta : $qNonAvant;

            $b = $marcheVerrouillee->liquidite_b;
            $valeurNominaleCents = $marcheVerrouillee->valeur_nominale_cents;

            $coutCents = $this->coutCents($qOuiApres, $qNonApres, $b, $valeurNominaleCents)
                - $this->coutCents($qOuiAvant, $qNonAvant, $b, $valeurNominaleCents);

            $montantCents = (int) round(abs($coutCents));
            $prixMoyenCents = $quantite > 0 ? intdiv($montantCents, $quantite) : 0;

            $ecartPct = $prixAvantCents > 0
                ? (int) round(abs($prixMoyenCents - $prixAvantCents) * 100 / $prixAvantCents)
                : 0;

            if ($ecartPct > $slippageMaxPct) {
                throw new SlippageDepasseException($prixAvantCents, $prixMoyenCents);
            }

            $marcheVerrouillee->q_oui = $qOuiApres;
            $marcheVerrouillee->q_non = $qNonApres;
            $marcheVerrouillee->save();

            $marche->q_oui = $qOuiApres;
            $marche->q_non = $qNonApres;

            return $montantCents;
        });
    }

    private function coutCents(int $qOui, int $qNon, int $b, int $valeurNominaleCents): float
    {
        return $valeurNominaleCents * $b * $this->logSumExp($qOui / $b, $qNon / $b);
    }

    /** @return array{0: int, 1: int} */
    private function prix(int $qOui, int $qNon, int $b, int $valeurNominaleCents): array
    {
        $xOui = $qOui / $b;
        $xNon = $qNon / $b;
        $m = max($xOui, $xNon);
        $eOui = exp($xOui - $m);
        $eNon = exp($xNon - $m);
        $somme = $eOui + $eNon;

        return [
            (int) round($valeurNominaleCents * $eOui / $somme),
            (int) round($valeurNominaleCents * $eNon / $somme),
        ];
    }

    /** logsumexp numériquement stable : ln(e^a + e^b). */
    private function logSumExp(float $a, float $b): float
    {
        $m = max($a, $b);

        return $m + log(exp($a - $m) + exp($b - $m));
    }
}
