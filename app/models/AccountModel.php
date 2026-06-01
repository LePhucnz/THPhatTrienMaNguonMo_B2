<?php
class AccountModel {
    private $conn;
    private $table_name = "account";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAccountByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAccountById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAccountByToken($token) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE remember_token = :token AND token_expire > NOW() LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAccountByResetToken($token) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE reset_token = :token AND reset_expire > NOW() LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAllAccounts() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Tìm tài khoản theo email
    public function getAccountByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE email = :email LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Tìm theo username HOẶC email (dùng cho đăng nhập)
    public function getAccountByUsernameOrEmail($input) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE username = :input OR email = :input2 LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":input",  $input);
        $stmt->bindParam(":input2", $input);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save($username, $fullName, $email, $password, $role = 'user', $securityQuestion = '', $securityAnswer = '') {
        if ($this->getAccountByUsername($username)) return ['field' => 'username', 'message' => 'Username này đã được đăng ký!'];
        if (!empty($email) && $this->getAccountByEmail($email)) return ['field' => 'email', 'message' => 'Email này đã được đăng ký!'];
    
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=:username, fullname=:fullname, email=:email,
                      password=:password, role=:role, 
                      security_question=:question, security_answer=:answer";
        $stmt = $this->conn->prepare($query);
    
        $username       = htmlspecialchars(strip_tags($username));
        $fullName       = htmlspecialchars(strip_tags($fullName));
        $email          = htmlspecialchars(strip_tags($email));
        $password       = password_hash($password, PASSWORD_BCRYPT);
        $role           = htmlspecialchars(strip_tags($role));
        $securityAnswer = password_hash(strtolower(trim($securityAnswer)), PASSWORD_BCRYPT);
    
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":fullname", $fullName);
        $stmt->bindParam(":email",    $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role",     $role);
        $stmt->bindParam(":question", $securityQuestion);
        $stmt->bindParam(":answer",   $securityAnswer);
    
        return $stmt->execute() ? true : false;
    }
    
    // Cập nhật email trong profile
    public function updateEmail($id, $email) {
        $query = "UPDATE " . $this->table_name . " SET email=:email WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id",    $id,    PDO::PARAM_INT);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }

    // ✅ Cập nhật hồ sơ cá nhân
    public function updateProfile($id, $fullname, $phone, $address) {
        $query = "UPDATE " . $this->table_name . " SET fullname=:fullname, phone=:phone, address=:address WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);
        return $stmt->execute();
    }

    

    // ✅ Cập nhật avatar
    public function updateAvatar($id, $avatar) {
        $query = "UPDATE " . $this->table_name . " SET avatar=:avatar WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":avatar", $avatar);
        return $stmt->execute();
    }

    // ✅ Đổi mật khẩu
    public function changePassword($id, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $query  = "UPDATE " . $this->table_name . " SET password=:password WHERE id=:id";
        $stmt   = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":password", $hashed);
        return $stmt->execute();
    }

    // ✅ Remember Me
    public function setRememberToken($id, $token, $expire) {
        $query = "UPDATE " . $this->table_name . " SET remember_token=:token, token_expire=:expire WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expire", $expire);
        return $stmt->execute();
    }

    public function clearRememberToken($id) {
        $query = "UPDATE " . $this->table_name . " SET remember_token=NULL, token_expire=NULL WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ✅ Reset password token
    public function setResetToken($id, $token, $expire) {
        $query = "UPDATE " . $this->table_name . " SET reset_token=:token, reset_expire=:expire WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expire", $expire);
        return $stmt->execute();
    }

    public function clearResetToken($id) {
        $query = "UPDATE " . $this->table_name . " SET reset_token=NULL, reset_expire=NULL WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ✅ Khóa / Mở khóa tài khoản (Admin)
    public function toggleLock($id) {
        $query = "UPDATE " . $this->table_name . " SET is_locked = IF(is_locked=1, 0, 1) WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ✅ Xóa tài khoản (Admin)
    public function deleteAccount($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Lưu câu hỏi & câu trả lời bảo mật
    public function saveSecurityQuestion($id, $question, $answer) {
        $hashed = password_hash(strtolower(trim($answer)), PASSWORD_BCRYPT);
        $query  = "UPDATE " . $this->table_name . " 
                SET security_question=:question, security_answer=:answer 
                WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id",       $id,       PDO::PARAM_INT);
        $stmt->bindParam(":question", $question);
        $stmt->bindParam(":answer",   $hashed);
        return $stmt->execute();
    }

    // Lấy câu hỏi bảo mật theo username
    public function getSecurityQuestion($username) {
        $query = "SELECT id, security_question, security_answer 
                FROM " . $this->table_name . " 
                WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateRole($id, $role) {
        $query = "UPDATE " . $this->table_name . " SET role=:role WHERE id=:id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id",   $id,   PDO::PARAM_INT);
        $stmt->bindParam(":role", $role);
        return $stmt->execute();
    }
}
?>