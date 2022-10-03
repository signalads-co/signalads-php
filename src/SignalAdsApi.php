<?php

namespace SignalAds;

use SignalAds\Exceptions\ApiException;
use SignalAds\Exceptions\HttpException;
use SignalAds\Exceptions\RuntimeException;
use SignalAds\Enums\ApiLogs;
use SignalAds\Enums\General;

class SignalAdsApi
{
    const APIPATH = "%s://127.0.0.1:8000/api/%s/%s/%s";
    const VERSION = "v1";

    public function __construct($apiKey, $insecure = false)
    {
        if (!extension_loaded('curl')) {
            die('cURL library is not loaded');
            exit;
        }
        if (is_null($apiKey)) {
            die('apiKey is empty');
            exit;
        }
        $this->apiKey = trim($apiKey);
        $this->insecure = $insecure;
    }

    protected function get_path($method, $version = self::VERSION)
    {
        return sprintf(
            self::APIPATH,
            $this->insecure == true ? "http" : "https",
            $version,
            $this->apiKey,
            $method
        );
    }

    protected function execute($url, $data = null, $method = 'POST')
    {
        $handle = curl_init();

        switch (strtoupper($method)) {
            case 'HEAD':
                curl_setopt($handle, CURLOPT_NOBODY, true);
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                break;
            case 'GET':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
            default:
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                break;
        }

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'charset: utf-8'
        );
        $fields_string = "";
        if (!is_null($data)) {
            $fields_string = http_build_query($data);
        }
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_string);

        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
        $curl_errno = curl_errno($handle);
        $curl_error = curl_error($handle);
        if ($curl_errno) {
            throw new HttpException($curl_error, $curl_errno);
        }
        $json_response = json_decode($response);
        if ($code != 200 && is_null($json_response)) {
            throw new HttpException("Request have errors", $code);
        } else {
            $json_return = $json_response->return;
            if ($json_return->status != 200) {
                throw new ApiException($json_return->message, $json_return->status);
            }
            return $json_response->entries;
        }

    }

    public function Send(int $sender, string $receptor, string $text, $date = null)
    {
        $path = $this->get_path("send");
        $params = array(
            "sender" => $sender,
            "text" => $text,
            "receptor" => $receptor,
            "date" => $date
        );
        return $this->execute($path, $params);
    }

    public function SendGroup(int $sender, array $receptor, string $text, $date = null)
    {
        $path = $this->get_path("sendGroup");
        $params = array(
            "receptor" => json_encode($receptor),
            "sender" => $sender,
            "text" => $text,
            "date" => $date
        );
        return $this->execute($path, $params);
    }

    public function SendPair(int $sender, array $messages)
    {
        foreach ($messages as $message) {
            if (!key_exists('text', $message)) {
                throw new ApiException('text key must be in message', 429);
            }
            if (!key_exists('receptor', $message)) {
                throw new ApiException('receptor key must be in message', 429);
            }
        }

        $path = $this->get_path("sendPair");
        $params = array(
            "sender" => $sender,
            "messages" => json_encode($messages),
        );
        return $this->execute($path, $params);
    }

    public function SendPattern(int $sender, int $patternId, array $patternParams, array $receptor)
    {
        $path = $this->get_path("sendPattern");
        $params = array(
            "sender" => $sender,
            "pattern_id" => $patternId,
            "pattern_params" => json_encode($patternParams),
            "receptor" => json_encode($receptor)
        );
        return $this->execute($path, $params);
    }

    public function Status(
        int    $messageid,
        int    $limit = null,
        int    $offset = null,
        int    $status = null,
        string $receptor = null
    )
    {
        $url = "$messageid?";
        $url .= !is_null($limit) ? "limit=$limit" : '';
        $url .= !is_null($offset) ? "&offset=$offset" : '';
        $url .= !is_null($status) ? "&status=$status" : '';
        $url .= !is_null($receptor) ? "&receptor=$receptor" : '';

        $path = $this->get_path("status" . $url);
        $params = null;
        return $this->execute($path, $params, 'GET');
    }

    public function GetCredit()
    {
        $path = $this->get_path("credit");
        $params = null;
        return $this->execute($path, $params, 'GET');
    }

    public function GetPackagePrice()
    {
        $path = $this->get_path("packagePrice");
        $params = null;
        return $this->execute($path, $params, 'GET');
    }

}

