<?php

/**
 * ########################################################################################
 * ## CUNITY(R) V2.0 - An open source social network / "your private social network"     ##
 * ########################################################################################
 * ##  Copyright (C) 2011 - 2015 Smart In Media GmbH & Co. KG                            ##
 * ## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
 * ##  http://www.cunity.net                                                             ##
 * ##                                                                                    ##
 * ########################################################################################.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or any later version.
 *
 * 1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
 * 2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
 * 3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
 * 4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program (under the folder LICENSE).
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * If your software can interact with users remotely through a computer network,
 * you have to make sure that it provides a way for users to get its source.
 * For example, if your program is a web application, its interface could display
 * a "Source" link that leads users to an archive of the code. There are many ways
 * you could offer source, and different solutions will be better for different programs;
 * see section 13 of the GNU Affero General Public License for the specific requirements.
 *
 * #####################################################################################
 */

namespace Cunity\Core\Models\Validation;

use Cunity\Core\Models\Db\Table\Users;
use Cunity\Core\Request\Session;
use Cunity\Register\Models\Login;

/**
 * Class Email.
 */
class Email extends \Zend_Validate_EmailAddress
{
    /**
     *
     */
    const USED = 'used';
    /**
     *
     */
    const EMPTYSTRING = 'empty';

    /**
     * @var array
     */
    protected $_messageTemplates = [
        self::USED => 'This E-Mail address is already in use',
        self::EMPTYSTRING => 'Please enter an email!',
    ];

    /**
     * @param string $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        $returnValue = false;

        $this->_setValue($value);
        $users = new Users();
        if (empty($value)) {
            $this->_error(self::EMPTYSTRING);
        } else {
            $user = $users->search('email', $value);
            if (($user !== null && !Login::loggedIn()) ||
                (Login::loggedIn() && $user->userid !== Session::get('user')->userid)
            ) {
                $this->_error(self::USED);
                $returnValue = false;
            } else {
                $returnValue = parent::isValid($value);
            }
        }

        return $returnValue;
    }
}
