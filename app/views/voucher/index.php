<?php 
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-ticket-alt"></i> Quản lý Voucher</h3>
    <a href="/Voucher/add" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm Voucher
    </a>
</div>

<?php if(empty($vouchers)): ?>
<div class="alert alert-info">Chưa có voucher nào.</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Mã</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Đơn tối thiểu</th>
                <th>Giảm tối đa</th>
                <th>Đã dùng / Giới hạn</th>
                <th>Thời hạn</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($vouchers as $v): ?>
            <?php
            $now     = time();
            $start   = strtotime($v->start_date);
            $end     = strtotime($v->end_date);
            $expired = $now > $end;
            ?>
            <tr class="<?php echo $expired ? 'table-secondary' : ''; ?>">
                <td><code class="text-primary font-weight-bold"><?php echo htmlspecialchars($v->code); ?></code></td>
                <td>
                    <?php if($v->type == 'percent'): ?>
                        <span class="badge badge-info">% Phần trăm</span>
                    <?php elseif($v->type == 'fixed'): ?>
                        <span class="badge badge-warning">Cố định</span>
                    <?php else: ?>
                        <span class="badge badge-success">Freeship</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($v->type == 'percent'): ?>
                        <?php echo $v->value; ?>%
                    <?php elseif($v->type == 'freeship'): ?>
                        Miễn phí ship
                    <?php else: ?>
                        <?php echo number_format($v->value, 0, ',', '.'); ?> đ
                    <?php endif; ?>
                </td>
                <td><?php echo number_format($v->min_order_value, 0, ',', '.'); ?> đ</td>
                <td>
                    <?php echo $v->max_discount ? number_format($v->max_discount, 0, ',', '.') . ' đ' : '-'; ?>
                </td>
                <td>
                    <span class="badge badge-secondary">
                        <?php echo $v->used_count; ?> / <?php echo $v->usage_limit > 0 ? $v->usage_limit : '∞'; ?>
                    </span>
                </td>
                <td>
                    <small>
                        <?php echo date('d/m/Y', strtotime($v->start_date)); ?> 
                        → 
                        <?php echo date('d/m/Y', strtotime($v->end_date)); ?>
                        <?php if($expired): ?>
                            <br><span class="text-danger">⚠ Hết hạn</span>
                        <?php endif; ?>
                    </small>
                </td>
                <td>
                    <?php if($v->status == 1 && !$expired): ?>
                        <span class="badge badge-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Tắt</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/Voucher/edit/<?php echo $v->id; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/Voucher/toggle/<?php echo $v->id; ?>" class="btn btn-secondary btn-sm"
                       title="Bật/Tắt">
                        <i class="fas fa-power-off"></i>
                    </a>
                    <a href="/Voucher/delete/<?php echo $v->id; ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Xóa voucher này?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>