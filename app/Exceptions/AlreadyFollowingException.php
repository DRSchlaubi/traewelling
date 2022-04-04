<?php

namespace App\Exceptions;

use App\Models\User;
use Exception;

class AlreadyFollowingException extends Exception
{
    /**
     * AlreadyFollowingException constructor.
     * $initiator is already following $user
     * OR
     * $initiator has already requested a follow to $user
     */
    public function __construct(private readonly User $initiator, private readonly User $user) {
        parent::__construct();
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getInitiator(): User {
        return $this->initiator;
    }
}
