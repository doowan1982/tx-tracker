<?php
namespace Tesoon\Tracker;
class DataSender{

    /**
     * @var string
     */
    private $url;

    public function __construct($url){
        $this->url = $url;
    }

    /**
     * 发送消息 
     * @param Invocation $headInvocation
     * @return boolean
     */
    public function send(Invocation $headInvocation): bool {
        $package = Tracker::getInstance()->getApplication()->getDataPackage();
        $data = json_encode($package->getJson($headInvocation), JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , "data={$data}");
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10);
        if(!curl_exec($ch) && curl_errno($ch) != 28){
            echo "no:", curl_errno($ch), ' msg:', curl_error($ch), PHP_EOL;
            return false;
        }
        curl_close($ch);
        return true;
    }

}