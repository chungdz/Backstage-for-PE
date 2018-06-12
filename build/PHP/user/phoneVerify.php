<?php
 include 'helper.php';
include 'userManager.php';
include '../response.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$mysqlCheck = new MysqlCheck($mysql);

$json = $_UserManager->getJson();
$phone = $json['phone'];
$code = $json['code'];

$userId = $_UserManager->getId();
if(!$userId) {
    $response->handleError('您尚未登陆');
    $response->setResponse('status', 1);
    $response->printResponseJson();
    exit;
}

$response = verifyCode($phone, $code);
$status = $response['status'];
$error = $response['error'];
switch ($status) {
    case 200:
        $updateQuery = "UPDATE users SET phone=? WHERE id=?";
        $stmt = $mysql->prepare($updateQuery);
        $stmt->bind_param('si', $phone, $userId);
        $stmt->execute();
        if($stmt->errno) {
            $response->handleError($StmtToErrMsg($stmt));
            $response->setResponse('status', 1);
            $response->printResponseJson();
            exit;
        }
        // 手机绑定成功
        $response->setResponse('status', 0);
        $response->printResponseJson();
        exit;
        break;
    
    default:
        $response->handleError($status.': '.$error);
        $response->setResponse('status', 1);
        $response->printResponseJson();
        exit;
        break;
}

/**
 * 发送短信验证请求
 * 
 * @param string $phone 手机号
 * @param int $code 验证码
 * @return array 验证结果
 */
function verifyCode($phone, $code) {
    // 发送验证码
    $response = postRequest('https://webapi.sms.mob.com/sms/verify', array(
        'appkey' => '2625f0756213c',
        'phone' => $phone,
        'zone' => '86',
        'code' => $code
    ) );
    return json_decode($response, TRUE);
}

/**
 * 发起一个post请求到指定接口
 * 
 * @param string $api 请求的接口
 * @param array $params post参数
 * @param int $timeout 超时时间
 * @return string 请求结果
 */
function postRequest( $api, array $params = array(), $timeout = 30 ) {
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $api );
    // 以返回的形式接收信息
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    // 设置为POST方式
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
    // 不验证https证书
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
        'Accept: application/json',
    ) ); 
    // 发送数据
    $response = curl_exec( $ch );
    // 不要忘记释放资源
    curl_close( $ch );
    return $response;
}
?>