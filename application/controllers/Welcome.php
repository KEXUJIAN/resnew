<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Res\Model\User as UserModel;
use Res\Model\SimCard;
use Res\Util\MyExcel;
use Res\Util\Upload as Uploader;

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		// App::view('templates/header');
  //       var_dump(UserModel::get(1));
  //       App::view('templates/footer');
	}
	public function foo ()
	{
		echo time(), '<br>';
	}

	public function select()
	{
		// $pdo = AppService::getPDO();
		// $sth = $pdo->prepare('select * from users where id = :id');
		// $sth->execute([':i' => 1]);
		// var_dump($sth->fetchAll(PDO::FETCH_ASSOC));
		// $o = UserModel::get(1);
		// $o->name('hello');
		// $o->save();
		// $o = UserModel::getList([], ['timeAdded' => 'asc', 'id' => 'desc']);
		// $o = UserModel::getList();
		// var_dump($o);
		// $o = UserModel::getOne();
		// var_dump($o->obj2Array(['deleted', 'timeModified']));
	}

	public function insert()
	{
		// $o = new UserModel();
		// $o->role(UserModel::ROLE_MANAGER);
		// $o->name('柯许剑');
		// $o->username('admin');
		// $salt = $o->role() . $o->timeAdded();
		// var_dump($salt, md5($salt));
		// $o->passwordSalt(md5($salt));
		// $o->password(sha1('123456' . $o->passwordSalt()));
		// $o->email('1043736801@qq.com');
		// $o->save();
		// var_dump($o);
		// $o = UserModel::get(4);
		// $o->passwordSalt(md5($o->username() . $o->timeAdded()));
		// $password = sha1(sha1($o->username() . '123456') . $o->passwordSalt());
		// $o->password($password);
		// $o->save();
	}

	public function gen()
	{
//		AppService::generateModel(\Res\Model\User::class);
//		AppService::generateModel(\Res\Model\Phone::class);
//		AppService::generateModel(\Res\Model\SimCard::class);
//		AppService::generateModel(\Res\Model\Request::class);
//		AppService::generateModel(\Res\Model\UploadFile::class);
		AppService::generateModel(\Res\Model\Notification::class);
	}

	public function exception()
	{
		throw new PDOException("Error Processing Request", 1);
	}

	public function likeFn()
	{
		$pdo = AppService::getPDO();
		$sth = $pdo->prepare("SELECT * FROM users");
		$sth->execute([]);
		var_dump($sth->fetchAll());
	}

	public function selectUser()
	{
		$ret = UserModel::getOne(['username' => 'admin']);
		var_dump($ret);
	}

	public function excelSIM($file = ROOT_PATH . 'simcard.xlsx')
	{
		$head = [
		    'phoneNumber' => ['#手机号#u'],
            'label' => ['#标识#u'],
            'carrier' => ['#运营商#u'],
            'place' => ['#归属地#u'],
            'imsi' => ['#imsi#i'],
            'status' => ['#状态#u'],
        ];
		$o = new MyExcel();
		$result = $o->load($file, $head);
//		var_dump($result);
        $content = json_encode($result['content'], JSON_UNESCAPED_UNICODE);
		var_dump(strlen($content));
		var_dump(mb_strlen($content));
	}

	public function excelPhone($file = ROOT_PATH . 'phone.xlsx')
    {
        $head = [
            'type' => ['#机型#u'],
            'os' => ['#系统#u'],
            'resolution' => ['#分辨率#u'],
            'ram' => ['#ram#i'],
            'carrier' => ['#运营商#u'],
            'screenSize' => ['#屏幕尺寸#u'],
            'label' => ['#编号#u'],
            'imei' => ['#imei#i'],
            'status' => ['#状态#u'],
        ];
        $o = new MyExcel();
        $result = $o->load($file, $head);
        var_dump(json_encode($result['content'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    public function dataTable()
    {
        App::view('datatable');
    }

    public function data()
    {
        $result = [
            'result' => true,
            'data' => [],
            'recordsTotal' => 50,
            'recordsFiltered' => 50,
            'draw' => intval($_POST['draw']),
        ];
        usleep(100000);
        $draw = $result['draw'];
        for ($i = 0; $i < 10; ++$i) {
            $result['data'][$i] = [];
            $result['data'][$i]["a"] = $i * $draw;
            $result['data'][$i]["b"] = $i * $draw;
            $result['data'][$i]["c"] = $i * $draw;
            $result['data'][$i]["d"] = $i * $draw;
            $result['data'][$i]["e"] = $i * $draw;
        }
        echo json_encode($result);
    }

    public function upload(string $name = '')
    {
//        sleep(5);
        unset($name);
//        var_dump($_FILES);
        $uploader = new Uploader();
        var_dump($uploader->check($_FILES['files']));
    }

    public function sqlIn()
    {
        $pdo = AppService::getPDO();
        $c = [
            'net_type()' => ['\'1\'', '\'2，3\''],
        ];
        $where = \Res\Model\MY_Model::buildWhere($c);
        $sth = $pdo->prepare("SELECT id,operator,status,net_type FROM phone WHERE {$where['string']}");
        $sth->execute($where['array']);
        var_dump($sth->fetchAll());
    }

    public function setNull()
    {
        $phone = \Res\Model\SimCard::get(1);
        var_dump($phone);
        $phone->userId(null);
        var_dump($phone->save());
    }

    public function mail()
    {
        var_dump(AppService::getEmail()->send('测试机', '<a href="http://192.168.99.100:3500/admin/console">192.168.99.100:3500/admin/console</a>' . "\r\n哈哈", 'kexujian@163.com'));
//        var_dump(AppService::getEmail()->send('测试机', '测试机归还'));
    }

    public function updateNoId()
    {
        $pdo = AppService::getPDO();
        $sth = $pdo->prepare("UPDATE phones SET status = :inventory where status = :rent_out");
        $sth->execute([
            ':inventory' => SimCard::STATUS_IN_INVENTORY,
            ':rent_out' => SimCard::STATUS_RENT_OUT,
        ]);
        var_dump($sth->errorInfo());
    }

    public function linuxPDO()
    {
        // $dsn = 'mysql:host=127.0.0.1;dbname=test1;charset=utf8';
        // $usr = 'root';
        // $passwd = 'DTkxj';
        // $pdo = new PDO($dsn, $usr, $passwd);
        // $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // $ret = $pdo->query('select * from users')->fetch();
        // var_dump($ret);
        // var_dump(json_encode($ret, JSON_UNESCAPED_UNICODE));

    }

    public function pay()
    {
        header("content-type:text/html;charset=UTF-8");
//设置默认时区
        date_default_timezone_set('PRC');
//设置连接, 读超时时间, 商户根据实际情况自行更改
        ini_set('connection_timeout', 10);
        ini_set('default_socket_timeout', 10);

//不缓存wsdl文件, soap版本为1.1
        $options = array('trace'=>true,
            'cache_wsdl'=>WSDL_CACHE_NONE,
            'soap_version'=> SOAP_1_1);
        libxml_disable_entity_loader(false);

//获取wsdl文件
        $soapClient = new SoapClient("http://cardpay.shengpay.com/api-acquire-channel/services/receiveOrderService?wsdl", $options);

//商户密钥
        $merchantKey = 'abcdefg';
        $ip = '112.10.243.190';

        $sender = array(
            //商户号
            'senderId'=>'107537'
        );

        $service = array(
            'serviceCode'=>'B2CPayment',
            'version'=>'V4.1.1.1.1'
        );

        $header = array(
            'service'=>$service,
            'charset'=>'UTF-8',
            //网关跟踪号, 保证唯一
            'traceNo'=>date('YmdHis') . uniqid(),
            'sender'=>$sender,
            'sendTime'=>date('YmdHis')
        );

        $extension = array(
            'ext1'=>'',
            'ext2'=>''
        );

        $signature = array(
            //签名方式, (商户根据实际情况自行更改)
            'signType'=>'MD5',
            'signMsg'=>''
        );

        $request = array(
            'header'=>$header,
            //商户订单号(商户根据实际情况自行更改), 需保证唯一
            'orderNo'=>date('YmdHis') . uniqid(),
            //订单金额(商户根据实际情况自行更改), 最小单位(元)
            'orderAmount'=>'1',
            //提交订单时间
            'orderTime'=>date('YmdHis'),
            'currency'=>'CNY',
            'language'=>'zh-CN',
            //页面通知地址(商户根据实际情况自行更改)
            'pageUrl'=>'http://127.0.0.1/merchantNotify.htm',
            //后台通知地址(商户根据实际情况自行更改)
            'notifyUrl'=>'http://39.106.51.185:3500/welcome/index',
            'signature'=>$signature,
            'extension'=>$extension,
            //以下根据商户自身需求按照文档描述自行添加
            'buyerContact'=>'',
            'buyerId'=>'',
            'buyerIp'=>$ip,
            'buyerName'=>'',
            'cardPayInfo'=>'',
            'cardValue'=>'',
            'depositId'=>'',
            'depositIdType'=>'',
            //订单过期时间
            'expireTime'=>'',
            'instCode'=>'',
            'payChannel'=>'',
            'payType'=>'',
            'payeeId'=>'',
            'payerAuthTicket'=>'',
            'payerId'=>'',
            'payerMobileNo'=>'',
            'productDesc'=>'',
            'productId'=>'',
            'productName'=>'testProductKe',
            'productNum'=>'',
            'productUrl'=>'',
            'sellerId'=>'',
            'terminalType'=>'',
            'unitPrice'=>''
        );

//签名字符串拼接
        $sign = $service['serviceCode'].$service['version']
            .$header['charset'].$header['traceNo'].$sender['senderId'].$header['sendTime']
            .$request['orderNo'].$request['orderAmount'].$request['orderTime']
            .$request['expireTime'].$request['currency'].$request['payType']
            .$request['payChannel'].$request['instCode'].$request['cardValue']
            .$request['language'].$request['pageUrl'].$request['notifyUrl']
            .$request['terminalType'].$request['productId'].$request['productName']
            .$request['productNum'].$request['unitPrice'].$request['productDesc']
            .$request['productUrl'].$request['sellerId'].$request['buyerName']
            .$request['buyerId'].$request['buyerContact'].$request['buyerIp']
            .$request['payeeId'].$request['depositId'].$request['depositIdType']
            .$request['payerId'].$request['cardPayInfo'].$request['payerMobileNo']
            .$request['payerAuthTicket'].$extension['ext1'].$extension['ext2'].$signature['signType'].$merchantKey;
//签名
        $signature['signMsg'] = MD5($sign);
        $request['signature'] = $signature;

        try {
            $response = $soapClient->__soapCall('receiveB2COrder', array(array('arg0'=>$request)));
            log_message('error', "\nReceive response:\n");
            log_message('error', json_encode($response, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
            if (is_object($response)) {
                $responseArray = get_object_vars($response);
                // var_dump($responseArray);
//                echo '商户订单号 : ' . $request['orderNo'] . '</br>';
//                echo '网关支付令牌 : ' . $responseArray['return']->tokenId . '</br>';
//                echo '网关支付标识 : ' . $responseArray['return']->sessionId . '</br>';
//                echo '网关订单号 : ' . $responseArray['return']->transNo . '</br>';
//                echo '网关订单状态 : ' . $responseArray['return']->transStatus . '</br>';
            }
        } catch (SOAPFault $e) {
            var_dump($e);
            die('收单请求 SOAPFault');
        } catch(Exception $e) {
            var_dump($e);
            die('收单请求 UnkonwnFault');
        }

// 支付
        $soapClient = new SoapClient("http://cardpay.shengpay.com/api-acquire-channel/services/paymentService?wsdl", $options);

//商户订单号
        $orderNo = $request['orderNo'];
//网关支付令牌
        $tokenId = $responseArray['return']->tokenId;
//网关支付标识
        $sessionId = $responseArray['return']->sessionId;
//网关订单号
        $transNo = $responseArray['return']->transNo;

        $header = array(
            'service'=>$service,
            'charset'=>'UTF-8',
            //网关跟踪号, 保证唯一
            'traceNo'=>date('YmdHis') . uniqid(),
            'sender'=>$sender,
            'sendTime'=>date('YmdHis')
        );

        $order = array(
            'transNo'=>$transNo,
            //订单金额(商户根据实际情况自行更改), 最小单位(元)
            'orderAmoumt'=>'1',
            'orderType'=>'OT001'
        );

        $ipItem = array(
            'key'=>'PAYER_IP',
            'value'=>$ip,
        );

        /**
         * 微信支付宝支付, 此值为空
         * @var array
         */
        $cardItem = array(
            'key'=>'CARD_INFO',
            //'value'=>'2013091100005018_111111_10@@2013091100005019_111111_10',
            'value'=>'',
        );

        $paymentItems = array(
            $ipItem, $cardItem
        );

        $payment = array(
            //支付渠道, (商户根据实际情况自行更改), 具体代码含义详见文档
            'paymentType'=>'PT312',
            'instCode'=>'WXZF',
            'payChannel'=>'wp',
            'paymentItems'=>$paymentItems
        );

        $extension = array(
            'ext1'=>'',
            'ext2'=>''
        );

        $signature = array(
            //签名方式, (商户根据实际情况自行更改)
            'signType'=>'MD5',
            'signMsg'=>''
        );

        $payer = array(
            'ptId'=>'',
            'ptIdType'=>array(),
            'sdId'=>'',
            'memberId'=>'',
            'accountId'=>'',
            'accountType'=>'',
            'payableAmount'=>'',
            'payableFee'=>''
        );

        $payee = array(
            'ptId'=>'',
            'sdId'=>'',
            'memberId'=>'',
            'accountId'=>'',
            'accountType'=>'',
            'receivableAmount'=>'',
            'receivableFee'=>''
        );

        $request = array(
            'header'=>$header,
            'order'=>$order,
            'payer'=>$payer,
            'payee'=>$payee,
            'payment'=>$payment,
            'tokenId'=>$tokenId,
            'sessionId'=>$sessionId,
            'extension'=>$extension,
            'signature'=>$signature
        );

//签名字符串拼接
        @$sign = $service['serviceCode'].$service['version']
            .$header['charset'].$header['traceNo'].$sender['senderId'].$header['sendTime']
            .$order['transNo'].$order['orderAmoumt'].$order['orderType']
            .$payer['ptId'].$payer['ptIdType'].$payer['sdId'].$payer['memberId']
            .$payer['accountId'].$payer['accountType'].$payer['accountType']
            .$payee['ptId'].$payee['sdId'].$payee['memberId']
            .$payee['accountId'].$payee['accountType'].$payee['accountType']
            .$payee['receivableAmount'].$payee['receivableFee']
            .$payment['paymentType'].$payment['instCode'].$payment['payChannel'];

//拼接支付明细字符串
        foreach($paymentItems as $item) {
            $sign = $sign.$item['key'].$item['value'];
        }

        $sign = $sign.$request['tokenId'].$request['sessionId']
            .$extension['ext1'].$extension['ext2'].$signature['signType'].$merchantKey;
//签名
        $signature['signMsg'] = MD5($sign);
        $request['signature'] = $signature;

        try {
            $response = $soapClient->__soapCall('processB2CPay', array(array('arg0'=>$request)));
            // var_dump($response);
            log_message('error', "\nPay response:\n");
            log_message('error', json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            if (is_object($response)) {
                $responseArray = json_decode(json_encode($response), true);
                //var_dump($responseArray);
//                echo '网关订单号 : ' . $orderNo. '</br>';
//                echo '商户订单号 : ' . $transNo . '</br>';
//                echo '网关订单状态 : ' . $responseArray['return']->transStatus . '</br>';
//                echo '网关订单支付状态 : ' . $responseArray['return']->paymentStatus . '</br>';
//                echo '错误返回码 : ' . $errorCode . '</br>';
//                echo '错误描述 : ' . $errorMsg . '</br>';
            }
        } catch (SOAPFault $e) {
            var_dump($e);
            die('支付请求 SOAPFault');
        } catch(Exception $e) {
            var_dump($e);
            die('支付请求 UnkonwnFault');
        }

        echo <<<EOF
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<script src="/asset/jquery-1.8.3.js" type="text/javascript"></script>
<!--[if IE]><script src="/asset/excanvas.js" type="text/javascript"></script><![endif]-->
<script src="/asset/jquery.qrcode.js" type="text/javascript"></script>
<script src="/asset/qrcode.js" type="text/javascript"></script>
<script src="/asset/test.js" type="text/javascript"></script>
</head>
<body>
<div id="div_div" style="width:400px;height:400px;border:1px solid #000;" data-url="{$responseArray['return']['extension']['ext3']}"></div>
</body>
</html>
EOF;

    }

    public function payStatus()
    {
        header("content-type:text/html;charset=utf-8");
        //设置默认时区
        date_default_timezone_set('PRC');
        //设置连接, 读超时时间, 商户根据实际情况自行更改
        ini_set('connection_timeout', 10);
        ini_set('default_socket_timeout', 10);

        //不缓存wsdl文件, soap版本为1.1
        $options = array('trace'=>true,
            'cache_wsdl'=>WSDL_CACHE_NONE,
            'soap_version'=> SOAP_1_1);
        libxml_disable_entity_loader(false);

        //获取wsdl文件
        $soapClient = new SoapClient("http://cardpay.shengpay.com/api-acquire-channel/services/queryOrderService?wsdl", $options);
        //var_dump($soapClient->__getFunctions());
        //var_dump($soapClient->__getTypes());

        //商户订单号
        $orderNo = '';
        //网关订单号
        $transNo = 'C20180121230636866807';
        //商户密钥
        $merchantKey = 'abcdefg';

        $sender = array(
            //商户号
            'senderId'=>'107537'
        );

        $service = array(
            'serviceCode'=>'QUERY_ORDER_REQUEST',
            'version'=>'V4.3.1.1.1'
        );

        $header = array(
            'service'=>$service,
            'charset'=>'UTF-8',
            //网关跟踪号, 保证唯一
            'traceNo'=>date('YmdHis') . uniqid(),
            'sender'=>$sender,
            'sendTime'=>date('YmdHis')
        );

        $extension = array(
            'ext1'=>'',
            'ext2'=>''
        );

        $signature = array(
            //签名方式, (商户根据实际情况自行更改)
            'signType'=>'MD5',
            'signMsg'=>''
        );

        $request = array(
            'header'=>$header,
            'merchantNo'=>'107537',
            //orderNo和transNo必填一个, 优先级transNo大于orderNo
            'orderNo'=>$orderNo,
            'transNo'=>$transNo,
            'extension'=>$extension,
            'signature'=>$signature
        );

        //签名字符串拼接
        $sign = $service['serviceCode'].$service['version']
            .$header['charset'].$header['traceNo'].$sender['senderId'].$header['sendTime']
            .$request['merchantNo'].$request['orderNo'].$request['transNo']
            .$extension['ext2'].$signature['signType'].$merchantKey;

        //签名
        $signature['signMsg'] = MD5($sign);
        $request['signature'] = $signature;

        try {
            $response = $soapClient->__soapCall('queryOrder', array(array('arg0'=>$request)));
            log_message('error', "\nquery:\n");
            log_message('error', json_encode($response,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
            if (is_object($response)) {
                $responseArray = get_object_vars($response);
                //var_dump($responseArray);
                echo '商户订单号 : ' . $responseArray['return']->orderNo . '</br>';
                echo '网关订单号 : ' . $responseArray['return']->transNo . '</br>';
                echo '网关订单状态 : ' . $responseArray['return']->transStatus . '</br>';
                echo '订单金额 : ' . $responseArray['return']->orderAmount . '</br>';
                echo '支付金额 : ' . $responseArray['return']->transAmoumt . '</br>';
                echo '支付时间 : ' . $responseArray['return']->transTime . '</br>';
            }
        } catch (SOAPFault $e) {
            var_dump($e);
        } catch(Exception $e) {
            var_dump($e);
        }
    }
}
