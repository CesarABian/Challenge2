<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Password as BaseClass;

class Password extends BaseClass
{
    public static function sendResetLink($credentials, $callback = null)
    {
        $user = User::where('email', '=', $credentials)->first();
        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if (self::getRepository()->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $token = self::getRepository()->create($user);

        if ($callback) {
            return $callback($user, $token) ?? static::RESET_LINK_SENT;
        }

        return static::RESET_LINK_SENT;
    }
}
