<?php
class AuthMiddleware {
    
    public static function requireAuth() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $payload = JwtHelper::verify($token);
            
            if ($payload) {
                return $payload;
            }
        }
        
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized: Token không hợp lệ hoặc đã hết hạn']);
        exit();
    }

    public static function requireAdmin() {
        $payload = self::requireAuth();
        
        if (!isset($payload['role']) || $payload['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: Yêu cầu quyền Admin']);
            exit();
        }
        
        return $payload;
    }
}
?>