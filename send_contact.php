<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Requête invalide.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$firstname = trim((string)($data['firstname'] ?? ''));
$lastname  = trim((string)($data['lastname'] ?? ''));
$email     = trim((string)($data['email'] ?? ''));
$phone     = trim((string)($data['phone'] ?? ''));
$subject   = trim((string)($data['subject'] ?? ''));
$message   = trim((string)($data['message'] ?? ''));
$website   = trim((string)($data['website'] ?? ''));

// Honeypot anti-spam
if ($website !== '') {
    echo json_encode([
        'success' => true,
        'message' => 'Demande traitée.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($firstname === '' || $lastname === '' || $email === '' || $subject === '' || $message === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Merci de remplir tous les champs obligatoires.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Adresse e-mail invalide.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Nettoyage anti-injection d'entêtes
$firstname = preg_replace("/[\r\n]+/", ' ', $firstname);
$lastname  = preg_replace("/[\r\n]+/", ' ', $lastname);
$email     = preg_replace("/[\r\n]+/", ' ', $email);
$phone     = preg_replace("/[\r\n]+/", ' ', $phone);
$subject   = preg_replace("/[\r\n]+/", ' ', $subject);

$subjectLabels = [
    'produit-structure' => 'Élaboration d’un produit structuré',
    'recherche'         => 'Intérêt pour nos travaux de recherche',
    'plateforme'        => 'Plateforme de simulation',
    'partenariat'       => 'Partenariat',
    'career'            => 'Careers',
    'newsletter'        => 'Newsletter',
    'autre'             => 'Autre demande',
];

$subjectLabel = $subjectLabels[$subject] ?? $subject;

// À ADAPTER SI BESOIN
$toEmail   = 'guillaume.verliac@priam-solutions.com';
$fromEmail = 'guillaume.verliac@priam-solutions.com';
$fromName  = 'Site Priam Solutions';

$mailSubject = 'Nouveau message de contact - ' . $subjectLabel . ' - ' . $firstname . ' ' . $lastname;

$bodyLines = [
    'Nouveau message reçu depuis la page contact.',
    '',
    'Prénom : ' . $firstname,
    'Nom : ' . $lastname,
    'Email : ' . $email,
    'Téléphone : ' . ($phone !== '' ? $phone : 'Non renseigné'),
    'Objet : ' . $subjectLabel,
    '',
    'Message :',
    $message,
    '',
    'Envoyé depuis le formulaire de contact du site Priam Solutions.'
];

$mailBody = implode("\r\n", $bodyLines);

$encodedSubject = '=?UTF-8?B?' . base64_encode($mailSubject) . '?=';

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: ' . mb_encode_mimeheader($fromName, 'UTF-8') . ' <' . $fromEmail . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();

$headersString = implode("\r\n", $headers);

$sent = mail($toEmail, $encodedSubject, $mailBody, $headersString);

if (!$sent) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "L'e-mail n'a pas pu être envoyé."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Votre message a bien été envoyé.'
], JSON_UNESCAPED_UNICODE);
