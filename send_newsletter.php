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
$company   = trim((string)($data['company'] ?? ''));
$email     = trim((string)($data['email'] ?? ''));
$website   = trim((string)($data['website'] ?? ''));

// Honeypot anti-spam
if ($website !== '') {
    echo json_encode([
        'success' => true,
        'message' => 'Demande traitée.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($firstname === '' || $lastname === '' || $email === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Prénom, nom et e-mail sont obligatoires.'
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

// Nettoyage simple pour éviter les injections d'entêtes
$firstname = preg_replace("/[\r\n]+/", ' ', $firstname);
$lastname  = preg_replace("/[\r\n]+/", ' ', $lastname);
$company   = preg_replace("/[\r\n]+/", ' ', $company);
$email     = preg_replace("/[\r\n]+/", ' ', $email);

// À ADAPTER SI BESOIN
$toEmail   = 'guillaume.verliac@priam-solutions.com, antoine.delcambre@priam-solutions.com';
$fromEmail = 'guillaume.verliac@priam-solutions.com';
$fromName  = 'Site Priam Solutions';

$subject = $firstname . ' ' . $lastname . " souhaite s'inscrire à la newsletter";

$bodyLines = [
    $firstname . ' ' . $lastname . " souhaite s'inscrire à la newsletter.",
    '',
    'Prénom : ' . $firstname,
    'Nom : ' . $lastname,
    'Entreprise : ' . ($company !== '' ? $company : 'Non renseignée'),
    'E-mail : ' . $email,
    '',
    'Envoyé depuis le formulaire newsletter du site Priam Solutions.'
];

$message = implode("\r\n", $bodyLines);

// Encodage UTF-8 pour le sujet
$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: ' . mb_encode_mimeheader($fromName, 'UTF-8') . ' <' . $fromEmail . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();

$headersString = implode("\r\n", $headers);

$sent = mail($toEmail, $encodedSubject, $message, $headersString);

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
    'message' => 'Inscription envoyée avec succès.'
], JSON_UNESCAPED_UNICODE);
