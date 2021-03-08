<?php


namespace Bangtch\ztdxsms\lib;

use Bangtch\ztdxsms\exception\DataIllegalException;
use Bangtch\ztdxsms\http\HttpHelper;

date_default_timezone_set('PRC'); //设置时区

/**
 * 短信客户端
 * Class Client
 * @package Bangtch\ztdxsms\lib
 */
class Client
{
    /**
     * 发送短信url
     */
    const SMS_SEND_URL = 'http://api.mix2.zthysms.com/v2/sendSmsTp';

    //TODO 其它API添加

    /**
     * 用户名
     * @var $username
     */
    private $username;

    /**
     * 用户密码
     * @var $password_key
     */
    private $password_key;

    /**
     * 用户签名
     * @var $signature
     */
    private $signature;

    /**
     * 短信模板
     * @var $tpId ;
     */
    private $tpId;

    /**
     * 用户记录时间
     * @var $records
     */
    private $records;

    /**
     * Client constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->password_key = $config['password_key'];
        $this->username = $config['username'];
    }

    /**
     * 设置签名
     * @param $signature
     * @return Client
     * @throws DataIllegalException
     */
    public function setSignature($signature)
    {
        if (empty($signature)) throw new DataIllegalException('参数必填 signature', 10001);
        $this->signature = $signature;
        return $this;
    }

    /**
     * 设置模板id
     * @param $tpId
     * @return $this
     * @throws DataIllegalException
     */
    public function setTpId($tpId)
    {
        if (empty($tpId)) throw new DataIllegalException('参数必填 tpId', 10001);
        $this->tpId = $tpId;
        return $this;
    }

    /**
     * 设置参数
     * 参数格式
     * @param $mobile [177****6221]
     * @param $tpContent [["valid_code"=>"1234"]]
     * @return Client
     * @throws DataIllegalException
     */
    public function setParam($mobile, $tpContent)
    {
        $records = [];
        if (empty($mobile)) throw new DataIllegalException('参数错误 mobile 必传');
        foreach ($mobile as $index => $mobile_item) {
            $record = [
                'mobile' => $mobile_item,
                'tpContent' => isset($tpContent[$index]) && $tpContent[$index] ? $tpContent[$index] : ''
            ];
            $records[] = $record;
        }
        $this->records = $records;
        return $this;
    }

    /**
     * 发送短信
     * @throws DataIllegalException
     */
    public function sendSms()
    {
        $sms_send_data = [
            'tpId' => $this->tpId,
            'signature' => $this->signature,
            'records' => $this->records
        ];
        $sign_data = $this->makeSignature();
        $post_data = array_merge($sms_send_data, $sign_data);
        $http_client = new HttpHelper();
        return $http_client->json_post(self::SMS_SEND_URL, $post_data);
    }

    /**
     * 设置校验码
     * @throws DataIllegalException
     */
    private function makeSignature()
    {
        if (!($this->username)) {
            throw new DataIllegalException('缺少配置参数 username', 10001);
        }
        if (!$this->password_key) {
            throw new DataIllegalException('缺少配置参数 password_key', 10001);
        }
        $username = $this->username;
        $tKey = time();
        $password = md5(md5($this->password_key) . $tKey);
        return compact('tKey', 'password', 'username');
    }

}
