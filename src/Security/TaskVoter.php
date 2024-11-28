<?php
// src/Security/TaskVoter.php

namespace App\Security;

use App\Entity\Task;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // Comprueba si la acción es editar o eliminar y el sujeto es una tarea
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Solo usuarios autenticados pueden editar o eliminar
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        // Permite solo si el usuario autenticado es el propietario de la tarea
        return $task->getUser() === $user;
    }
}
