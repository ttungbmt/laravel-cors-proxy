<?php

namespace ttungbmt\CorsProxy\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CorsProxyController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'The given data was invalid.', 'errors' => $validator->getMessageBag()], 500);
        }

        $url = $this->getUrl();
        $params = $this->getParams();
        $method = Str::lower(request()->input('method', 'GET'));

        $request = Http::{$method}($url, $params);

        $headers = collect($request->headers())->except(['Transfer-Encoding']);

        return response($request->body(), $request->status())->withHeaders($headers);
    }

    protected function getUrl(){
        $url = request()->input('url');
        $parsed = parse_url($url);

        //$url = $parsed['scheme']. '://'. $parsed['host']. $parsed['path'];
        $url = strtok($url, '?');

        return $url;
    }

    protected function getParams(){
        $params = request()->except(['url', 'method']);
        $queryStr = parse_url(request()->input('url'), PHP_URL_QUERY);
        parse_str($queryStr, $queryParams);

        return array_merge($queryParams, $params);
    }
}
