<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 05/05/2016
 * Time: 18:31
 */

namespace AppBundle\Utils;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Helper
{
    /**
     * @param Request $request
     * @return mixed
     */
    public static function getIp(Request $request)
    {
        return $request->server->get("REMOTE_ADDR");
    }

    /**
     * @return \DateTime
     */
    public static function getDate()
    {
        return new \DateTime();
    }

    /**
     * @param string $content
     * @return JsonResponse
     */
    public static function errorJsonResponse($content = "error")
    {
        return new JsonResponse($content, 404);
    }

}