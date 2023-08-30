<?php
namespace Tesoon\Tests;

use PHPUnit\Framework\TestCase;
use Tesoon\Tests\Classes\A;
use Tesoon\Tracker\{
    Tracker,
    Span,
    Operator,
    CliApplication,
    DataPackage,
    DataSender,
    HttpDataSender,
    Utils,
    WebApplication
};

class Test extends TestCase{

    private $tracker;

    public static function setUpBeforeClass(){
        file_put_contents(self::TEST_FILE, '');
    }

    public static function tearDownAfterClass(){
        @unlink(self::TEST_FILE);
    }

    /**
     * @test
     */
    public function traceBack(){
        $this->getTracker()->setApplication(new CliApplication("aaaa", "cli"));
        $a = new A('TEST');
        $current = $this->getTracker()->getSpanCollection()->current();
        $this->assertTrue($current->getDesc() == 'A class method test', '测试失败');
        $this->assertTrue($this->getTracker()->flush(), "测试失败");
    }

    /**
     * @test
     */
    public function cli(){
        $tracker = $this->getTracker();
        $tracker->setApplication(new CliApplication("aaaa", "命令行测试"));
        $this->process($tracker, __FUNCTION__);
        $current = $this->getTracker()->getSpanCollection()->current();
        $this->assertTrue($current->getDesc() == '['.__FUNCTION__.']last', '测试失败');
        $this->assertTrue($this->getTracker()->flush(), "测试失败");
    }

    /**
     * @test
     */
    public function web(){
        $tracker = $this->getTracker();
        $operator = new Operator();
        $operator->id = 111;
        $operator->name = "admin";
        $operator->ip = "127.0.0.1";
        $tracker->setApplication(new WebApplication(12231, "backend", $operator));
        $this->process($tracker, __FUNCTION__);
        $current = $this->getTracker()->getSpanCollection()->current();
        $this->assertTrue($current->getDesc() == '['.__FUNCTION__.']last', '测试失败');
        $this->assertTrue($this->getTracker()->flush(), "测试失败");
    }

    /**
     * @test
     */
    public function sliceSend(){
        $tracker = $this->getTracker();
        //满足4条数据推送到服务器
        $tracker->setSpanCount(4)->setApplication(new CliApplication("aaaa", "命令行测试"));
        $this->process($tracker, __FUNCTION__);
        $this->assertTrue($this->getTracker()->flush(), "测试失败");
    }

    private function fun(){
        $tracker = $this->getTracker();
        $tracker->add(Span::create(__FILE__.' '.__METHOD__.':'.__LINE__));
    }

    private function process($tracker, $scope){
        $timestamp = Utils::getIntegerMicroTime();
        usleep(1500);
        $tracker->add(Span::persist("[{$scope}]处理逻辑1", '00000', $timestamp)->setArguments(['id' => 123123, "order_id" => 'TXSD234234']));
        $tracker->add(Span::create("[{$scope}]处理逻辑2")->setTargetName($this)->setArguments(['a' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑3")->setTargetName(__METHOD__)->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑4")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑5")->setArguments(['sdf' => 1, 'b' => 2]));
        $this->fun();
        $tracker->add(Span::create("[{$scope}]处理逻辑6")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑7")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑8")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑9")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑10")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]处理逻辑11")->setArguments(['sdf' => 1, 'b' => 2]));
        $tracker->add(Span::create("[{$scope}]last")->setArguments(['sdf' => 1, 'b' => 2]));
    }

    private function getTracker(){
        if($this->tracker != null){
            return $this->tracker;
        }
        $this->tracker = Tracker::getInstance();
        $this->tracker->setDataSender($this->getDataSender());
        // $this->tracker->setDataSender(new HttpDataSender('http://trace.dev'));
        return $this->tracker;
    }

    const TEST_FILE = './tests/file/test.data';

    private function getDataSender(){
        //本地测试使用
        return new class implements DataSender{
            public function send(DataPackage $package): bool{
                $json = json_encode($package->toArray(), JSON_UNESCAPED_UNICODE);
                return file_put_contents(Test::TEST_FILE, $json.PHP_EOL, FILE_APPEND);
            }
        };
    }

}