<?php
// Activation des messages d'erreur
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérification de la méthode HTTP
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: contact.html");
    exit();
}

// Récupération et nettoyage des données du formulaire
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validation des champs requis
if (!$name || !$email || !$subject || !$message) {
    header("Location: contact.html?error=missing_fields");
    exit();
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: contact.html?error=invalid_email");
    exit();
}

try {
    // Préparation de l'entrée du log
    $log_file = 'contact_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "=== Nouveau message - $timestamp ===\n";
    $log_entry .= "Nom: $name\n";
    $log_entry .= "Email: $email\n";
    $log_entry .= "Sujet: $subject\n";
    $log_entry .= "Message:\n$message\n";
    $log_entry .= "================================\n\n";

    // Sauvegarde dans le fichier log
    if (file_put_contents($log_file, $log_entry, FILE_APPEND)) {
        header("Location: success.html");
        exit();
    } else {
        throw new Exception("Erreur lors de l'enregistrement du message");
    }
} catch (Exception $e) {
    header("Location: contact.html?error=server_error");
    exit();
}
?>