<?php 
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php'; 
?>

<h3><i class="fas fa-edit"></i> Sửa Voucher</h3>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/Voucher/update">
            <input type="hidden" name="id" value="<?php echo $voucher->id; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Mã Voucher</label>
                    <input type="text" name="code" class="form-control text-uppercase"
                           value="<?php echo htmlspecialchars($voucher->code); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Loại Voucher</label>
                    <select name="type" class="form-control" id="type" onchange="toggleFields()">
                        <option value="percent"  <?php echo $voucher->type == 'percent'  ? 'selected' : ''; ?>>Phần trăm (%)</option>
                        <option value="fixed"    <?php echo $voucher->type == 'fixed'    ? 'selected' : ''; ?>>Cố định (đ)</option>
                        <option value="freeship" <?php echo $voucher->type == 'freeship' ? 'selected' : ''; ?>>Miễn phí vận chuyển</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3" id="value-group">
                    <label>Giá trị</label>
                    <input type="number" name="value" class="form-control"
                           value="<?php echo $voucher->value; ?>" min="0" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Đơn hàng tối thiểu (đ)</label>
                    <input type="number" name="min_order_value" class="form-control"
                           value="<?php echo $voucher->min_order_value; ?>" min="0">
                </div>
                <div class="col-md-4 mb-3" id="max-discount-group">
                    <label>Giảm tối đa (đ)</label>
                    <input type="number" name="max_discount" class="form-control"
                           value="<?php echo $voucher->max_discount; ?>" min="0">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit" class="form-control"
                           value="<?php echo $voucher->usage_limit; ?>" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="form-control"
                           value="<?php echo $voucher->start_date; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control"
                           value="<?php echo $voucher->end_date; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1" <?php echo $voucher->status == 1 ? 'selected' : ''; ?>>Hoạt động</option>
                    <option value="0" <?php echo $voucher->status == 0 ? 'selected' : ''; ?>>Tắt</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
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