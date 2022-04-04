<?php

namespace App\Exceptions;

use App\Models\TrainCheckin;
use Exception;
use Throwable;

class CheckInCollisionException extends Exception
{
    public function __construct(private readonly TrainCheckin $trainCheckIn, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getCollision(): TrainCheckin {
        return $this->trainCheckIn;
    }
}
