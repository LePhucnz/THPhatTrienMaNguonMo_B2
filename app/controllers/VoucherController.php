<?php
require_once('app/config/database.php');
require_once('app/models/VoucherModel.php');
require_once('app/helpers/SessionHelper.php');

class VoucherController {
    private $voucherModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->voucherModel = new VoucherModel($this->db);
    }

    // Chỉ admin mới được truy cập
    private function requireAdmin() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            die('
                <div style="text-align:center; margin-top:100px; font-family:sans-serif;">
                    <h2>⛔ Truy cập bị từ chối</h2>
                    <p>Bạn không có quyền thực hiện chức năng này.</p>
                    <a href="/Product" style="color:#28a745;">← Quay về trang chủ</a>
                </div>
            ');
        }
    }

    // Danh sách voucher
    public function index() {
        $this->requireAdmin();
        $query = "SELECT * FROM vouchers ORDER BY id DESC";
        $stmt  = $this->db->prepare($query);
        $stmt->execute();
        $vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);
        include 'app/views/voucher/index.php';
    }

    // Form thêm voucher
    public function add() {
        $this->requireAdmin();
        include 'app/views/voucher/add.php';
    }

    // Lưu voucher mới
    public function save() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $code            = strtoupper(trim($_POST['code'] ?? ''));
            $type            = $_POST['type'] ?? '';
            $value           = $_POST['value'] ?? 0;
            $min_order_value = $_POST['min_order_value'] ?? 0;
            $max_discount    = $_POST['max_discount'] ?? null;
            $usage_limit     = $_POST['usage_limit'] ?? 0;
            $start_date      = $_POST['start_date'] ?? '';
            $end_date        = $_POST['end_date'] ?? '';
            $status          = $_POST['status'] ?? 1;

            if (empty($code))       $errors[] = 'Vui lòng nhập mã voucher.';
            if (empty($type))       $errors[] = 'Vui lòng chọn loại voucher.';
            if ($value <= 0)        $errors[] = 'Giá trị voucher phải lớn hơn 0.';
            if (empty($start_date)) $errors[] = 'Vui lòng chọn ngày bắt đầu.';
            if (empty($end_date))   $errors[] = 'Vui lòng chọn ngày kết thúc.';

            // Kiểm tra mã đã tồn tại chưa
            $check = $this->voucherModel->getVoucherByCode($code);
            if ($check) $errors[] = 'Mã voucher này đã tồn tại.';

            if (!empty($errors)) {
                include 'app/views/voucher/add.php';
                return;
            }

            $query = "INSERT INTO vouchers 
                      (code, type, value, min_order_value, max_discount, usage_limit, start_date, end_date, status)
                      VALUES (:code, :type, :value, :min_order_value, :max_discount, :usage_limit, :start_date, :end_date, :status)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':min_order_value', $min_order_value);
            $stmt->bindParam(':max_discount', $max_discount);
            $stmt->bindParam(':usage_limit', $usage_limit, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: /Voucher');
            exit;
        }
    }

    // Form sửa voucher
    public function edit($id) {
        $this->requireAdmin();
        $query = "SELECT * FROM vouchers WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $voucher = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$voucher) {
            die('Không tìm thấy voucher.');
        }
        include 'app/views/voucher/edit.php';
    }

    // Cập nhật voucher
    public function update() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id              = $_POST['id'];
            $code            = strtoupper(trim($_POST['code'] ?? ''));
            $type            = $_POST['type'] ?? '';
            $value           = $_POST['value'] ?? 0;
            $min_order_value = $_POST['min_order_value'] ?? 0;
            $max_discount    = $_POST['max_discount'] ?? null;
            $usage_limit     = $_POST['usage_limit'] ?? 0;
            $start_date      = $_POST['start_date'] ?? '';
            $end_date        = $_POST['end_date'] ?? '';
            $status          = $_POST['status'] ?? 1;

            $query = "UPDATE vouchers SET 
                      code=:code, type=:type, value=:value, 
                      min_order_value=:min_order_value, max_discount=:max_discount,
                      usage_limit=:usage_limit, start_date=:start_date, 
                      end_date=:end_date, status=:status
                      WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':min_order_value', $min_order_value);
            $stmt->bindParam(':max_discount', $max_discount);
            $stmt->bindParam(':usage_limit', $usage_limit, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: /Voucher');
            exit;
        }
    }

    // Xóa voucher
    public function delete($id) {
        $this->requireAdmin();
        $query = "DELETE FROM vouchers WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: /Voucher');
        exit;
    }

    // Bật/tắt trạng thái voucher
    public function toggle($id) {
        $this->requireAdmin();
        $query = "UPDATE vouchers SET status = IF(status=1, 0, 1) WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: /Voucher');
        exit;
    }
}
?>