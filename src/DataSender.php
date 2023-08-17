<?php
namespace Tesoon\Tracker;
class DataSender{

    /**
     * 发送消息 
     * @param Invocation $headInvocation
     * @return boolean
     */
    public function send(Invocation $headInvocation): bool {
        echo "send--------------".PHP_EOL;
        $package = Tracker::getInstance()->getApplication()->getDataPackage();
        $data = json_encode($package->getJson($headInvocation), JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://test-dev.tesoon.com");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , "data={$data}");
        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10);
        if(!curl_exec($ch) && curl_errno($ch) != 28){
            echo "no:", curl_errno($ch), ' msg:', curl_error($ch), PHP_EOL;
            return false;
        }
        curl_close($ch);
        echo "已发送".PHP_EOL;
        return true;
    }

}