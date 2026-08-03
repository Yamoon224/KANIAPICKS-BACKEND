<?php

return [
    'devise_defaut' => env('KANIAPICKS_DEFAULT_CURRENCY', 'XOF'),

    'valeur_nominale_part_cents' => (int) env('KANIAPICKS_NOMINAL_SHARE_VALUE_CENTS', 100000),

    // Commission prélevée sur chaque transaction de trading (cf. cahier des
    // charges, section 6 — taux par défaut 1 à 2 %).
    'frais_trading_pct' => (float) env('KANIAPICKS_TRADING_FEE_PCT', 1.5),

    // Écart maximal toléré entre le prix affiché et le prix moyen exécuté.
    'slippage_max_pct_defaut' => (int) env('KANIAPICKS_DEFAULT_SLIPPAGE_MAX_PCT', 5),

    // Contrepartie comptable de la liquidité AMM et des frais de trading.
    'email_compte_plateforme' => env('KANIAPICKS_PLATFORM_ACCOUNT_EMAIL', 'plateforme@kaniapicks.internal'),

    // Plafonds journaliers par palier KYC, en centimes (cf. section 5.1).
    'plafonds_kyc' => [
        1 => ['depot' => 50_000_00, 'retrait' => 25_000_00],
        2 => ['depot' => 500_000_00, 'retrait' => 250_000_00],
        3 => ['depot' => 5_000_000_00, 'retrait' => 2_500_000_00],
    ],
];
