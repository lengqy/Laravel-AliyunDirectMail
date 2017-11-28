<?php 

namespace Cherry\DirectMail;

use GuzzleHttp\Client;
use Swift_Mime_SimpleMessage;
use Illuminate\Mail\Transport\Transport;
use GuzzleHttp\Exception\ClientException;

class DirectMailTransport extends Transport
{
    const API_URL = 'https://dm.aliyuncs.com/';

	private $access_key_id;
	private $access_secret;
	private $replay_to_address;
	private $address_type;
	private $region;
	private $click_trace;

	public function __construct(array $config)
	{
		$this->access_key_id     = $config['access_key_id'];
		$this->access_secret     = $config['access_secret'];
		$this->replay_to_address = $config['replay_to_address'];
		$this->address_type      = $config['address_type'];
		$this->region            = $config['region'];
		$this->click_trace       = $config['click_trace'];
	}

	/**
	 * {@inheritdoc}
	 */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        return $this->SingleSendMail($message);
    }

    /**
     * 单一发信接口
     * @param  Swift_Mime_SimpleMessage $message
     * @return array
     */
    private function SingleSendMail(Swift_Mime_SimpleMessage $message)
    {
    	$params = [];
        $params['Action']         = 'SingleSendMail';
        $params['AccountName']    = $this->getAccountName($message);
        $params['ReplyToAddress'] = $this->getReplyToAddress($this->replay_to_address);
        $params['AddressType']    = (string) $this->address_type;
        $params['ToAddress']      = $this->getToAddress($message);
        $params['FromAlias']      = $this->getAlisa($message);
        $params['Subject']        = $message->getSubject();
        $params['HtmlBody']       = $message->getBody();
        $params['ClickTrace']     = (string) $this->click_trace;

        $params = array_merge($params, $this->getCommonParams());
        $params['Signature'] = $this->makeSign($params);

        try {
            $response = (new Client)->post(self::API_URL, ['form_params' => $params]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody());
            throw new DirectMailException($body->Message, $response->getStatusCode());
        }
        
        return json_decode($response->getBody(), true);
    }

    /**
     * 获取发信人
     * @param  Swift_Mime_SimpleMessage $message
     * @return string
     */
    private function getAccountName(Swift_Mime_SimpleMessage $message)
    {
        return head(array_keys($message->getFrom()));
    }

    /**
     * 获取发信人
     * @param  Swift_Mime_SimpleMessage $message
     * @return string
     */
    private function getAlisa(Swift_Mime_SimpleMessage $message)
    {
        return head(array_values($message->getFrom()));
    }

    /**
     * 获取发信人
     * @param  Swift_Mime_SimpleMessage $message
     * @return string
     */
    private function getToAddress(Swift_Mime_SimpleMessage $message)
    {
        return implode(',', array_keys($message->getTo()));
    }


    /**
     * 是否使用管理控制台中配置的回信地址
     * @return string
     */
    private function getReplyToAddress($data)
    {
        if (is_string($data))
            return $data;

        return $data === false ? 'false' : 'true';
    }

    /**
     * 生成请求签名信息
     * @param  array $params 请求参数
     * @return string
     */
    private function makeSign(array $params)
    {
        ksort($params);
        $str = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $str = 'POST&%2F&'.rawurlencode($str);
        return base64_encode(hash_hmac('sha1', $str, $this->access_secret . "&", true));
    }

    /**
     * 获取公共请求参数
     * @return array
     */
    private function getCommonParams()
    {
        return [
            'Format'           => 'JSON',
            'Version'          => $this->region == 'cn-hangzhou' ? '2015-11-23' : '2017-06-22',
            'AccessKeyId'      => $this->access_key_id,
            'SignatureMethod'  => 'HMAC-SHA1',
            'Timestamp'        => gmdate('c'),
            'SignatureVersion' => '1.0',
            'SignatureNonce'   => uniqid(),
            'RegionId'         => $this->region
        ];
    }
}