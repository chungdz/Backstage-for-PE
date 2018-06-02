<?php
class RsaManager
{
    private $publicKey =
            "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCbrbo/JaPJTJLl+6hfZm7uuLIr
t/hivaLfot32wq/nSzoSsYkoNk27Yy+n10ODoZ75/91Y8QoJKeoWe0Ik1H1DmMuw
Ef3eBoBCFn+eNjgZq6SIVBCNEnUaS0STmWqGPFKRFJ1Ujd4rJQ1tGFG3z3v9Cw2b
Kq41AAYMD7ZqLv2zfQIDAQAB
-----END PUBLIC KEY-----";
    private $privateKey =
            "-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCbrbo/JaPJTJLl+6hfZm7uuLIrt/hivaLfot32wq/nSzoSsYko
Nk27Yy+n10ODoZ75/91Y8QoJKeoWe0Ik1H1DmMuwEf3eBoBCFn+eNjgZq6SIVBCN
EnUaS0STmWqGPFKRFJ1Ujd4rJQ1tGFG3z3v9Cw2bKq41AAYMD7ZqLv2zfQIDAQAB
AoGAOzDkvZm8Go+I0vTKYet6hj2nUMQUJsbfpJQyPN5tL04E+JKUCbwL9hGSTwij
3OqYnYlRSb3sCOvs2ztmPMamEXOlLUb2SiFQ9hApie5r7ArdsiSz+OI+xrt8Q9zS
9dnYL/JMmaKnSHx3xVipSNrD4sxVaCLDv77k+54AtO7iZCkCQQDORC9B4UkfrfUd
t6FpOphctLbIejzl1oKb1bdowLsGD1Q78JGzU0nZIeaBTTIhFLjdcOXwOqdXYfhX
tmSRHIHTAkEAwTcCce1FM80mT4QOjdmAVaNPah1AjliuJBIDXN9vpP3+v0XMf4IS
9lEib/3eq+jXug0t9osdsHuIIVKc5A5TbwJBAI2iAjUhhb2MDJi4Q8xm4MIfkLb+
QJytfAeXa5YxaPqTQgraeKGDGl1PSuEUacyPVxUu9aTMEaYN7qID6vA9e+MCQQCa
2aeP/wUzWvuVRtUTQnnkKJqBBSiz7MbECdvAFyK3LQl56krW9jyURLpA30oSpO4g
Imfv69bDln2nlzo+XGzpAkBigEyscmneSvs9j78yhTKKiyvrkSRUuER3Xn1GmLIX
wnTJqJJchY7ZOd0SeFhwHGOUywb28+elra1yv9Pazany
-----END RSA PRIVATE KEY-----";

    
    public function __construct()
    {
        // $resource = openssl_pkey_new();
        // openssl_pkey_export($resource, $this->privateKey);
        // $detail = openssl_pkey_get_details($resource);
        // $this->publicKey = $detail['key'];
    }

    public function publicEncrypt($data)
    {
        openssl_public_encrypt($data, $encrypted, $this->publicKey);
        return $encrypted;
    }

    public function publicDecrypt($data)
    {
        openssl_public_decrypt($data, $decrypted, $this->publicKey);
        return $decrypted;
    }

    public function privateEncrypt($data)
    {
        openssl_private_encrypt($data, $encrypted, $this->privateKey);
        return $encrypted;
    }

    public function privateDecrypt($data)
    {
        openssl_private_decrypt($data, $decrypted, $this->privateKey);
        return $decrypted;
    }
    public function getPublicKey() {
        return $this->publicKey;
    }
    public function getPrivateKey() {
        return $this->privateKey;
    }
}

$_RsaManager = new RsaManager();
// $rsa = new Rsa();
// echo "公钥：\n", $rsa->getPublicKey(), "\n";
// echo "私钥：\n", $rsa->getPrivateKey(), "\n";

// // 使用公钥加密
// $str = $rsa->publicEncrypt('hello');
// // 这里使用base64是为了不出现乱码，默认加密出来的值有乱码
// $str = base64_encode($str);
// echo "公钥加密（base64处理过）：\n", $str, "\n";
// $str = base64_decode($str);
// $pubstr = $rsa->publicDecrypt($str);
// echo "公钥解密：\n", $pubstr, "\n";
// $privstr = $rsa->privateDecrypt($str);
// echo "私钥解密：\n", $privstr, "\n";

// // 使用私钥加密
// $str = $rsa->privateEncrypt('world');
// // 这里使用base64是为了不出现乱码，默认加密出来的值有乱码
// $str = base64_encode($str);
// echo "私钥加密（base64处理过）：\n", $str, "\n";
// $str = base64_decode($str);
// $pubstr = $rsa->publicDecrypt($str);
// echo "公钥解密：\n", $pubstr, "\n";
// $privstr = $rsa->privateDecrypt($str);
// echo "私钥解密：\n", $privstr, "\n";

?>