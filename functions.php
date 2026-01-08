<?php
// * Fonction pour convertir les minutes en heures et minutes
// @param int $minutes - Durée en minutes
function formatDuree($minutes)
{
    // Calculer les heures et les minutes restantes
    $heures = floor($minutes / 60);
    $mins = $minutes % 60;

    // Formater l'affichage selon la durée
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