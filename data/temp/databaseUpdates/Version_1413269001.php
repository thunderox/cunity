<?php


/**
 * ########################################################################################
 * ## CUNITY(R) V2.0 - An open source social network / "your private social network"     ##
 * ########################################################################################
 * ##  Copyright (C) 2011 - 2014 Smart In Media GmbH & Co. KG                            ##
 * ## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
 * ##  http://www.cunity.net                                                             ##
 * ##                                                                                    ##
 * ########################################################################################
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


use Cunity\Admin\Models\Updater\DbCommandInterface;
use Cunity\Admin\Models\Updater\DbUpdateVersion;

/**
 * Class Version 1231231231
 * @package Admin\Models\Updater\DatabaseUpdates
 */
class Version_1413269001 extends DbUpdateVersion implements DbCommandInterface
{

    protected $_timestamp = 1413269001;

    /**
     *
     */
    public function execute()
    {
        $this->_db->query("ALTER TABLE " . $this->_db->get_dbprefix() . "profilefields_values CHANGE  value  value TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;");
        $this->_db->query("ALTER TABLE " . $this->_db->get_dbprefix() . "profilefields_values ADD  sorting INT NOT NULL ;");
    }
}
