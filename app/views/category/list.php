<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">
        <i class="fas fa-folder"></i> Quản lý danh mục
    </h1>
    <a href="/Category/add" class="btn btn-success mb-3">
        <i class="fas fa-plus"></i> Thêm danh mục mới
    </a>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Số sản phẩm</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        foreach($categories as $category): 
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($category->name); ?></td>
                            <td><?php echo htmlspecialchars($category->description); ?></td>
                            <td>
                                <a href="/Product?category=<?php echo $category->id; ?>" 
                                   class="badge badge-info">
                                    Xem sản phẩm
                                </a>
                            </td>
                            <td>
                                <a href="/Category/edit/<?php echo $category->id; ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="/Category/delete/<?php echo $category->id; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>