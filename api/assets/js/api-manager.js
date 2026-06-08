$(document).ready(function() {
    // Xử lý click vào endpoint trong sidebar
    $('.api-list a').click(function(e) {
        e.preventDefault();
        
        const method = $(this).data('method');
        const url = $(this).data('url');
        const text = $(this).text(); // ✅ Lấy text của link để phân biệt action
        
        $('#method').val(method);
        $('#url').val(url);
        
        // ✅ Truyền thêm text vào hàm để phân biệt action
        updateRequestBody(url, method, text);
        
        // Hiển thị/ẩn body section
        if (method === 'POST' || method === 'PUT') {
            $('#bodySection').show();
        } else {
            $('#bodySection').hide();
        }
    });

    // Hàm cập nhật request body mẫu
    function updateRequestBody(url, method, text) {
        let sampleBody = '';
        
        // ===== PRODUCT =====
        if (url.includes('/api/product') && method === 'POST') {
            sampleBody = JSON.stringify({
                name: "Sản phẩm mới",
                description: "Mô tả sản phẩm",
                price: 100000,
                category_id: 1,
                image: "product.jpg"
            }, null, 2);
        } else if (url.includes('/api/product') && method === 'PUT') {
            sampleBody = JSON.stringify({
                name: "Sản phẩm cập nhật",
                description: "Mô tả mới",
                price: 150000,
                category_id: 1,
                image: "product-updated.jpg"
            }, null, 2);
        }
        // ===== CATEGORY =====
        else if (url.includes('/api/category') && method === 'POST') {
            sampleBody = JSON.stringify({
                name: "Danh mục mới",
                description: "Mô tả danh mục"
            }, null, 2);
        } else if (url.includes('/api/category') && method === 'PUT') {
            sampleBody = JSON.stringify({
                name: "Danh mục cập nhật",
                description: "Mô tả danh mục mới"
            }, null, 2);
        }
        // ===== ACCOUNT =====
        else if (url.includes('/api/account') && method === 'POST') {
            sampleBody = JSON.stringify({
                username: "newuser",
                fullname: "Nguyễn Văn A",
                email: "newuser@example.com",
                password: "password123",
                role: "user",
                security_question: "Tên thú cưng?",
                security_answer: "Mèo"
            }, null, 2);
        }
        // ✅ Phân biệt các action PUT của Account bằng text của link
        else if (url.includes('/api/account') && method === 'PUT' && text.includes('Change Password')) {
            sampleBody = JSON.stringify({
                action: "change_password",
                new_password: "newpassword123"
            }, null, 2);
        } else if (url.includes('/api/account') && method === 'PUT' && text.includes('Toggle Lock')) {
            sampleBody = JSON.stringify({
                action: "toggle_lock"
            }, null, 2);
        } else if (url.includes('/api/account') && method === 'PUT' && text.includes('Update Email')) {
            sampleBody = JSON.stringify({
                action: "update_email",
                email: "newemail@example.com"
            }, null, 2);
        } else if (url.includes('/api/account') && method === 'PUT' && text.includes('Update Avatar')) {
            sampleBody = JSON.stringify({
                action: "update_avatar",
                avatar: "avatar.jpg"
            }, null, 2);
        } else if (url.includes('/api/account') && method === 'PUT' && text.includes('Update Role')) {
            sampleBody = JSON.stringify({
                action: "update_role",
                role: "admin"
            }, null, 2);
        } else if (url.includes('/api/account') && method === 'PUT' && text.includes('Security Question')) {
            sampleBody = JSON.stringify({
                action: "save_security_question",
                security_question: "Tên thú cưng của bạn?",
                security_answer: "Mèo"
            }, null, 2);
        } else if (url.includes('/api/account') && method === 'PUT') {
            // Mặc định: Update Profile
            sampleBody = JSON.stringify({
                action: "update_profile",
                fullname: "Nguyễn Văn A",
                phone: "0123456789",
                address: "Hà Nội"
            }, null, 2);
        }
        
        if (sampleBody) {
            $('#requestBody').val(sampleBody);
        }
    }

    // Xử lý thay đổi method
    $('#method').change(function() {
        const method = $(this).val();
        if (method === 'POST' || method === 'PUT') {
            $('#bodySection').show();
        } else {
            $('#bodySection').hide();
        }
    });

    // Xử lý gửi request
    $('#sendBtn').click(function() {
        const method = $('#method').val();
        const url = $('#url').val();
        const body = $('#requestBody').val();
        
        // Reset response
        $('#statusBadge').removeClass('success error').text('Status: --');
        $('#timeBadge').text('Time: -- ms');
        $('#responseBody').text('// Đang gửi request...');
        
        const startTime = Date.now();
        
        // Cấu hình request
        const ajaxConfig = {
            url: url,
            method: method,
            contentType: 'application/json',
            dataType: 'json',
            success: function(data, textStatus, xhr) {
                const endTime = Date.now();
                const duration = endTime - startTime;
                
                $('#statusBadge')
                    .addClass('success')
                    .text('Status: ' + xhr.status + ' ' + xhr.statusText);
                $('#timeBadge').text('Time: ' + duration + ' ms');
                $('#responseBody').html(syntaxHighlight(JSON.stringify(data, null, 2)));
            },
            error: function(xhr, textStatus, errorThrown) {
                const endTime = Date.now();
                const duration = endTime - startTime;
                
                $('#statusBadge')
                    .addClass('error')
                    .text('Status: ' + xhr.status + ' ' + xhr.statusText);
                $('#timeBadge').text('Time: ' + duration + ' ms');
                
                let errorMessage = 'Error: ' + errorThrown;
                if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMessage = JSON.stringify(errorData, null, 2);
                    } catch (e) {
                        errorMessage = xhr.responseText;
                    }
                }
                $('#responseBody').html('<span style="color: #f44336;">' + escapeHtml(errorMessage) + '</span>');
            }
        };
        
        // Thêm body cho POST và PUT
        if ((method === 'POST' || method === 'PUT') && body) {
            ajaxConfig.data = body;
        }
        
        $.ajax(ajaxConfig);
    });

    // Hàm syntax highlighting cho JSON
    function syntaxHighlight(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
            let cls = 'json-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'json-key';
                } else {
                    cls = 'json-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'json-boolean';
            } else if (/null/.test(match)) {
                cls = 'json-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

    // Hàm escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});