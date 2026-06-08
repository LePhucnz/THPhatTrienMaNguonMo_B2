<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/AccountModel.php');

class AccountApiController {
    private $accountModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

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
        } else {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        }
    }

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
    }
}
?>