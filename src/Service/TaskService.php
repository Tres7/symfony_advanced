<?php
namespace App\Service;

use App\Entity\Task;

class TaskService
{
    public function canEdit(Task $task): bool
    {
        $createdAt = $task->getCreatedAt();
        if (!$createdAt) {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->diff($createdAt);

        return $interval->days <= 7;
    }
}


?>