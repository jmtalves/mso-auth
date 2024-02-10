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
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            Response::sendResponse(401, ["msg" => "Authentication not received"]);
        }
        $ha = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $ha);
        $authorization = Encrypt::encryptJwt($_SERVER['PHP_AUTH_USER'] . "-1-0-auth");
        $url = "http://" . getenv('URL_LOAD_BALANCE') . "/api/user/" . $_SERVER['PHP_AUTH_USER'];
        $user_info = Request::callApi("GET", $authorization['token'], $url);
        if ($user_info["status"] != 200 || !isset($user_info['body']["info"][0]["password"])) {
            Response::sendResponse($user_info["status"], $user_info["body"]);
        }
        if (password_verify($_SERVER['PHP_AUTH_PW'], $user_info['body']["info"][0]["password"])) {
            $user = $user_info['body']["info"][0];
            $jwt = Encrypt::encryptJwt($user["apikey"] . "-" . $user["iduser"] . "-" . $user["type"]);
            if (!empty($jwt['error'])) {
                Response::sendResponse(503, ["msg" => $jwt['error']]);
            } else {
                Response::sendResponse(200, ["token" => $jwt["token"], "expire" => $jwt["expire"]]);
            }
        } else {
            Response::sendResponse(401, ["msg" => "Password invalid"]);
        }
    }
}
