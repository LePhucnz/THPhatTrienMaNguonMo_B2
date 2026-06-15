<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AccountModel.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AccountApiController {
    private $accountModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

    // POST /api/account/register
    public function register() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $username = trim($data['username'] ?? '');
        $fullname = trim($data['fullname'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $secQ = $data['security_question'] ?? '';
        $secA = $data['security_answer'] ?? '';

        $errors = [];
        if (empty($username)) $errors['username'] = 'Vui lòng nhập username';
        if (empty($fullname)) $errors['fullname'] = 'Vui lòng nhập họ tên';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email không hợp lệ';
        if (strlen($password) < 6) $errors['password'] = 'Mật khẩu ít nhất 6 ký tự';
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return;
        }

        $result = $this->accountModel->save($username, $fullname, $email, $password, 'user', $secQ, $secA);
        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Đăng ký thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        }
    }

    // POST /api/account/login
    public function login() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $input = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($input) || empty($password)) {
            http_response_code(400);
            echo json_encode(['message' => 'Vui lòng nhập username và password']);
            return;
        }

        $account = $this->accountModel->getAccountByUsernameOrEmail($input);
        
        if (!$account) {
            http_response_code(401);
            echo json_encode(['message' => 'Tài khoản không tồn tại']);
            return;
        }
        
        if ($account->is_locked) {
            http_response_code(403);
            echo json_encode(['message' => 'Tài khoản bị khóa']);
            return;
        }
        
        if (!password_verify($password, $account->password)) {
            http_response_code(401);
            echo json_encode(['message' => 'Mật khẩu không đúng']);
            return;
        }

        $token = JwtHelper::generate([
            'id' => $account->id,
            'username' => $account->username,
            'role' => $account->role,
            'fullname' => $account->fullname,
        ]);

        echo json_encode([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => [
                'id' => $account->id,
                'username' => $account->username,
                'fullname' => $account->fullname,
                'role' => $account->role,
                'avatar' => $account->avatar ?? '',
            ]
        ]);
    }

    // GET /api/account/me
    public function me() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        $account = $this->accountModel->getAccountById($payload['id']);
        
        if (!$account) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy tài khoản']);
            return;
        }
        
        unset($account->password, $account->security_answer, $account->remember_token, $account->reset_token, $account->reset_expires);
        echo json_encode($account);
    }

    // PUT /api/account/profile
    public function updateProfile() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        $fullname = trim($data['fullname'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');

        if (empty($fullname)) {
            http_response_code(400);
            echo json_encode(['message' => 'Họ tên không được rỗng']);
            return;
        }
        
        $this->accountModel->updateProfile($payload['id'], $fullname, $phone, $address);
        echo json_encode(['message' => 'Cập nhật hồ sơ thành công']);
    }

    // PUT /api/account/change-password
    public function changePassword() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        $current = $data['current_password'] ?? '';
        $newPass = $data['new_password'] ?? '';
        $confirmPass = $data['confirm_password'] ?? '';

        $account = $this->accountModel->getAccountById($payload['id']);
        
        if (!password_verify($current, $account->password)) {
            http_response_code(400);
            echo json_encode(['message' => 'Mật khẩu hiện tại không đúng']);
            return;
        }
        
        if (strlen($newPass) < 6) {
            http_response_code(400);
            echo json_encode(['message' => 'Mật khẩu mới ít nhất 6 ký tự']);
            return;
        }
        
        if ($newPass !== $confirmPass) {
            http_response_code(400);
            echo json_encode(['message' => 'Mật khẩu xác nhận không khớp']);
            return;
        }
        
        $this->accountModel->changePassword($payload['id'], $newPass);
        echo json_encode(['message' => 'Đổi mật khẩu thành công']);
    }

    // POST /api/account/forgot-password
    public function forgotPassword() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $email = trim($data['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['message' => 'Email không hợp lệ']);
            return;
        }

        $account = $this->accountModel->getAccountByEmail($email);

        if (!$account) {
            echo json_encode(['message' => 'Nếu email tồn tại, liên kết đặt lại đã được gửi']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->accountModel->setResetToken($account->id, $token, $expiry);

        $resetLink = "http://yourdomain.com/reset-password?token=" . $token;

        echo json_encode([
            'message' => 'Nếu email tồn tại, liên kết đặt lại đã được gửi',
            'reset_link' => $resetLink
        ]);
    }

    // POST /api/account/reset-password
    public function resetPassword() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $token = $data['token'] ?? '';
        $newPass = $data['new_password'] ?? '';

        if (empty($token) || empty($newPass)) {
            http_response_code(400);
            echo json_encode(['message' => 'Token và mật khẩu mới là bắt buộc']);
            return;
        }

        if (strlen($newPass) < 6) {
            http_response_code(400);
            echo json_encode(['message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
            return;
        }

        $account = $this->accountModel->getAccountByResetToken($token);

        if (!$account) {
            http_response_code(400);
            echo json_encode(['message' => 'Token không hợp lệ hoặc đã hết hạn']);
            return;
        }

        $this->accountModel->changePassword($account->id, $newPass);
        $this->accountModel->clearResetToken($account->id);

        echo json_encode(['message' => 'Đặt lại mật khẩu thành công']);
    }

    // GET /api/account (Admin only)
    public function index() {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin();
        echo json_encode($this->accountModel->getAllAccounts());
    }

    // PUT /api/account/{id}/toggle-lock (Admin only)
    public function toggleLock($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin();
        $this->accountModel->toggleLock($id);
        echo json_encode(['message' => 'Đã cập nhật trạng thái tài khoản']);
    }

    // DELETE /api/account/{id} (Admin only)
    public function destroy($id) {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAdmin();
        
        if ($id == $payload['id']) {
            http_response_code(400);
            echo json_encode(['message' => 'Không thể xóa chính mình']);
            return;
        }
        
        $this->accountModel->deleteAccount($id);
        echo json_encode(['message' => 'Đã xóa tài khoản']);
    }
}
?>