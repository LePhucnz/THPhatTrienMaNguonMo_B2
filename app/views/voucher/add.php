<?php 
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php'; 
?>

<h3><i class="fas fa-plus"></i> Thêm Voucher mới</h3>

<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach($errors as $e): ?>
            <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/Voucher/save">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Mã Voucher <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control text-uppercase"
                           placeholder="VD: SALE10, FREESHIP"
                           value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Loại Voucher <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" id="type" onchange="toggleFields()" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="percent"  <?php echo ($_POST['type'] ?? '') == 'percent'  ? 'selected' : ''; ?>>Phần trăm (%)</option>
                        <option value="fixed"    <?php echo ($_POST['type'] ?? '') == 'fixed'    ? 'selected' : ''; ?>>Cố định (đ)</option>
                        <option value="freeship" <?php echo ($_POST['type'] ?? '') == 'freeship' ? 'selected' : ''; ?>>Miễn phí vận chuyển</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3" id="value-group">
                    <label>Giá trị <span class="text-danger">*</span></label>
                    <input type="number" name="value" class="form-control" 
                           placeholder="VD: 10 (%) hoặc 50000 (đ)"
                           value="<?php echo htmlspecialchars($_POST['value'] ?? ''); ?>" min="0" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Đơn hàng tối thiểu (đ)</label>
                    <input type="number" name="min_order_value" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['min_order_value'] ?? '0'); ?>" min="0">
                </div>
                <div class="col-md-4 mb-3" id="max-discount-group">
                    <label>Giảm tối đa (đ)</label>
                    <input type="number" name="max_discount" class="form-control"
                           placeholder="Để trống = không giới hạn"
                           value="<?php echo htmlspecialchars($_POST['max_discount'] ?? ''); ?>" min="0">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['usage_limit'] ?? '0'); ?>" min="0">
                    <small class="text-muted">0 = không giới hạn</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['start_date'] ?? date('Y-m-d')); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ngày kết thúc <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1">Hoạt động</option>
                    <option value="0">Tắt</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Lưu Voucher
            </button>
            <a href="/Voucher" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('type').value;
    const maxGroup = document.getElementById('max-discount-group');
    const valueGroup = document.getElementById('value-group');
    
    if (type === 'freeship') {
        valueGroup.style.display = 'none';
        maxGroup.style.display = 'none';
    } else if (type === 'fixed') {
        valueGroup.style.display = 'block';
        maxGroup.style.display = 'none';
    } else {
        valueGroup.style.display = 'block';
        maxGroup.style.display = 'block';
    }
}
toggleFields();
</script>

<?php include 'app/views/shares/footer.php'; ?>