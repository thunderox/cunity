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

namespace Cunity\Register\Models;

use Cunity\Core\Cunity;
use Cunity\Core\Exception;
use Cunity\Core\Models\Db\Table\Users;
use Cunity\Core\Models\Generator\Url;
use Cunity\Core\View\Ajax\View;
use Cunity\Core\View\Message;
use Cunity\Register\View\ForgetPw;
use Cunity\Register\View\ForgetPwMail;
use Cunity\Register\View\Registration;

/**
 * Class Process
 * @package Cunity\Register\Models
 */
class Process {

    /**
     * @param $action
     */
    public function __construct($action) {
        if (method_exists($this, $action))
            call_user_func([$this, $action]);
    }

    /**
     *
     */
    private function sendRegistration() {
        $register = new Register;        
        if (!$register->validateForm()) {
            $register->renderErrors();
        } else {
            $users = new Users();
            if ($users->registerNewUser($_POST)) {
                $view = new Registration();
                $view->assign('success', true);
                $view->render();
            }
        }
    }

    /**
     *
     */
    private function validate() {
        $users = new Users;
        $res = $users->search($_POST['field'], $_POST['val']);
        $view = new View(true);
        $view->addData(["valid" => ($res == NULL)]);
        $view->sendResponse();
    }

    /**
     * @throws Exception
     */
    private function login() {
        if (!isset($_POST['email']) || !isset($_POST['password']))
            throw new Exception("Missing Parameters for login!");
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $users = new Users();
        $user = $users->search("email", $email);
        if ($user !== NULL) {
            if ($user->passwordMatch($password)) {
                if ($user->groupid == 0)
                    new \Cunity\Core\View\Message("Sorry", "Your account is not verified! Please check your verification mail! if you have not received a mail, enter your email at \"I forgot my password\" and we will send you a new mail!", "danger");
                else if ($user->groupid == 4)
                    new \Cunity\Core\View\Message("Sorry", "Your Account is blocked! Please contact the Administrator", "danger");
                else {
                    $user->setLogin(isset($_POST['save-login']));
                    header("Location:" . Url::convertUrl("index.php?m=profile"));
                    exit();
                }
            } else
                new \Cunity\Core\View\Message("Sorry", "The entered data is not correct!", "danger");
        } else
            new \Cunity\Core\View\Message("Sorry", "The entered data is not correct!", "danger");
    }

    /**
     *
     */
    private function logout() {
        if (Login::loggedIn())
            $_SESSION['user']->logout();
        header("Location:" . Url::convertUrl("index.php?m=start"));
        exit();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    private function verify() {
        if (!isset($_GET['x']) || empty($_GET['x']))
            throw new Exception("No verify-code submitted!");
        $users = new Users();
        $user = $users->search("salt", $_GET['x']);
        if ($user !== NULL) {
            $user->groupid = 1;
            $user->save();
            $config = Cunity::get("config");
            $functions = $config->registerFunctions->toArray();
            foreach ($functions["module"] AS $module)
                call_user_func([ucfirst($module) . "\Controller", "onRegister"], $user);
            new Message("Ready to go!", "Your account was verified! You can now login!", "success");
        } else
            new Message("Sorry", "We cannot verify your account! The given data was not found!", "danger");
    }

    /**
     *
     */
    private function forgetPw() {
        if (!isset($_POST['resetPw'])) {
            $view = new ForgetPw();
            $view->render();
        } else {
            $users = new Users();
            $user = $users->search("email", $_POST['email']);
            if ($user !== NULL) {
                $token = rand(123123, 999999);
                $user->password_token = json_encode(["token" => $token, "time" => time()]);
                $user->save();
                new ForgetPwMail(["name" => $user->username, "email" => $user->email], $token);
                new Message("Done!", "Please check your mails! We have sent you a token to reset your password!", "success");
                exit();
            }
        }
        $view = new ForgetPw();
        $view->assign("error", true);
        $view->render();
    }

    /**
     * @throws \Exception
     */
    private function delete() {
        $config = Cunity::get("config");
        $functions = $config->registerFunctions->toArray();
        foreach ($functions["module"] AS $module)
            call_user_func([ucfirst($module) . "\Controller", "onUnregister"], $_SESSION['user']);
    }

    /**
     *
     */
    private function reset() {
        $register = new Register;
        $register->reset();
    }

}