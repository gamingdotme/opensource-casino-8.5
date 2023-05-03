<?php 

namespace Intergo\SmsTo\Http;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Client {
    
    /**
     * Guzzle Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Access token.
     *
     * @var string
     */
    public $accessToken;

    /**
     * The message that will going to send.
     *
     * @var string
     */
    public $message;

    /**
     * Array of destination numbers with their respective personalized messages to be sent
     *
     * @var array
     */
    public $messages;

    /**
     * The sender ID which is optional
     *
     * @var string
     */
    public $senderId;

    /**
     * Array of phone numbers.
     *
     * @var array
     */
    public $recipients;

    /**
     * List id.
     *
     * @var int
     */
    public $listId;

    /**
     * Callback URL
     *
     * This will be an optional URL where we will POST some information 
     * about the status of SMS as soon as we have an update
     *
     * @var string
     */
    public $callbackUrl;

    /**
     * Environment
     *
     * Developer can use live or sandbox
     *
     * @var string
     */
    public $environment = 'sandbox';

    /**
     * Base URL
     *
     * Base URL to be use for calling API
     * Example URL is https://api.sms.to/v1
     *
     * @var string
     */
    public $baseUrl = 'https://api.sms.to/v1';

    /**
     * The client id
     *
     *
     * @var integer
     */
    public $clientId;

    /**
     * The client secret
     *
     *
     * @var string
     */
    public $clientSecret;

    /**
     * The username or email account in sms.to
     *
     *
     * @var string
     */
    public $username;

    /**
     * The password we use in sms.to itself
     *
     *
     * @var string
     */
    public $password;

    /**
     * We can add options for get requests
     *
     * @var array $options
     */
    public $options;

    /**
     * Constructor
     *
     * @param integer|null $clientId
     * @param string|null $clientSecret
     * @param string|null $username
     * @param string|null $password
     * @param string|null $accessToken
     * @return void
     */
    public function __construct($clientId = null, $clientSecret = null, $username = null, $password = null, $accessToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username = $username;
        $this->password = $password;
        $this->accessToken = $accessToken;
        
        // Check if this library is used by laravel
        if (function_exists('config')) {
            $this->baseUrl = config('smsto.base_url');
        }
    }

    /**
     * Get Access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        // If we have value in access token then just return it
        if ($this->accessToken) {
            return $this->accessToken;
        }
        $url = $this->baseUrl . '/oauth/token';

        $this->credentials = [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => '*'
        ];

        return $this->token($url);
    }

    /**
     * Refreshes Access token.
     *
     * @return string
     */
    public function refreshToken()
    {
        $url = $this->baseUrl . '/oauth/token';

        $this->credentials = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->accessToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => ''
        ];

        return $this->token($url);
    }

    /**
     * Access token.
     *
     * @param string $url
     * @return string|bool
     */
    public function token($url)
    {
        $response = $this->request($url, 'post', $this->credentials);
        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            return $response;
        }

        return false;
    }

    /**
     * Get user cash balance.
     *
     * @return array
     */
    public function getBalance()
    {
        $this->getAccessToken();
        
        $path = $this->baseUrl . '/balance';

        return $this->request($path, 'post');
    }

    /**
     * Sends personalized SMS to a single number or array of 
     * numbers with personalized SMS.
     *
     * @return array
     */
    public function sendSingle()
    {
        $this->getAccessToken();
        
        $path = $this->baseUrl . '/sms/single/send';

        $body = [
            'messages' => $this->messages,
            'send_id' => $this->senderId,
            'callback_url' => $this->callbackUrl
        ];
        return $this->request($path, 'post', $body);
    }

    /**
     * This will send a message to multiple numbers specified in request body.
     *
     * @return array
     */
    public function sendMultiple()
    {
        $this->getAccessToken();
        
        $path = $this->baseUrl . '/sms/send';
        
        $body = [
            'body' => $this->message,
            'to' => $this->recipients,
            'send_id' => $this->senderId,
            'callback_url' => $this->callbackUrl
        ];
        return $this->request($path, 'post', $body);
    }

    /**
     * Sending single SMS to a list.
     *
     * @return array
     */
    public function sendList()
    {
        $this->getAccessToken();
        $path = $this->baseUrl . '/sms/send';
        
        $body = [
            'body' => $this->message,
            'to_list_id' => $this->listId,
            'send_id' => $this->senderId,
            'callback_url' => $this->callbackUrl
        ];
        return $this->request($path, 'post', $body);
    }

    /**
     * Fetch paginated lists
     *
     * @return array
     */
    public function getLists($options = null)
    {
        if ($options) {
            $this->options = $options;
        }
        $this->getAccessToken();
        $path = $this->baseUrl . '/lists';
        return $this->request($path, 'get');
    }

    /**
     * Get a single list with the specified ID.
     * Initially you need to make a request to fetch all lists. 
     * From that you can get the ID of a specific list and then 
     * fetch its data separately.
     *
     * @return array
     */
    public function getList($id)
    {
        $this->getAccessToken();
        $path = $this->baseUrl . '/lists/' . $id;
        return $this->request($path, 'get');
    }

    /**
     * Set the message.
     *
     * @param string $message 
     * @return \Intergo\SmsTo\SmsTo
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the messages.
     *
     * @param array $messages 
     * @return \Intergo\SmsTo\SmsTo
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Set Sender ID.
     *
     * @param string $senderId 
     * @return \Intergo\SmsTo\SmsTo
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * Set recipients.
     *
     * @param array $recipients 
     * @return \Intergo\SmsTo\SmsTo
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
        return $this;
    }

    /**
     * Set list id.
     *
     * @param int $listId 
     * @return \Intergo\SmsTo\SmsTo
     */
    public function setListId($listId)
    {
        $this->listId = $listId;
        return $this;
    }

    /**
     * Set callback URL.
     *
     * @param string $callbackUrl 
     * @return \Intergo\SmsTo\SmsTo
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    /**
     * Send Request
     *
     * @param string $path
     * @param string $method
     * @param array|null $body
     *
     * @return array
     */
    public function request($path, $method, $body = [])
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $headers['Authorization'] = 'Bearer ' . $this->accessToken;

        $client = new HttpClient(['headers' => $headers]);
        $response = null;
        if ($this->options != null and !empty($this->options)) {
            $path .= '?' . http_build_query($this->options);
        }
        try
        {
            switch ($method)
            {
                case 'get':
                    $response = $client->get($path)->getBody()->getContents();
                    break;
                case 'post':
                    $response = $client->post($path, [
                        'json' => $body,
                    ])->getBody()->getContents();
                    break;
                case 'delete':
                    $response = $client->delete($path)->getBody()->getContents();
                    break;
                case 'put':
                    $response = $client->put($path, [
                        'json' => $body,
                    ])->getBody()->getContents();
                    break;
                default:
                    $response = '';
                    break;
            }
            $response = json_decode($response, true);

        } catch (Exception $e) {
            $response = $this->exception($e);
        } catch (RequestException $e) {
            $response = $this->exception($e);
        } catch (ClientException $e) {
            $response = $this->exception($e);
        } catch (ServerException $e) {
            $response = $this->exception($e);
        }

        return $response;
    }

    /**
     * Format exceptions message
     *
     * @param Exception $e
     *
     * @return mixed
     */
    public function exception($e)
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            return json_decode($response->getBody()->getContents(), true);
        }

        return false;
    }
}
