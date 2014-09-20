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

namespace Cunity\Friends\Models;

use Cunity\Core\Exception;
use Cunity\Core\View\Ajax\View;

/**
 * Class FriendShipController
 * @package Cunity\Friends\Models
 */
class FriendShipController {

    /**
     *
     */
    public function __construct() {
        if (method_exists($this, $_GET['action'])) {
            call_user_func([$this, $_GET['action']]);
        }
    }

    /**
     *
     */
    private function add() {
        if (!isset($_POST['userid']))
            new Exception("No userid given!");
        else {
            $relations = new Db\Table\Relationships();
            $res = $relations->insert(["sender" => $_SESSION['user']->userid, "receiver" => $_POST['userid'], "status" => 1]);
            if ($res) {
                $view = new View($res !== false);
                $view->sendResponse();
            }
        }
    }

    /**
     *
     */
    private function block() {
        if (!isset($_POST['userid']))
            new Exception("No userid given!");
        else {
            $relations = new Db\Table\Relationships();
            $res = $relations->updateRelation($_SESSION['user']->userid, $_POST['userid'], ["status" => 0, "sender" => $_SESSION['user']->userid, "receiver" => $_POST['userid']]);
            if ($res) {
                $view = new View($res !== false);
                $view->sendResponse();
            }
        }
    }

    /**
     *
     */
    private function confirm() {
        if (!isset($_POST['userid'])) // Here the userid is the relation id to make it easier to identify the friendship!
            new Exception("No userid given!");
        else {
            $relations = new Db\Table\Relationships();
            $res = $relations->updateRelation($_SESSION['user']->userid, $_POST['userid'], ["status" => 2]);
            if ($res) {
                $view = new View($res !== false);
                $view->sendResponse();
            }
        }
    }

    /**
     *
     */
    private function remove() {
        if (!isset($_POST['userid'])) // Here the userid is the relation id to make it easier to identify the friendship!
            new Exception("No userid given!");
        else {
            $relations = new Db\Table\Relationships();
            $res = $relations->deleteRelation($_SESSION['user']->userid, $_POST['userid']);
            if ($res) {
                $view = new View($res !== false);
                $view->sendResponse();
            }
        }
    }

    /**
     *
     */
    private function change() {
        if (!isset($_POST['userid'])) // Here the userid is the relation id to make it easier to identify the friendship!
            new Exception("No userid given!");
        else {
            $relations = new Db\Table\Relationships();
            $res = $relations->updateRelation($_POST['userid'], $_SESSION['user']->userid, ["status" => $_POST['status']]);
            if ($res) {
                $view = new View($res !== false);
                $view->sendResponse();
            }
        }
    }

    /**
     * @throws Exception
     */
    private function loadData() {
        $userid = $_POST['userid'];
        $users = $_SESSION['user']->getTable();
        $result = $users->get($userid);
        if ($result === NULL)
            throw new Exception("No User found with the given ID!");
        else {
            $view = new View(true);
            $view->addData(["user" => $result->toArray(["pimg", "username", "firstname", "lastname"])]);
            $view->sendResponse();
        }
    }

    /**
     *
     */
    private function load() {
        $relations = new Db\Table\Relationships();
        $userid = ($_POST['userid'] == 0) ? $_SESSION['user']->userid : $_POST['userid'];
        $rows = $relations->getFullFriendList(">1", $userid);
        $view = new View(true);
        $view->addData(["result" => $rows]);
        $view->sendResponse();
    }

}