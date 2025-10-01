<?php
namespace App\Repository;

use App\Entity\Habit;
use App\Utils\EntityMapper;
use Mns\Buggy\Core\AbstractRepository;

class HabitRepository extends AbstractRepository
{
    public function findAll()
    {
        $DB = $this->getConnection();
        $SQL = "SELECT * FROM habits";
        $STMT = $DB->prepare($SQL);
        $STMT->execute();
        $habits = $STMT->fetchAll();
        return EntityMapper::mapCollection(Habit::class, $habits);
    }

    public function find(int $id)
    {
        $DB = $this->getConnection();
        $SQL = "SELECT * FROM habits WHERE id = :id";
        $STMT = $DB->prepare($SQL);
        $STMT->bindValue(":id", $id);
        $STMT->execute();
        $habit = $STMT->fetch();
        return EntityMapper::map(Habit::class, $habit);
    }

    public function findByUser(int $userId)
    {
        $DB = $this->getConnection();
        $SQL = "SELECT * FROM habits WHERE user_id = :user_id";
        $STMT = $DB->prepare($SQL);
        $STMT->bindValue(":user_id", $userId);
        $STMT->execute();
        $habits = $STMT->fetchAll();
        return EntityMapper::mapCollection(Habit::class, $habits);
    }

     /**
     * Compte le nombre d'habitudes actives pour un utilisateur
     */
    public function countByUser(int $userId): int
    {
        $stmt = $this->getConnection()->prepare("SELECT COUNT(*) as total FROM habits WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function insert(array $data = array())
    {   
        $DB = $this->getConnection();
        $SQL = "INSERT INTO habits (user_id, name, description, created_at) VALUES (:user_id, :name, :description, NOW())";
        $STMT = $DB->prepare($SQL);
        $STMT->bindValue(":user_id", $data['user_id']);
        $STMT->bindValue(":name", $data['name']);
        $STMT->bindValue(":description", $data['description']);
        $STMT->execute();
        return $DB->lastInsertId();
    }

    /**
     * Calcule le nombre de jours consécutifs où l'utilisateur a complété au moins une habitude
     */
    public function getStreak(int $userId): int
    {
        $pdo = $this->getConnection();

        $sql = "
            SELECT DISTINCT log_date
            FROM habit_logs hl
            JOIN habits h ON hl.habit_id = h.id
            WHERE h.user_id = :user_id AND hl.status = 1
            ORDER BY log_date DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $dates = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $streak = 0;
        $today = new \DateTime();
        foreach ($dates as $dateStr) {
            $date = new \DateTime($dateStr);
            if ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                $streak++;
                $today->modify('-1 day');
            } elseif ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                // continue streak
                $streak++;
                $today->modify('-1 day');
            } else {
                break;
            }
        }

        return $streak;
    }

}
