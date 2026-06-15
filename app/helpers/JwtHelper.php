<?php
class JwtHelper {
    private static $secretKey = "your_secret_key_change_this_2026";
    private static $algorithm = "HS256";
    private static $expireTime = 3600; // 1 hour

    public static function generate($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        $base64UrlHeader = self::base64UrlEncode($header);
        
        $payload['iat'] = time();
        $payload['exp'] = time() + self::$expireTime;
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secretKey, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function verify($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return false;
        
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secretKey, true);
        
        if (self::base64UrlEncode($signature) !== $base64UrlSignature) {
            return false;
        }
        
        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);
        
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token expired
        }
        
        return $payload;
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?>