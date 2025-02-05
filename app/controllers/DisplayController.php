<?php

namespace app\controllers;

class DisplayController {
    private $view;

    public function __construct($view) {
        $this->view = trim($view, '/'); // Supprime les '/' au début et à la fin
    }

    public function display() {
        // Chemin de base des views
        $basePath = '../app/views/';

        // Vérification de la validité du chemin
        $safeView = str_replace(['..', '//'], '', $this->view); // Sécurise le chemin

        // Construire le bon fichier en fonction du dossier demandé
        $filePath = realpath($basePath . $safeView . '/' . basename($safeView) . '.php');

        // Vérifier si le fichier existe et qu'il est bien dans le dossier views
        if ($filePath && strpos($filePath, realpath($basePath)) === 0 && file_exists($filePath)) {
            include $filePath;
        } else {
            include $basePath . 'Accueil.html'; // Page d'accueil par défaut
        }
    }
}
?>
