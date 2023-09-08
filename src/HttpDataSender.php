<?php
namespace Tesoon\Tracker;

use Tesoon\Tracker\Exception\HttpException;

class HttpDataSender extends DataSender{

    /**
     * @var string
     */
    private $url;

    /**
     * @var Header
     */
    private $header = [];

    /**
     * @var array
     */
    private $timeout = 0;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(string $url, Header $header = null, array $options=[], int $timeout = 1){
        $this->url = $url;
        $this->header = $header ?? new Header();
        $this->options = [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => $timeout,
        ] + $options;
        $this->timeout = $timeout;
    }

    /**
     * @return Header
     */
    public function getHeader(): Header{
        return $this->header;
    }

    /**
     * @inheritdoc
     */
    protected function send(array $data): bool {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ch = curl_init($this->url);
        $this->options[CURLOPT_POSTFIELDS] = $data;
        curl_setopt_array($ch, $this->options);

        $this->header->setHeader('Content-Type', 'application/json');
        $this->header->setHeader('Content-Length', strlen($data));
        $headers = [];
        foreach($this->header->get() as $name => $value){
            $headers[] = "{$name}:{$value}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if(curl_exec($ch) === false){
            $code = (int)curl_errno($ch);
            $message = "Curl request failure: ".curl_error($ch);
            throw new HttpException($message, $code);
        }
        curl_close($ch);
        return true;
    }

}