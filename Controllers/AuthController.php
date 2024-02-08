<?php

namespace Controllers;

use Libraries\Response;
use Libraries\Encrypt;
use Libraries\Request;

class AuthController
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        $jwt = Encrypt::encryptJwt("a4b728c805a50b7d81115ce5d10a39d8-1-0" );
       
        
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            Response::sendResponse(401, ["msg" => "Authentication not received"]);
        }
        $ha = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $ha);
        

        $authorization = Encrypt::encryptJwt("a4b728c805a50b7d81115ce5d10a39d8-1-0-auth");

        $a = "http://172.233.34.152/api/user/".$_SERVER['PHP_AUTH_USER'];
       $r = Request::callApi("GET", $authorization, $a );
        Response::sendResponse($r["status"], (array)$r );
        /*$user = User::find("*", ["email" => $_SERVER['PHP_AUTH_USER'], "password" => $auth_pw]);
        if (!$user) {
            Response::sendResponse(401, ["msg" => "User not found"]);
        }
        Logs::updateInfo(["iduser" => $user[0]->iduser, "tokenvalidate" => 1]);
        $jwt = Encrypt::encryptJwt($user[0]->apikey);
        if (!empty($jwt['error'])) {
            Response::sendResponse(503, ["msg" => $jwt['error']]);
        } else {
            Response::sendResponse(200, ["token" => $jwt["token"], "expire" => $jwt["expire"]]);
        }*/
    }
}
