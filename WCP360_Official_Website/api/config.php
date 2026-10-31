<?php
$config = [
    'site_name' => 'NETTOYAGE 360',
    'base_url' => 'https://www.nettoyage360.fr',
    'recipient' => 'contact@nettoyage360.fr',
    'from_email' => 'info@nettoyage360.fr',
    'from_name' => 'NETTOYAGE 360',
    'mail_transport' => 'mail',
    'smtp_host' => 'nodels46-eu.n0c.com',
    'smtp_port' => 465,
    'smtp_security' => 'ssl',
    'smtp_username' => '',
    'smtp_password' => '',
    'smtp_timeout' => 15,
    'max_upload_files' => 6,
    'max_upload_bytes_each' => 8 * 1024 * 1024,
    'max_upload_bytes_total' => 20 * 1024 * 1024,
    'max_upload_pixels' => 40_000_000,
    'rate_limit_count' => 5,
    'rate_limit_window' => 900,
    'contact_subjects' => ['Question sur une prestation', 'Demande de rappel', 'Contrat d’entretien', 'Copropriété / syndic', 'Recrutement', 'Autre demande'],
    'profiles' => ['Particulier', 'Professionnel', 'Copropriété / syndic', 'Association / autre'],
    'frequencies' => ['Ponctuelle', '1 fois par semaine', '2 à 3 fois par semaine', '4 à 5 fois par semaine', 'Quotidienne', 'À définir'],
    'services' => ['Nettoyage de copropriété / immeuble', 'Nettoyage locaux professionnels', 'Nettoyage commercial', 'Nettoyage industriel', 'Nettoyage résidentiel', 'Nettoyage vitres / façades', 'Nettoyage fin de bail', 'Shampoing moquettes / tapis', 'Autre prestation', 'À définir', 'Nettoyage d’appartement', 'Nettoyage de maison', 'Ménage ponctuel', 'Nettoyage approfondi', 'Avant / après déménagement', 'État des lieux', 'Nettoyage après travaux', 'Vitres', 'Canapé / tapis / moquette', 'Débarras', 'Nettoyage de bureaux', 'Commerces / boutiques', 'Cabinets médicaux / dentaires', 'Salles de sport', 'Locaux professionnels', 'Entrepôts / industriel', 'Entretien régulier', 'Contrat d’entretien professionnel', 'Copropriété / parties communes', 'Poubelles / conteneurs', 'Parkings / sous-sols', 'Nettoyage haute pression', 'Lessivage des murs', 'Entretien des espaces verts', 'Nettoyage après syndrome de Diogène / Insalubrité', 'Nettoyage de façades et bardages', 'Nettoyage mécanisé des sols', 'Après chantier', 'Après sinistre', 'Dégât des eaux', 'Remise en état complète', 'Intervention urgente'],
];

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $overrides = require $localConfig;
    if (is_array($overrides)) $config = array_replace($config, $overrides);
}

$environment = [
    'mail_transport' => 'N360_MAIL_TRANSPORT',
    'smtp_host' => 'N360_SMTP_HOST',
    'smtp_port' => 'N360_SMTP_PORT',
    'smtp_security' => 'N360_SMTP_SECURITY',
    'smtp_username' => 'N360_SMTP_USERNAME',
    'smtp_password' => 'N360_SMTP_PASSWORD',
];
foreach ($environment as $key => $variable) {
    $value = getenv($variable);
    if ($value !== false && $value !== '') $config[$key] = $key === 'smtp_port' ? (int)$value : $value;
}

// L'ancien hôte mail.nettoyage360.fr est proxifié par Cloudflare et ne laisse
// pas passer SMTP sur le port 465. Conserver cette compatibilité permet aux
// configurations privées déjà déployées de fonctionner après mise à jour.
if (strcasecmp(trim((string)($config['smtp_host'] ?? '')), 'mail.nettoyage360.fr') === 0) {
    $config['smtp_host'] = 'nodels46-eu.n0c.com';
}

return $config;
