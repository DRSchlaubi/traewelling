<?php

namespace App\Http\Controllers\Backend\Social;

use App\Exceptions\NotConnectedException;
use App\Http\Controllers\Backend\LegacyTwitterController;
use App\Http\Controllers\Backend\TwitterController;
use App\Http\Controllers\Controller;
use App\Models\SocialLoginProfile;
use App\Models\Status;
use App\Models\User;
use App\Notifications\TwitterNotSent;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialiteUser;

abstract class AbstractTwitterController extends Controller
{
    /**
     * @throws NotConnectedException
     */
    public static function forUser(User $user): AbstractTwitterController {
        $sPro = $user->socialProfile;
        if ($sPro?->twitter_id === null || $sPro?->twitter_token === null) {
            throw new NotConnectedException();
        }

        if ($sPro->twitter_tokenSecret !== null) {
            return new LegacyTwitterController();
        } else {
            return new TwitterController();
        }
    }

    /**
     * Function to create a user with a login-provider.
     * If logged in, the user will have the login-provider added.
     * If a user with corresponding login-provider already exists, it will be returned.
     *
     * @param SocialiteUser $socialiteUser
     *
     * @return User model
     */
    public static function getUserFromSocialite(SocialiteUser $socialiteUser): User {
        $socialProfile = SocialLoginProfile::where('twitter_id', $socialiteUser->id)->first();

        if ($socialProfile !== null) {
            self::updateToken($socialProfile->user, $socialiteUser);
            return $socialProfile->user;
        }

        if (auth()->check()) {
            self::updateToken(auth()->user(), $socialiteUser);
            return auth()->user();
        }
        return self::createUser($socialiteUser);
    }

    private static function createUser(SocialiteUser $socialiteUser): User {
        $user = User::create([
                                 'name'     => SocialController::getDisplayName($socialiteUser),
                                 'username' => SocialController::getUniqueUsername($socialiteUser->getNickname()),
                             ]);
        self::updateToken($user, $socialiteUser);
        return $user;
    }

    private static function updateToken(User $user, SocialiteUser $socialiteUser): void {
        $user->socialProfile->update([
                                         'twitter_id'               => $socialiteUser->id,
                                         'twitter_token'            => $socialiteUser->token,
                                         'twitter_refresh_token'    => $socialiteUser->refreshToken,
                                         'twitter_token_expires_at' => Date::now()->add('second', $socialiteUser->expiresIn)
                                     ]);
    }


    /**
     * @param Status $status
     * @param string $socialText
     *
     * @return int
     * @throws NotConnectedException
     * @throws TweetNotSendException
     */
    public abstract function postTweet(Status $status, string $socialText): int;

    /**
     * @throws NotConnectedException
     */
    public static function postStatus(Status $status) {
        $controller = self::forUser($status->user);

        try {
            $socialText = self::generateFullSocialText($status);
            $tweetId    = $controller->postTweet($status, $socialText);
            $status->update(['tweet_id' => $tweetId]);
            Log::info("Posted on Twitter: " . $socialText);
        } catch (NotConnectedException $exception) {
            throw $exception;
        } catch (TweetNotSendException $exception) {
            $status->user->notify(new TwitterNotSent($exception->getStatusCode(), $exception->getStatus()));
        } catch (Exception $exception) {
            report($exception);
            // The Twitter adapter itself won't throw Exceptions, but rather return HTTP codes.
            // However, we still want to continue if it explodes, thus why not catch exceptions here.
        }
    }

    protected static function generateFullSocialText(Status $status): string {
        $socialText = $status->socialText;
        if ($status->user->always_dbl) {
            $socialText .= ' #dbl';
        }
        $socialText .= ' ' . url('/status/' . $status->id);

        return $socialText;
    }
}

class TweetNotSendException extends Exception
{
    protected Status $status;
    protected int    $statusCode;

    /**
     * @param Status $status
     * @param int    $statusCOde
     */
    public function __construct(Status $status, int $statusCode) {
        parent::__construct();
        $this->status     = $status;
        $this->statusCode = $statusCode;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }
}