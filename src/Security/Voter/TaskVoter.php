<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class  TaskVoter extends Voter{
    public const EDIT = 'edit';
    public const VIEW = 'view';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
//        return in_array($attribute, [self::EDIT, self::VIEW])
//            && $subject instanceof \App\Entity\Task;
//        return $attribute === self::EDIT && $subject instanceof Task;
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE]) && $subject instanceof Task;


    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $task = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::VIEW:
                return $this->canView($task, $user);
                // logic to determine if the user can VIEW
                // return true or false
                break;
            case self::DELETE:
                return $this->canDelete($task, $user);

                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Task $task, User $user): bool
    {
        //users can only edit their own task or if they are an admin
        return $user === $task->getAuthor() || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canView(Task $task, User $user): bool
    {
        //users can only view their own task or if they are an admin
        return $user === $task->getAuthor() || in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(Task $task, User $user): bool
    {
        //users can only delete their own task or if they are an admin
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
