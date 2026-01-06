<?php
// Fonction pour convertir les minutes en heures et minutes
function formatDuree($minutes) {
    // Calculer les heures et les minutes restantes
    $heures = floor($minutes / 60);
    $mins = $minutes % 60;

    // Si on a des heures
    if ($heures > 0) {
        if ($mins > 0) {
            return $heures . 'h ' . $mins . 'min';
        } else {
            return $heures . 'h';
        }
    } else {
        return $mins . 'min';
    }
}
?>