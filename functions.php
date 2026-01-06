<?php
// Fonction pour convertir les minutes en heures et minutes
function formatDuree($minutes)
{
    $heures = floor($minutes / 60);
    $mins = $minutes % 60;

    if ($heures > 0) {
        return $heures . 'h' . ($mins > 0 ? ' ' . $mins . 'min' : '');
    } else {
        return $mins . 'min';
    }
}
?>