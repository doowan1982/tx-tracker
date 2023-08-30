<?php
namespace Tesoon\Tracker;
interface DataSender{

    /**
     * 发送消息 
     * @param DataPackage $package
     * @return boolean
     * @throws HttpException
     */
    public function send(DataPackage $package): bool;

}