<?php

namespace App\Http\Controllers\Backend\Social;

use App\Http\Controllers\API\v1\Controller;
use App\Models\SocialLoginProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Socialite;
use SocialiteProviders\Manager\OAuth2\User as OAuthUser;

class ZitadelController extends Controller
{
    public function callback(): RedirectResponse {
        /** @var OAuthUser $socialiteUser */
        $socialiteUser = Socialite::driver('zitadel')->user();
        $tokenResponse = $socialiteUser->accessTokenResponseBody;
        $idToken       = $tokenResponse['id_token'];

        session()->put('zitadel_token', $idToken);


        $user = self::getUserFromSocialite($socialiteUser);
        if (!auth()->check()) {
            auth()->login(user: $user, remember: true);
            $user->update(['last_login' => Carbon::now()->toIso8601String()]);
        }

        return redirect()->intended('/dashboard');
    }

    public static function getUserFromSocialite(SocialiteUser $socialiteUser): Authenticatable {
        $socialProfile = SocialLoginProfile::where('zitadel_id', $socialiteUser->id)
                                           ->first();

        if ($socialProfile !== null) {
            self::updateToken($socialProfile->user, $socialiteUser);
            return $socialProfile->user;
        }

        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            self::updateToken($user, $socialiteUser);
            return $user;
        }

        return self::createUser($socialiteUser);
    }

    private static function createUser(SocialiteUser $socialiteUser): User {
        if (!config('app.registration.enabled')) {
            abort(403, 'Registrations are currently closed.');
        }

        $user = User::create([
                                 'name'     => SocialController::getDisplayName($socialiteUser),
                                 'username' => SocialController::getUniqueUsername($socialiteUser->getNickname()),
                             ]);


        self::updateToken($user, $socialiteUser);
        return $user;
    }

    private static function updateToken(User $user, SocialiteUser $socialiteUser): void {
        $user->socialProfile->update([
                                         'zitadel_id' => $socialiteUser->id,
                                     ]);
    }
}
