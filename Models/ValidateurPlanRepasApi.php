<?php
declare(strict_types=1);

final class ValidateurPlanRepasApi
{
    /** @param array<string, mixed> $data */
    public static function valider(array $data): ResultatValidation
    {
        $r = new ResultatValidation();
        $idU = isset($data['idUtilisateur']) ? trim((string) $data['idUtilisateur']) : '';
        ValidateurChampsCommuns::entierPositif($r, 'idUtilisateur', $idU, "L'identifiant utilisateur");

        $dd = isset($data['dateDebut']) ? trim((string) $data['dateDebut']) : '';
        $df = isset($data['dateFin']) ? trim((string) $data['dateFin']) : '';
        ValidateurChampsCommuns::dateIso($r, 'dateDebut', $dd, 'La date de début');
        ValidateurChampsCommuns::dateIso($r, 'dateFin', $df, 'La date de fin');
        if ($r->ok() && strcmp($dd, $df) > 0) {
            $r->ajouter('dateFin', 'La date de fin doit être postérieure ou égale à la date de début.');
        }

        $obj = isset($data['objectif']) ? trim((string) $data['objectif']) : '';
        ValidateurChampsCommuns::obligatoire($r, 'objectif', $obj, "L'objectif");
        ValidateurChampsCommuns::longueurMax($r, 'objectif', $obj, 255, "L'objectif");

        $st = isset($data['statut']) ? trim((string) $data['statut']) : '';
        ValidateurChampsCommuns::longueurMax($r, 'statut', $st, 64, 'Le statut');

        return $r;
    }
}
