<?php
/**
 * Modèle : plan_repas (MCD NutriSmart).
 */
declare(strict_types=1);

final class PlanRepas
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /** @return list<array<string, mixed>> */
    public function listerPourApi(): array
    {
        $st = $this->pdo->query(
            'SELECT id, id_utilisateur AS idUtilisateur, date_debut AS dateDebut, date_fin AS dateFin, objectif, statut FROM plan_repas ORDER BY id'
        );
        $rows = $st->fetchAll();
        foreach ($rows as &$row) {
            $row['id'] = (string) $row['id'];
            $row['idUtilisateur'] = (string) $row['idUtilisateur'];
        }
        unset($row);
        /** @var list<array<string, mixed>> $rows */
        return $rows;
    }

    /** @param array<string, mixed> $data */
    public function creer(array $data): array
    {
        $sql = 'INSERT INTO plan_repas (id_utilisateur, date_debut, date_fin, objectif, statut) VALUES (?,?,?,?,?)';
        $st = $this->pdo->prepare($sql);
        $st->execute([
            (int) $data['idUtilisateur'],
            $data['dateDebut'],
            $data['dateFin'],
            $data['objectif'],
            $data['statut'] !== '' ? $data['statut'] : 'brouillon',
        ]);
        $id = (int) $this->pdo->lastInsertId();
        return $this->getParIdApi($id);
    }

    /** @param array<string, mixed> $patch */
    public function mettreAJour(int $id, array $patch): ?array
    {
        $ex = $this->getParIdBrut($id);
        if ($ex === null) {
            return null;
        }
        $merged = array_merge($ex, $patch);
        $sql = 'UPDATE plan_repas SET id_utilisateur=?, date_debut=?, date_fin=?, objectif=?, statut=? WHERE id=?';
        $st = $this->pdo->prepare($sql);
        $st->execute([
            (int) $merged['idUtilisateur'],
            $merged['dateDebut'],
            $merged['dateFin'],
            $merged['objectif'],
            $merged['statut'] !== '' ? $merged['statut'] : 'brouillon',
            $id,
        ]);
        return $this->getParIdApi($id);
    }

    public function supprimer(int $id): bool
    {
        $st = $this->pdo->prepare('DELETE FROM plan_repas WHERE id=?');
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }

    /** @return array<string, mixed>|null */
    public function getParIdApi(int $id): ?array
    {
        $st = $this->pdo->prepare(
            'SELECT id, id_utilisateur AS idUtilisateur, date_debut AS dateDebut, date_fin AS dateFin, objectif, statut FROM plan_repas WHERE id=?'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) {
            return null;
        }
        $row['id'] = (string) $row['id'];
        $row['idUtilisateur'] = (string) $row['idUtilisateur'];
        return $row;
    }

    /** @return array<string, mixed>|null format camelCase keys */
    private function getParIdBrut(int $id): ?array
    {
        return $this->getParIdApi($id);
    }
}
