# Cheney Admin System

基于 Laravel 的后台管理系统 Composer 扩展包，提供完整的用户管理、角色管理、权限管理、菜单管理和操作日志功能。

## 功能特性

- ✅ 用户管理 - 管理员账号的增删改查、角色分配、密码重置、状态管理
- ✅ 角色管理 - 角色的增删改查、权限分配、用户分配
- ✅ 权限管理 - 权限的增删改查、权限分组
- ✅ 菜单管理 - 菜单的增删改查、树形结构展示、权限关联
- ✅ 操作日志 - 记录用户操作行为、日志查询、统计、导出
- ✅ JWT 认证 - 基于 JWT 的用户认证机制
- ✅ RBAC 权限控制 - 基于角色的访问控制
- ✅ Swagger API 文档 - 自动生成 API 文档
- ✅ 单元测试 - 完整的单元测试覆盖
- ✅ Service 层架构 - 业务逻辑封装在 Service 层
- ✅ CORS 支持 - 跨域请求支持

## 技术栈

- Laravel 8+
- PHP 7.4+
- MySQL 5.7+
- Redis
- JWT Auth
- Swagger

## 安装

### 1. 安装 Composer 包

```bash
composer require cheney/admin-system
```

### 2. 发布配置文件

```bash
php artisan vendor:publish --provider="Cheney\AdminSystem\AdminSystemServiceProvider" --tag="admin-config"
```

### 3. 发布数据库迁移文件

```bash
php artisan vendor:publish --provider="Cheney\AdminSystem\AdminSystemServiceProvider" --tag="admin-migrations"
```

### 4. 运行数据库迁移

```bash
php artisan migrate
```

### 5. 运行数据填充

```bash
php artisan db:seed --class=Cheney\\AdminSystem\\Database\\Seeders\\AdminSeeder
php artisan db:seed --class=Cheney\\AdminSystem\\Database\\Seeders\\RoleSeeder
php artisan db:seed --class=Cheney\\AdminSystem\\Database\\Seeders\\PermissionSeeder
php artisan db:seed --class=Cheney\\AdminSystem\\Database\\Seeders\\MenuSeeder
php artisan db:seed --class=Cheney\\AdminSystem\\Database\\Seeders\\RoleAdminSeeder
php artisan db:seed --class=Cheney\\AdminSystem\\Database\\Seeders\\PermissionRoleSeeder
```

### 6. 生成 JWT Secret

```bash
php artisan jwt:secret
```

### 7. 配置环境变量

在 `.env` 文件中添加以下配置：

```env
JWT_SECRET=your-jwt-secret-key
JWT_TTL=1440
JWT_REFRESH_TTL=20160
```

## 配置

### 配置文件说明

发布配置文件后，可以在 `config/admin.php` 中进行配置：

```php
return [
    // 路由前缀
    'route_prefix' => 'system',

    // 中间件
    'middleware' => [
        'auth' => 'jwt',
        'permission' => 'permission',
    ],

    // JWT 配置
    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'ttl' => env('JWT_TTL', 1440),
        'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
    ],

    // 分页配置
    'pagination' => [
        'per_page' => 15,
    ],

    // 操作日志配置
    'operation_log' => [
        'enabled' => true,
        'except' => [
            'login',
            'logout',
        ],
    ],

    // 上传配置
    'upload' => [
        'disk' => 'public',
        'path' => 'uploads',
    ],

    // 默认管理员
    'default_admin' => [
        'username' => 'admin',
        'password' => 'admin123',
        'name' => '超级管理员',
    ],
];
```

## 使用方法

### 1. 认证

#### 登录

```bash
POST /api/system/auth/login
Content-Type: application/json

{
    "username": "admin",
    "password": "admin123"
}
```

响应：

```json
{
    "code": 10000,
    "message": "登录成功",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 86400,
        "user": {
            "id": 1,
            "username": "admin",
            "name": "超级管理员",
            "email": "admin@example.com",
            "avatar": null
        }
    }
}
```

#### 获取当前用户信息

```bash
GET /api/system/auth/me
Authorization: Bearer {access_token}
```

#### 退出登录

```bash
POST /api/system/auth/logout
Authorization: Bearer {access_token}
```

#### 刷新 Token

```bash
POST /api/system/auth/refresh
Authorization: Bearer {access_token}
```

### 2. 用户管理

#### 获取用户列表

```bash
GET /api/system/admins
Authorization: Bearer {access_token}
```

#### 创建用户

```bash
POST /api/system/admins
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "username": "testuser",
    "password": "password123",
    "name": "测试用户",
    "email": "test@example.com",
    "phone": "13800138000",
    "status": 1
}
```

#### 更新用户

```bash
PUT /api/system/admins/{id}
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "更新后的名称",
    "email": "newemail@example.com"
}
```

#### 删除用户

```bash
DELETE /api/system/admins/{id}
Authorization: Bearer {access_token}
```

#### 分配角色

```bash
POST /api/system/admins/{id}/roles
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "role_ids": [1, 2, 3]
}
```

#### 重置密码

```bash
POST /api/system/admins/{id}/reset-password
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "password": "newpassword123"
}
```

#### 修改状态

```bash
POST /api/system/admins/{id}/change-status
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "status": 0
}
```

### 3. 角色管理

#### 获取角色列表

```bash
GET /api/system/roles
Authorization: Bearer {access_token}
```

#### 创建角色

```bash
POST /api/system/roles
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "编辑",
    "code": "editor",
    "description": "内容编辑角色"
}
```

#### 分配权限

```bash
POST /api/system/roles/{id}/permissions
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "permission_ids": [1, 2, 3, 4]
}
```

#### 分配用户

```bash
POST /api/system/roles/{id}/admins
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "admin_ids": [1, 2, 3]
}
```

### 4. 权限管理

#### 获取权限列表

```bash
GET /api/system/permissions
Authorization: Bearer {access_token}
```

#### 创建权限

```bash
POST /api/system/permissions
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "name": "用户管理",
    "code": "user.manage",
    "type": "menu",
    "description": "用户管理权限"
}
```

### 5. 菜单管理

#### 获取菜单列表

```bash
GET /api/system/menus
Authorization: Bearer {access_token}
```

#### 获取菜单树

```bash
GET /api/system/menus/tree
Authorization: Bearer {access_token}
```

#### 获取当前用户菜单

```bash
GET /api/system/user-menus
Authorization: Bearer {access_token}
```

#### 创建菜单

```bash
POST /api/system/menus
Authorization: Bearer {access_token}
Content-Type: application/json

{
    "parent_id": 0,
    "name": "用户管理",
    "path": "/user",
    "icon": "user",
    "sort": 1,
    "permission_id": 1
}
```

### 6. 操作日志

#### 获取操作日志列表

```bash
GET /api/system/operation-logs
Authorization: Bearer {access_token}
```

#### 获取日志统计

```bash
GET /api/system/operation-logs/statistics
Authorization: Bearer {access_token}
```

#### 导出日志

```bash
POST /api/system/operation-logs/export
Authorization: Bearer {access_token}
```

#### 清空日志

```bash
POST /api/system/operation-logs/clear
Authorization: Bearer {access_token}
```

## Facade 使用

本扩展包提供了多个 Facade 方便调用：

```php
use Cheney\AdminSystem\Facades\AdminAuth;
use Cheney\AdminSystem\Facades\AdminUser;
use Cheney\AdminSystem\Facades\AdminRole;
use Cheney\AdminSystem\Facades\AdminPermission;
use Cheney\AdminSystem\Facades\AdminMenu;
use Cheney\AdminSystem\Facades\AdminOperationLog;

// 认证
$token = AdminAuth::login($credentials);
$user = AdminAuth::me();

// 用户管理
$users = AdminUser::getList($params);
AdminUser::create($data);

// 角色管理
$roles = AdminRole::getList($params);
AdminRole::create($data);

// 权限管理
$permissions = AdminPermission::getList($params);
AdminPermission::create($data);

// 菜单管理
$menus = AdminMenu::getTree();
AdminMenu::create($data);

// 操作日志
$logs = AdminOperationLog::getList($params);
AdminOperationLog::create($data);
```

## 中间件

### JWT 认证中间件

在路由中使用 JWT 认证：

```php
Route::middleware('jwt')->group(function () {
    // 需要认证的路由
});
```

### 权限检查中间件

在路由中使用权限检查：

```php
Route::middleware('permission:user.manage')->group(function () {
    // 需要 user.manage 权限的路由
});
```

### 操作日志中间件

自动记录用户操作：

```php
Route::middleware('operation.log')->group(function () {
    // 需要记录操作日志的路由
});
```

### CORS 中间件

处理跨域请求：

```php
Route::middleware('cors')->group(function () {
    // 需要处理跨域的路由
});
```

## 数据库表结构

### admins 表

管理员表，存储系统管理员信息。

### roles 表

角色表，存储系统角色信息。

### permissions 表

权限表，存储系统权限信息。

### menus 表

菜单表，存储系统菜单信息。

### role_admin 表

角色管理员关联表，多对多关系。

### permission_role 表

权限角色关联表，多对多关系。

### operation_logs 表

操作日志表，记录用户操作行为。

## 单元测试

运行单元测试：

```bash
php artisan test --filter="Cheney\\AdminSystem\\Tests"
```

测试文件位于 `src/tests/Feature/` 目录下：

- AuthTest.php - 认证功能测试
- AdminTest.php - 用户管理测试
- RoleTest.php - 角色管理测试
- PermissionTest.php - 权限管理测试
- MenuTest.php - 菜单管理测试
- OperationLogTest.php - 操作日志测试

## Swagger API 文档

生成 Swagger API 文档：

```bash
php artisan l5-swagger:generate
```

访问文档：

```
http://your-domain.com/api/documentation
```

## 默认账号

系统初始化后会创建默认管理员账号：

- 用户名：admin
- 密码：admin123

**⚠️ 请在生产环境中立即修改默认密码！**

## API 响应格式

所有 API 接口统一返回格式：

```json
{
    "code": 10000,
    "message": "操作成功",
    "data": {}
}
```

- code: 10000 表示成功，20000 表示失败
- message: 操作结果消息
- data: 返回的数据

## 目录结构

```
composer-package/
├── config/              # 配置文件
│   ├── admin.php       # 管理系统配置
│   ├── jwt.php         # JWT 配置
│   └── ...
├── database/
│   ├── factories/      # 数据工厂
│   ├── migrations/     # 数据库迁移
│   └── seeders/        # 数据填充
├── routes/
│   └── api.php         # API 路由
├── src/
│   ├── Controllers/    # 控制器
│   ├── Exceptions/     # 异常类
│   ├── Facades/        # 门面类
│   ├── Middleware/     # 中间件
│   ├── Models/         # 模型
│   ├── Services/       # 服务层
│   ├── Traits/         # 特性
│   ├── tests/          # 单元测试
│   └── AdminSystemServiceProvider.php
└── composer.json       # Composer 配置
```

## 常见问题

### 1. JWT Token 过期

Token 默认有效期为 24 小时，可以使用 refresh 接口刷新 Token。

### 2. 权限验证失败

确保用户已分配相应角色，角色已分配相应权限。

### 3. CORS 错误

确保已在路由中应用 CORS 中间件。

### 4. 操作日志未记录

检查操作日志中间件是否已正确配置，排除不需要记录的路由。

## 版本历史

### 1.0.0 (2024-01-01)

- 初始版本发布
- 实现用户、角色、权限、菜单、操作日志管理
- JWT 认证
- Swagger API 文档
- 单元测试

## 许可证

MIT License

## 作者

Cheney

## 支持

如有问题或建议，请提交 Issue 或 Pull Request。
