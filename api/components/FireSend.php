<?php

namespace app\components;

use yii\base\Component;
use Yii;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;

use GuzzleHttp\Exception\ClientException;

class FireSend extends Component
{
    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var int
     **/
    public $batchSize;
    
    /**
     * @var bool
     */
    public $active = true;
    
    /**
     * @var paragraph1\phpFCM\Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $messageText;

    /**
     * @var string
     */
    private $messageTitle;
    
    /**
     * @var array
     */
    private $queue = [ ];
    
    /**
     * @var array
     */
    private $queueData = [ ];

    /**
     * @var Callable
     */
    private $onSuccess = null;

    /**
     * @var Callable
     */
    private $onError = null;
    
    /**
     * @var Callable
     */
    private $onReplace = null;
    
    public function init()
    {
        if ($this->active) {
            $guzzle = new \GuzzleHttp\Client();
            
            $client = new Client();
            $client->setApiKey($this->apiKey);
            $client->injectHttpClient($guzzle);
            
            $this->client = $client;
        }
    }
    
    public function onSuccess(Callable $func) {
        $this->onSuccess = $func;
    }
    
    public function onError(Callable $func) {
        $this->onError = $func;
    }
    
    public function onReplace(Callable $func) {
        $this->onReplace = $func;
    }
    
    public function setMessage($title, $message) {
        $this->messageText = (string)$message;
        $this->messageTitle = (string)$title;
    }

    public function send($token, $data = null)
    {
        if (!$this->active) {
            return;
        }
        
        if(count($this->queue) >= $this->batchSize) {
            $this->flush();
        }
        
        $this->queue[] = $token;
        $this->queueData[] = $data;
    }
    
    public function flush() {
        if(!count($this->queue)) {
            return;
        }
        
        $note = new Notification($this->messageTitle, $this->messageText);
        
        $message = new Message();
        
        foreach($this->queue as $token) {
            $message->addRecipient(new Device($token));
        }
        
        $message->setNotification($note);
        
        try {
            $response = $this->client->send($message);
            $body = json_decode($response->getBody());
            
            foreach($body->results as $k => $result) {
                $old_token = $this->queue[$k];
                
                if(isset($result->error)) {
                    if($this->onError) {
                        call_user_func($this->onError, $old_token, $this->queueData[$k], $result->error);
                    }
                } else {
                    if(isset($result->registration_id)) {
                        if($this->onReplace) {
                            call_user_func($this->onReplace, $old_token, $result->registration_id, $this->queueData[$k]);
                        }
                    }
                    
                    if($this->onSuccess) {
                        call_user_func($this->onSuccess, $old_token, $this->queueData[$k]);
                    }
                }
            }
        } catch (ClientException $e) {
            $message = "Invalid response code: " . $e->getMessage();
            
            foreach($this->queue as $k => $token) {
                if($this->onError) {
                    call_user_func($this->onError, $token, $this->queueData[$k], $message);
                }
            }
        }
        
        $this->queue = [ ];
        $this->queueData = [ ];
    }
}
