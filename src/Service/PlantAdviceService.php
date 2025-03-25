<?php

namespace App\Service;

use App\Entity\Plant;

class PlantAdviceService
{
    /**
     * Génère des conseils personnalisés selon les conditions de la plante.
     * @param Plant $plant
     * @return array
     */
    public function getPersonalizedAdvice(Plant $plant): array
    {
        $advice = [];

        // ☀️ Conseil ensoleillement
        switch ($plant->getSunlightLevel()) {
            case 'low':
                $advice[] = "Placez votre plante à l'ombre ou dans un endroit peu lumineux.";
                break;
            case 'medium':
                $advice[] = "Votre plante a besoin de lumière indirecte, évitez le soleil.";
                break;
            case 'high':
                $advice[] = "Assurez-vous que votre plante reçoit beaucoup de lumière.";
                break;
        }

        // 🌡️ Conseil température
        $temp = $plant->getTemperature();
        if ($temp !== null) {
            if ($temp < 15) {
                $advice[] = "Protégez votre plante du froid.";
            } elseif ($temp > 30) {
                $advice[] = "Évitez l'exposition à des températures élevées et arrosez.";
            }
        }

        // 💧 Conseil humidité
        switch ($plant->getHumidityLevel()) {
            case 'low':
                $advice[] = "Pulvérisez régulièrement de l'eau sur les feuilles.";
                break;
            case 'medium':
                $advice[] = "L'humidité est correcte, surveillez les signes de sécheresse.";
                break;
            case 'high':
                $advice[] = "Assurez une bonne ventilation pour éviter les moisissures.";
                break;
        }

        return $advice;
    }
}
