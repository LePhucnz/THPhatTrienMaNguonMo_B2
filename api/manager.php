<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info .user-details {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .user-info .user-name {
            font-weight: 600;
            font-size: 15px;
        }
        
        .user-info .user-role {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .btn-logout {
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- User Info -->
            <div class="user-info" id="userInfo" style="display: none;">
                <div class="user-details">
                    <div class="avatar" id="userAvatar">👤</div>
                    <div>
                        <div class="user-name" id="userName">--</div>
                        <div class="user-role" id="userRole">--</div>
                    </div>
                </div>
                <button class="btn-logout" onclick="logout()">🚪 Logout</button>
            </div>
            
            <h2>📡 API Endpoints</h2>
            
            <!-- Token Section (ẩn đi, tự động dùng từ localStorage) -->
            <div class="token-section" style="display: none;">
                <h3>🔑 Authorization Token</h3>
                <textarea id="authToken" class="token-input" readonly></textarea>
                <button id="clearToken" class="clear-token-btn" onclick="logout()">🗑️ Xóa Token</button>
            </div>

            <div class="api-group">
                <h3>👤 Accounts</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="POST" data-url="/api/account/register" data-body='{"username":"newuser","fullname":"New User","email":"new@example.com","password":"123456"}'>POST /api/account/register</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/account/login" data-body='{"username":"admin","password":"password"}'>POST /api/account/login</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/account/me">GET /api/account/me 🔒</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/profile" data-body='{"fullname":"Updated Name","phone":"0123456789","address":"New Address"}'>PUT /api/account/profile 🔒</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/change-password" data-body='{"current_password":"password","new_password":"newpass123","confirm_password":"newpass123"}'>PUT /api/account/change-password 🔒</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/account/forgot-password" data-body='{"email":"test@example.com"}'>POST /api/account/forgot-password</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/account/reset-password" data-body='{"token":"reset_token","new_password":"newpass123"}'>POST /api/account/reset-password</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/account">GET /api/account 🔒👑</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/account/1">DELETE /api/account/{id} 🔒👑</a></li>
                </ul>
            </div>

            <div class="api-group">
                <h3>📦 Products</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="GET" data-url="/api/product">GET /api/product</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/product?search=iphone">GET /api/product?search=keyword</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/product?category=1">GET /api/product?category=id</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/product?sort=ASC">GET /api/product?sort=ASC|DESC</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/product/1">GET /api/product/{id}</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/product" data-body='{"name":"Sản phẩm mới","description":"Mô tả","price":100000,"category_id":1,"image":"product.jpg"}'>POST /api/product 🔒👑</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/product/1" data-body='{"name":"Updated","description":"Updated desc","price":200000,"category_id":1,"image":"updated.jpg"}'>PUT /api/product/{id} 🔒👑</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/product/1">DELETE /api/product/{id} 🔒👑</a></li>
                </ul>
            </div>

            <div class="api-group">
                <h3>📂 Categories</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="GET" data-url="/api/category">GET /api/category</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/category/1">GET /api/category/{id}</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/category" data-body='{"name":"Danh mục mới","description":"Mô tả danh mục"}'>POST /api/category 🔒👑</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/category/1" data-body='{"name":"Updated Category","description":"Updated description"}'>PUT /api/category/{id} 🔒👑</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/category/1">DELETE /api/category/{id} 🔒👑</a></li>
                </ul>
            </div>

            <div class="api-group">
                <h3>🛒 Cart</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="GET" data-url="/api/cart">GET /api/cart 🔒</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/cart/add" data-body='{"product_id":1,"quantity":2}'>POST /api/cart/add 🔒</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/cart/update" data-body='{"product_id":1,"quantity":5}'>PUT /api/cart/update 🔒</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/cart/1">DELETE /api/cart/{productId} 🔒</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/cart/clear">DELETE /api/cart/clear 🔒</a></li>
                </ul>
            </div>

            <div class="api-group">
                <h3>📋 Orders</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="POST" data-url="/api/order/create" data-body='{"payment_method":"cod","shipping_address":"123 Đường ABC, Quận 1, TP.HCM","voucher_code":"SALE10"}'>POST /api/order/create 🔒</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/order/my-orders">GET /api/order/my-orders 🔒</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/order/1">GET /api/order/{id} 🔒</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/order/1/cancel">PUT /api/order/{id}/cancel 🔒</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/order/admin/all">GET /api/order/admin/all 🔒👑</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/order/1/status" data-body='{"status":"processing"}'>PUT /api/order/{id}/status 🔒👑</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/order/1/payment" data-body='{"payment_status":"paid"}'>PUT /api/order/{id}/payment 🔒👑</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>🚀 API Manager</h1>
                <p class="subtitle">Công cụ test API trực quan</p>
                <p class="legend">
                    <span class="legend-item">🔒 = Cần token</span>
                    <span class="legend-item">👑 = Admin only</span>
                </p>
            </header>

            <!-- Request Section -->
            <section class="request-section">
                <h2>📤 Request</h2>
                <div class="request-form">
                    <div class="form-row">
                        <select id="method" class="method-select">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        <input type="text" id="url" class="url-input" placeholder="Nhập URL API" value="/api/product">
                        <button id="sendBtn" class="send-btn">🚀 Gửi Request</button>
                    </div>

                    <div class="body-section" id="bodySection" style="display: none;">
                        <label for="requestBody">Request Body (JSON):</label>
                        <textarea id="requestBody" class="request-body">{}</textarea>
                    </div>
                </div>
            </section>

            <!-- Response Section -->
            <section class="response-section">
                <h2>📥 Response</h2>
                <div class="response-info">
                    <span class="status-badge" id="statusBadge">Status: --</span>
                    <span class="time-badge" id="timeBadge">Time: -- ms</span>
                </div>
                <div class="response-body">
                    <pre id="responseBody" class="response-body-content">// Response sẽ hiển thị ở đây...</pre>
                </div>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const API_BASE = 'http://localhost:8080/api';
        
        // Kiểm tra đăng nhập khi vào trang
        $(document).ready(function() {
            const token = localStorage.getItem('api_token');
            const userStr = localStorage.getItem('api_user');
            
            if (!token || !userStr) {
                // Chưa đăng nhập → về trang login
                window.location.href = 'index.php';
                return;
            }
            
            // Hiển thị thông tin user
            try {
                const user = JSON.parse(userStr);
                $('#userName').text(user.fullname || user.username);
                $('#userRole').text(user.role === 'admin' ? '👑 Administrator' : '👤 User');
                $('#userAvatar').text(user.role === 'admin' ? '👑' : '👤');
                $('#userInfo').show();
                $('#authToken').val(token);
            } catch (e) {
                console.error('Error parsing user data:', e);
            }
            
            // Khởi tạo API Manager
            initApiManager();
        });
        
        function initApiManager() {
            // Xử lý click vào endpoint
            $('.api-list a').click(function(e) {
                e.preventDefault();
                
                const method = $(this).data('method');
                const url = $(this).data('url');
                const body = $(this).data('body');
                
                $('#method').val(method);
                $('#url').val(url);
                
                if (body) {
                    $('#requestBody').val(JSON.stringify(body, null, 2));
                }
                
                if (method === 'POST' || method === 'PUT') {
                    $('#bodySection').show();
                } else {
                    $('#bodySection').hide();
                }
            });
            
            // Xử lý thay đổi method
            $('#method').change(function() {
                const method = $(this).val();
                if (method === 'POST' || method === 'PUT') {
                    $('#bodySection').show();
                } else {
                    $('#bodySection').hide();
                }
            });
            
            // Gửi request
            $('#sendBtn').click(function() {
                const method = $('#method').val();
                const url = $('#url').val();
                const body = $('#requestBody').val();
                const token = localStorage.getItem('api_token');
                
                $('#statusBadge').removeClass('success error').text('Status: --').css('background-color', '#e0e0e0');
                $('#timeBadge').text('Time: -- ms');
                $('#responseBody').text('// Đang gửi request...');
                
                const startTime = Date.now();
                
                const headers = {
                    'Content-Type': 'application/json'
                };
                
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }
                
                const ajaxConfig = {
                    url: API_BASE + url,
                    method: method,
                    contentType: 'application/json',
                    dataType: 'json',
                    headers: headers,
                    success: function(data, textStatus, xhr) {
                        const endTime = Date.now();
                        const duration = endTime - startTime;
                        
                        $('#statusBadge')
                            .addClass('success')
                            .css('background-color', '#4caf50')
                            .text('Status: ' + xhr.status + ' ' + xhr.statusText);
                        $('#timeBadge').text('Time: ' + duration + ' ms');
                        $('#responseBody').html(syntaxHighlight(JSON.stringify(data, null, 2)));
                        
                        // Auto-save token nếu login thành công
                        if (url.includes('/account/login') && data.token) {
                            localStorage.setItem('api_token', data.token);
                            localStorage.setItem('api_user', JSON.stringify(data.user));
                            alert('✅ Token đã được cập nhật!');
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        const endTime = Date.now();
                        const duration = endTime - startTime;
                        
                        $('#statusBadge')
                            .addClass('error')
                            .css('background-color', '#f44336')
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
                        
                        // Nếu lỗi 401 → token hết hạn → về login
                        if (xhr.status === 401) {
                            setTimeout(() => {
                                if (confirm('Token đã hết hạn. Đăng nhập lại?')) {
                                    logout();
                                }
                            }, 1000);
                        }
                    }
                };
                
                if ((method === 'POST' || method === 'PUT') && body) {
                    ajaxConfig.data = body;
                }
                
                $.ajax(ajaxConfig);
            });
        }
        
        // Logout
        function logout() {
            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                localStorage.removeItem('api_token');
                localStorage.removeItem('api_user');
                window.location.href = 'index.php';
            }
        }
        
        // Syntax highlighting
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
    </script>
</body>
</html>