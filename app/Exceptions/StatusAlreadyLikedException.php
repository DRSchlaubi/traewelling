<?php

namespace App\Exceptions;

use App\Models\Status;
use App\Models\User;
use Exception;

class StatusAlreadyLikedException extends Exception
{
    public function __construct(private readonly User $user, private readonly Status $status) {
        parent::__construct();
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getStatus(): Status {
        return $this->status;
    }
}
