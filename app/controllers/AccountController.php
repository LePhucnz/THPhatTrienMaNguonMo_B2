<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/helpers/SessionHelper.php');

class AccountController {
    private $accountModel;
    private $db;

    public function __construct() {
        $this->db           = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

    private function requireLogin() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }
    }

    private function requireAdmin() {
        $this->requireLogin();
        if (!SessionHelper::isAdmin()) {
            die('<div style="text-align:center;margin-top:100px"><h2>⛔ Không có quyền truy cập</h2><a href="/Product">← Quay lại</a></div>');
        }
    }

    // ==================== ĐĂNG KÝ ====================

    public function register() {
        include_once 'app/views/account/register.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username         = $_POST['username']          ?? '';
            $fullName         = $_POST['fullname']          ?? '';
            $email            = trim($_POST['email']        ?? '');
            $password         = $_POST['password']          ?? '';
            $confirmPassword  = $_POST['confirmpassword']   ?? '';
            $securityQuestion = $_POST['security_question'] ?? '';
            $securityAnswer   = $_POST['security_answer']   ?? '';
            $role             = 'user';
    
            $errors = [];
            if (empty($username))
                $errors['username']    = "Vui lòng nhập username!";
            if (empty($fullName))
                $errors['fullname']    = "Vui lòng nhập họ tên!";
            if (empty($email))
                $errors['email']       = "Vui lòng nhập email!";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors['email']       = "Email không hợp lệ!";
            if (empty($password))
                $errors['password']    = "Vui lòng nhập mật khẩu!";
            if (strlen($password) < 6)
                $errors['password']    = "Mật khẩu ít nhất 6 ký tự!";
            if ($password != $confirmPassword)
                $errors['confirmPass'] = "Mật khẩu xác nhận không khớp!";
            if (empty($securityQuestion))
                $errors['security_question'] = "Vui lòng chọn câu hỏi bảo mật!";
            if (empty($securityAnswer))
                $errors['security_answer']   = "Vui lòng nhập câu trả lời!";
    
            // Kiểm tra trùng username/email (nếu chưa có lỗi khác)
            if (empty($errors)) {
                if ($this->accountModel->getAccountByUsername($username))
                    $errors['username'] = "Username này đã được đăng ký!";
                if ($this->accountModel->getAccountByEmail($email))
                    $errors['email']    = "Email này đã được đăng ký!";
            }
    
            if (!empty($errors)) {
                include_once 'app/views/account/register.php';
                return;
            }
    
            $result = $this->accountModel->save(
                $username, $fullName, $email, $password, $role,
                $securityQuestion, $securityAnswer
            );
    
            if ($result === true) {
                header('Location: /Account/login');
                exit;
            }
        }
    }

    // ==================== ĐĂNG NHẬP ====================

    public function login() {
        // Kiểm tra Remember Me cookie
        if (!SessionHelper::isLoggedIn() && isset($_COOKIE['remember_token'])) {
            $account = $this->accountModel->getAccountByToken($_COOKIE['remember_token']);
            if ($account) {
                SessionHelper::start();
                $_SESSION['user_id']  = $account->id;
                $_SESSION['username'] = $account->username;
                $_SESSION['role']     = $account->role;
                $_SESSION['fullname'] = $account->fullname;
                $_SESSION['avatar']   = $account->avatar;
                header('Location: /Product');
                exit;
            }
        }
        include_once 'app/views/account/login.php';
    }

    public function checkLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input      = trim($_POST['username'] ?? '');  // username hoặc email
            $password   = $_POST['password']      ?? '';
            $rememberMe = isset($_POST['remember_me']);
    
            // Tìm theo username hoặc email
            $account = $this->accountModel->getAccountByUsernameOrEmail($input);
    
            if (!$account) {
                $error = "Không tìm thấy tài khoản!";
                include_once 'app/views/account/login.php';
                return;
            }
    
            if ($account->is_locked) {
                $error = "Tài khoản đã bị khóa. Vui lòng liên hệ Admin!";
                include_once 'app/views/account/login.php';
                return;
            }
    
            if (!password_verify($password, $account->password)) {
                $error = "Mật khẩu không đúng!";
                include_once 'app/views/account/login.php';
                return;
            }
    
            // Đăng nhập thành công
            SessionHelper::start();
            $_SESSION['user_id']  = $account->id;
            $_SESSION['username'] = $account->username;
            $_SESSION['role']     = $account->role;
            $_SESSION['fullname'] = $account->fullname;
            $_SESSION['avatar']   = $account->avatar;
    
            if ($rememberMe) {
                $token  = bin2hex(random_bytes(32));
                $expire = date('Y-m-d H:i:s', strtotime('+30 days'));
                $this->accountModel->setRememberToken($account->id, $token, $expire);
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
    
            header('Location: /Product');
            exit;
        }
    }

    // ==================== ĐĂNG XUẤT ====================

    public function logout() {
        SessionHelper::start();

        // Xóa Remember Me
        if (isset($_COOKIE['remember_token'])) {
            if (isset($_SESSION['user_id'])) {
                $this->accountModel->clearRememberToken($_SESSION['user_id']);
            }
            setcookie('remember_token', '', time() - 3600, '/');
        }

        session_destroy();
        header('Location: /Account/login');
        exit;
    }

    // ==================== HỒ SƠ CÁ NHÂN ====================

    public function profile() {
        $this->requireLogin();
        $account = $this->accountModel->getAccountById($_SESSION['user_id']);
        include 'app/views/account/profile.php';
    }

    public function updateProfile() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone    = trim($_POST['phone']    ?? '');
            $address  = trim($_POST['address']  ?? '');
            $email    = trim($_POST['email']    ?? '');
            $errors   = [];
    
            if (empty($fullname)) $errors[] = "Vui lòng nhập họ tên!";
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ!";
            }
    
            // Kiểm tra email trùng với người khác
            if (!empty($email)) {
                $existing = $this->accountModel->getAccountByEmail($email);
                if ($existing && $existing->id != $_SESSION['user_id']) {
                    $errors[] = "Email này đã được sử dụng!";
                }
            }
    
            // Upload avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                try {
                    $avatarPath = $this->uploadAvatar($_FILES['avatar']);
                    $this->accountModel->updateAvatar($_SESSION['user_id'], $avatarPath);
                    $_SESSION['avatar'] = $avatarPath;
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
    
            if (empty($errors)) {
                $this->accountModel->updateProfile($_SESSION['user_id'], $fullname, $phone, $address);
                if (!empty($email)) {
                    $this->accountModel->updateEmail($_SESSION['user_id'], $email);
                }
                $_SESSION['fullname'] = $fullname;
                $success = "Cập nhật hồ sơ thành công!";
            }
    
            $account = $this->accountModel->getAccountById($_SESSION['user_id']);
            include 'app/views/account/profile.php';
        }
    }

    // ==================== ĐỔI MẬT KHẨU ====================

    public function changePassword() {
        $this->requireLogin();
        include 'app/views/account/change_password.php';
    }

    public function updatePassword() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword     = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $errors          = [];

            $account = $this->accountModel->getAccountById($_SESSION['user_id']);

            if (!password_verify($currentPassword, $account->password)) {
                $errors[] = "Mật khẩu hiện tại không đúng!";
            }
            if (strlen($newPassword) < 6) {
                $errors[] = "Mật khẩu mới ít nhất 6 ký tự!";
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = "Mật khẩu xác nhận không khớp!";
            }

            if (empty($errors)) {
                $this->accountModel->changePassword($_SESSION['user_id'], $newPassword);
                $success = "Đổi mật khẩu thành công!";
            }

            include 'app/views/account/change_password.php';
        }
    }

    // ==================== QUÊN MẬT KHẨU ====================

    // Bước 1: Nhập username
    public function forgotPassword() {
        include 'app/views/account/forgot_password.php';
    }

    // Bước 2: Kiểm tra username → hiển thị câu hỏi bảo mật
    public function verifyUsername() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $account  = $this->accountModel->getAccountByUsername($username);

            if (!$account) {
                $error = "Không tìm thấy tài khoản!";
                include 'app/views/account/forgot_password.php';
                return;
            }

            if (empty($account->security_question)) {
                $error = "Tài khoản này chưa thiết lập câu hỏi bảo mật. Vui lòng liên hệ Admin!";
                include 'app/views/account/forgot_password.php';
                return;
            }

            // Lưu username vào session tạm
            SessionHelper::start();
            $_SESSION['reset_username'] = $username;

            $question = $account->security_question;
            include 'app/views/account/security_question.php';
        }
    }

    // Bước 3: Kiểm tra câu trả lời → hiển thị form đặt mật khẩu mới
    public function verifyAnswer() {
        SessionHelper::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_SESSION['reset_username'] ?? '';
            $answer   = trim($_POST['answer'] ?? '');

            if (empty($username)) {
                header('Location: /Account/forgotPassword');
                exit;
            }

            $account = $this->accountModel->getAccountByUsername($username);

            if (!$account || !password_verify(strtolower($answer), $account->security_answer)) {
                $error    = "Câu trả lời không đúng!";
                $question = $account->security_question ?? '';
                include 'app/views/account/security_question.php';
                return;
            }

            // Đánh dấu đã xác minh
            $_SESSION['reset_verified'] = true;

            include 'app/views/account/reset_password.php';
        }
    }

    // Bước 4: Lưu mật khẩu mới
    public function updateResetPassword() {
        SessionHelper::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username        = $_SESSION['reset_username']  ?? '';
            $verified        = $_SESSION['reset_verified']  ?? false;
            $newPassword     = $_POST['new_password']       ?? '';
            $confirmPassword = $_POST['confirm_password']   ?? '';
            $errors          = [];

            // Chặn truy cập thẳng không qua xác minh
            if (!$verified || empty($username)) {
                header('Location: /Account/forgotPassword');
                exit;
            }

            if (strlen($newPassword) < 6)         $errors[] = "Mật khẩu ít nhất 6 ký tự!";
            if ($newPassword !== $confirmPassword) $errors[] = "Mật khẩu xác nhận không khớp!";

            if (!empty($errors)) {
                include 'app/views/account/reset_password.php';
                return;
            }

            $account = $this->accountModel->getAccountByUsername($username);
            $this->accountModel->changePassword($account->id, $newPassword);

            // Dọn session
            unset($_SESSION['reset_username'], $_SESSION['reset_verified']);

            header('Location: /Account/login');
            exit;
        }
    }

    // Thiết lập câu hỏi bảo mật (trong profile)
    public function setupSecurity() {
        $this->requireLogin();
        include 'app/views/account/setup_security.php';
    }

    public function saveSecurity() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question = $_POST['security_question'] ?? '';
            $answer   = trim($_POST['security_answer'] ?? '');
            $errors   = [];

            if (empty($question)) $errors[] = "Vui lòng chọn câu hỏi!";
            if (empty($answer))   $errors[] = "Vui lòng nhập câu trả lời!";

            if (!empty($errors)) {
                include 'app/views/account/setup_security.php';
                return;
            }

            $this->accountModel->saveSecurityQuestion($_SESSION['user_id'], $question, $answer);
            $success = "Đã lưu câu hỏi bảo mật!";
            include 'app/views/account/setup_security.php';
        }
    }

    // ==================== ADMIN: QUẢN LÝ USER ====================

    public function manageUsers() {
        $this->requireAdmin();
        $accounts = $this->accountModel->getAllAccounts();
        include 'app/views/account/manage_users.php';
    }

    public function toggleLock($id) {
        $this->requireAdmin();
        $this->accountModel->toggleLock($id);
        header('Location: /Account/manageUsers');
        exit;
    }

    public function deleteUser($id) {
        $this->requireAdmin();
        if ($id != $_SESSION['user_id']) {
            $this->accountModel->deleteAccount($id);
        }
        header('Location: /Account/manageUsers');
        exit;
    }

    // ==================== UPLOAD AVATAR ====================

    private function uploadAvatar($file) {
        $target_dir = "public/uploads/avatars/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $extension   = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $target_file = $target_dir . $newFileName;

        if (getimagesize($file["tmp_name"]) === false) throw new Exception("File không phải hình ảnh!");
        if ($file["size"] > 2 * 1024 * 1024)          throw new Exception("Ảnh tối đa 2MB!");
        if (!in_array($extension, ["jpg","jpeg","png","gif","webp"])) {
            throw new Exception("Chỉ chấp nhận JPG, PNG, GIF, WebP!");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Lỗi upload ảnh!");
        }

        return 'uploads/avatars/' . $newFileName;
    }

    // ==================== ADMIN: SỬA NGƯỜI DÙNG ====================

    public function editUser($id) {
        $this->requireAdmin();
        $account = $this->accountModel->getAccountById($id);
        if (!$account) {
            die('Không tìm thấy người dùng.');
        }
        include 'app/views/account/edit_user.php';
    }

    public function updateUser() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id       = $_POST['id']       ?? '';
            $fullname = trim($_POST['fullname'] ?? '');
            $phone    = trim($_POST['phone']    ?? '');
            $address  = trim($_POST['address']  ?? '');
            $role     = $_POST['role']          ?? 'user';
            $errors   = [];

            if (empty($fullname)) $errors[] = "Vui lòng nhập họ tên!";
            if (!in_array($role, ['user', 'admin'])) $errors[] = "Role không hợp lệ!";

            // Không cho tự đổi role chính mình
            if ($id == $_SESSION['user_id'] && $role !== 'admin') {
                $errors[] = "Không thể tự đổi role của chính mình!";
            }

            // Đổi mật khẩu nếu có nhập
            $newPassword = $_POST['new_password'] ?? '';
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $errors[] = "Mật khẩu mới ít nhất 6 ký tự!";
                }
            }

            if (!empty($errors)) {
                $account = $this->accountModel->getAccountById($id);
                include 'app/views/account/edit_user.php';
                return;
            }

            // Cập nhật thông tin
            $this->accountModel->updateProfile($id, $fullname, $phone, $address);

            // Cập nhật role
            $this->accountModel->updateRole($id, $role);

            // Đổi mật khẩu nếu có
            if (!empty($newPassword)) {
                $this->accountModel->changePassword($id, $newPassword);
            }

            header('Location: /Account/manageUsers');
            exit;
        }
    }
}
?>