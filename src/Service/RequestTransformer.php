<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class RequestTransformer {
    public function getRequestContent(Request $request) {
        if (strpos($request->getContentTypeFormat(), "json") !== false) {
            $content = json_decode($request->getContent(), true);
            return $content;
        }

        if($request->getMethod() === "GET") {
            return $request->query;
        }

        return $request->request;
    }
}