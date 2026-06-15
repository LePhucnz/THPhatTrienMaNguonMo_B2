<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AccountModel.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
=======
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/AccountModel.php');
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6

class AccountApiController {
    private $accountModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

<<<<<<< HEAD
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
=======
    // GET /api/account - Lấy danh sách tài khoản
    public function index() {
        header('Content-Type: application/json');
        $accounts = $this->accountModel->getAllAccounts();
        echo json_encode($accounts);
    }

    // GET /api/account/{id} - Lấy tài khoản theo ID
    public function show($id) {
        header('Content-Type: application/json');
        $account = $this->accountModel->getAccountById($id);
        if ($account) {
            echo json_encode($account);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Account not found']);
        }
    }

    // POST /api/account - Tạo tài khoản mới (Register)
    public function store() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $username       = $data['username']       ?? '';
        $fullname       = $data['fullname']       ?? '';
        $email          = $data['email']          ?? '';
        $password       = $data['password']       ?? '';
        $role           = $data['role']           ?? 'user';
        $securityQuestion = $data['security_question'] ?? '';
        $securityAnswer   = $data['security_answer']   ?? '';

        $result = $this->accountModel->save(
            $username, 
            $fullname, 
            $email, 
            $password, 
            $role, 
            $securityQuestion, 
            $securityAnswer
        );

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Account created successfully']);
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
        } else {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        }
    }

<<<<<<< HEAD
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
=======
    // PUT /api/account/{id} - Cập nhật tài khoản
    public function update($id) {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $action = $data['action'] ?? 'update_profile';

        switch ($action) {
            case 'update_profile':
                $fullname = $data['fullname'] ?? '';
                $phone    = $data['phone']    ?? '';
                $address  = $data['address']  ?? '';
                $result = $this->accountModel->updateProfile($id, $fullname, $phone, $address);
                break;

            case 'update_email':
                $email = $data['email'] ?? '';
                $result = $this->accountModel->updateEmail($id, $email);
                break;

            case 'update_avatar':
                $avatar = $data['avatar'] ?? '';
                $result = $this->accountModel->updateAvatar($id, $avatar);
                break;

            case 'change_password':
                $newPassword = $data['new_password'] ?? '';
                $result = $this->accountModel->changePassword($id, $newPassword);
                break;

            case 'update_role':
                $role = $data['role'] ?? '';
                $result = $this->accountModel->updateRole($id, $role);
                break;

            case 'toggle_lock':
                $result = $this->accountModel->toggleLock($id);
                break;

            case 'save_security_question':
                $question = $data['security_question'] ?? '';
                $answer   = $data['security_answer']   ?? '';
                $result = $this->accountModel->saveSecurityQuestion($id, $question, $answer);
                break;

            default:
                http_response_code(400);
                echo json_encode(['message' => 'Invalid action']);
                return;
        }

        if ($result) {
            echo json_encode(['message' => 'Account updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Account update failed']);
        }
    }

    // DELETE /api/account/{id} - Xóa tài khoản
    public function destroy($id) {
        header('Content-Type: application/json');
        $result = $this->accountModel->deleteAccount($id);
        if ($result) {
            echo json_encode(['message' => 'Account deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Account deletion failed']);
        }
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
    }
}
?>