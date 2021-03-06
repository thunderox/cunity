<?php

namespace Cunity\Core\Access;

use Cunity\Core\Exceptions\UnknownUser;
use Cunity\Core\Models\Db\Row\User;
use Cunity\Core\Request\Session;

/**
 * Class UserAccess.
 */
class UserAccess
{
    /**
     * @param User $user
     *
     * @throws UnknownUser
     */
    public static function profilePublic(User $user)
    {
        if ($user->userid === null) {
            $user = Session::get('user');
        }

        switch ($user->groupid) {
            case 0:
            case 4:
                throw new UnknownUser();
            case 1:
            case 2:
            case 3:
            default:
                break;
        }
    }
}
