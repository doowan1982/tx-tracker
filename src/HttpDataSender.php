<?php
namespace Tesoon\Tracker;

use Tesoon\Tracker\Exception\HttpException;

class HttpDataSender implements DataSender{

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $timeout = 0;

    public function __construct($url, array $headers=[], int $timeout = 1){
        $this->url = $url;
        $this->headers = $headers;
        $this->timeout = $timeout;
    }

    /**
     * @inheritdoc
     */
    public function send(DataPackage $package): bool {
        $data = json_encode($package->toArray(), JSON_UNESCAPED_UNICODE);
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $header = [
            'Content-Type' => 'application/json',
            'Content-Length' => strlen($data),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header + $this->headers);
        
        if(curl_exec($ch) === false){
            $code = (int)curl_errno($ch);
            $message = "Curl request failure: ".curl_error($ch);
            throw new HttpException($message, $code);
        }
        curl_close($ch);
        return true;
    }

}