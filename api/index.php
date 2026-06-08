<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>📡 API Endpoints</h2>
            
            <div class="api-group">
                <h3>📦 Products</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="GET" data-url="/api/product">GET /api/product</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/product/1">GET /api/product/{id}</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/product">POST /api/product</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/product/1">PUT /api/product/{id}</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/product/1">DELETE /api/product/{id}</a></li>
                </ul>
            </div>

            <div class="api-group">
                <h3>📂 Categories</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="GET" data-url="/api/category">GET /api/category</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/category/1">GET /api/category/{id}</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/category">POST /api/category</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/category/1">PUT /api/category/{id}</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/category/1">DELETE /api/category/{id}</a></li>
                </ul>
            </div>

            <div class="api-group">
            <h3>👤 Accounts</h3>
                <ul class="api-list">
                    <li><a href="#" data-method="GET" data-url="/api/account">GET /api/account</a></li>
                    <li><a href="#" data-method="GET" data-url="/api/account/1">GET /api/account/{id}</a></li>
                    <li><a href="#" data-method="POST" data-url="/api/account">POST /api/account (Register)</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Update Profile</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Update Email</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Update Avatar</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Change Password</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Update Role</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Toggle Lock</a></li>
                    <li><a href="#" data-method="PUT" data-url="/api/account/1">PUT /api/account/{id} - Security Question</a></li>
                    <li><a href="#" data-method="DELETE" data-url="/api/account/1">DELETE /api/account/{id}</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>🚀 API Manager</h1>
                <p class="subtitle">Công cụ test API trực quan</p>
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
                        <textarea id="requestBody" class="request-body">{
  "name": "Sản phẩm mới",
  "description": "Mô tả sản phẩm",
  "price": 100000,
  "category_id": 1,
  "image": "product.jpg"
}</textarea>
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
    <script src="assets/js/api-manager.js"></script>
</body>
</html>