<?php

namespace TungTT\CorsProxy\Http\Controllers;

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
    public function __invoke(Request $request, $path = null)
    {
        if($path){
            $url = $path;
        } else {
            $validator = Validator::make($request->all(), [
                'url' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => 'The given data was invalid.', 'errors' => $validator->getMessageBag()], 500);
            }

            $url = $this->getUrl();
        }

        $params = $this->getParams();
        
        $url= Str::replaceFirst('https://geoportal-uat.vntts.vn', 'http://geoportal-uat.vntts.vn', $url);

        $this->handleParamArray($params, $url);

        $headers = collect($request->header())->except([
            'host',
            'content-length',
        ])->all();

        $method = Str::lower(request()->input('method', 'GET'));

        $newRequest = Http::withHeaders($headers)->{$method}($url, $params);

        return response($newRequest->body(), $newRequest->status())->withHeaders($headers);
    }

    protected function handleParamArray(&$params, &$url){
        $paramArrStr = '';
        foreach ($params as $key => $param){
            if(is_array($param)){
                $paramArrStr .= ($paramArrStr === '' ? '' : '&').collect($param)->map(fn($value) => "{$key}[]={$value}")->join('&');
                unset($params[$key]);
            }
        }

        if($paramArrStr) $url = "{$url}?{$paramArrStr}";
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
