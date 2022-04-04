<?php

namespace App\Exceptions;

use App\Models\PrivacyAgreement;
use App\Models\User;
use Exception;

class AlreadyAcceptedException extends Exception
{
    /**
     * @var \App\Models\PrivacyAgreement|mixed
     */
    public                $privacyPolicy;
    private readonly User $initiator;

    /**
     * AlreadyFollowingException constructor.
     * $initiator is already following $user
     * OR
     * $initiator has already requested a follow to $user
     *
     * @param PrivacyAgreement privacyPolicy
     */
    public function __construct(PrivacyAgreement $agreement, private readonly User $user) {
        $this->privacyPolicy = $agreement;
        parent::__construct();
    }

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    public function getPrivacyValidity(): \DateTimeInterface {
        return $this->privacyPolicy->valid_at;
    }

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    public function getUserAccepted(): \DateTimeInterface {
        return $this->user->privacy_ack_at;
    }
}
