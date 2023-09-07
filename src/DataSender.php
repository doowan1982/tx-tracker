<?php
namespace Tesoon\Tracker;
/**
 * 将DataPackage中的数据
 */
abstract class DataSender{

    /**
     * @param DataPackage $package
     * @return bool
     */
    public function collectAndSend(DataPackage $package): bool{
        if(!$this->send($package->toArray())){
            return false;
        }
        return true;
    }

    /**
     * 发送消息 
     * @param DataPackage $package
     * @return boolean
     * @throws HttpException
     */
    protected abstract function send(array $data): bool;
}