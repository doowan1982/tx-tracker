<?php
namespace Tesoon\Tests;

use PHPUnit\Framework\TestCase;
use Tesoon\Tracker\{
    Tracker,
    Invocation,
    Operator,
    CliApplication,
    DataSender,
    WebApplication
};

class Test extends TestCase{

    public function testCli(){
        $tracker = $this->getTracker();
        $tracker->setApplication(new CliApplication("aaaa", "命令行测试"));
        $this->process($tracker);
    }

    public function testWeb(){
        $tracker = $this->getTracker();
        $operator = new Operator();
        $operator->id = 111;
        $operator->name = "天星教育管理员";
        $operator->ip = "127.0.0.1";
        $tracker->setApplication(new WebApplication(12231, "天星教育后台", $operator));
        $this->process($tracker);
    }

    public function registerShutdown(){
        $this->assertTrue($this->getTracker()->save(), "测试失败");
    }

    private function fun(){
        $tracker = $this->getTracker();
        $tracker->add(Invocation::create(__FILE__.' '.__METHOD__.':'.__LINE__)->setArguments(['id' => 11, 'bid' => 21]));
    }

    private function process($tracker){
        $tracker->add(Invocation::persist('处理逻辑1', '00000')->setArguments(['id' => 123123, "order_id" => 'TXSD234234']));
        $tracker->add(Invocation::create('处理逻辑2')->setTarget($this)->setArguments(['a' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑3')->setTarget(__METHOD__)->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑4')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑5')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑6')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑7')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑8')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑9')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑10')->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Invocation::create('处理逻辑11')->setArguments(['sdf' => 1, 'b' => 2]));
        $this->fun();
    }
    
    private $tracker;

    private function getTracker(){
        if($this->tracker != null){
            return $this->tracker;
        }
        $this->tracker = Tracker::getInstance();
        $this->tracker->setShutdownHandler([$this, "registerShutdown"]);
        // $this->tracker->setDataSender($this->getDataSender());
        return $this->tracker;
    }

    private function getDataSender(){
        return new class extends DataSender{
            public function send(Invocation $headInvocation): bool{
                $package = Tracker::getInstance()->getApplication()->getDataPackage();
                $string = json_encode($package->getJson($headInvocation));
                if(is_string($string)){
                    echo $string.PHP_EOL;
                    return true;
                }
                return false;
            }
        };
    }

}