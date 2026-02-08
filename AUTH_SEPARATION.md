# 认证系统分离说明

## 设计概述

本扩展包实现了后台管理员系统与前端用户系统的完全分离，两者使用独立的认证机制，互不干扰。

## 认证架构

### 1. 双 Guard 设计

系统配置了两个独立的认证 Guard：

```php
'guards' => [
    // 前端用户认证 Guard
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],

    // 后台管理员认证 Guard
    'admin' => [
        'driver' => 'jwt',
        'provider' => 'admins',
    ],
],
```

### 2. 双 Provider 设计

```php
'providers' => [
    // 前端用户 Provider
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    // 后台管理员 Provider
    'admins' => [
        'driver' => 'eloquent',
        'model' => Cheney\AdminSystem\Models\Admin::class,
    ],
],
```

## Admin 模型说明

### 为什么继承 Authenticatable？

`Admin` 模型继承 `Illuminate\Foundation\Auth\User as Authenticatable` 是 Laravel 的标准做法，原因如下：

1. **Authenticatable 是接口实现类**：它实现了 Laravel 认证系统所需的核心接口
2. **不与前端用户冲突**：通过不同的 guard 和 provider 完全隔离
3. **JWT 认证支持**：实现了 `JWTSubject` 接口，支持 JWT 令牌生成和验证
4. **标准 Laravel 模式**：遵循 Laravel 的最佳实践，便于维护和扩展

### Admin 模型特性

- **独立数据表**：使用 `admins` 表，与 `users` 表完全分离
- **JWT 认证**：实现 `JWTSubject` 接口，支持 JWT 令牌
- **RBAC 权限**：通过角色关联实现基于角色的权限控制
- **操作日志**：记录管理员的所有操作历史

## 使用方式

### 后台管理员认证

```php
// 使用 admin guard 进行认证
auth('admin')->attempt($credentials);
auth('admin')->user();
auth('admin')->logout();
```

### 前端用户认证

```php
// 使用 api guard 进行认证
auth('api')->attempt($credentials);
auth('api')->user();
auth('api')->logout();
```

### JWT 中间件配置

```php
// 后台管理员路由
Route::middleware(['auth:admin'])->group(function () {
    // 后台管理路由
});

// 前端用户路由
Route::middleware(['auth:api'])->group(function () {
    // 前端用户路由
});
```

## 环境变量配置

在 `.env` 文件中配置：

```env
# 认证配置
AUTH_GUARD=web
AUTH_USER_MODEL=App\Models\User
AUTH_ADMIN_MODEL=Cheney\AdminSystem\Models\Admin

# JWT 配置
JWT_SECRET=your-jwt-secret-key
JWT_TTL=1440
JWT_REFRESH_TTL=20160

# Admin 系统配置
ADMIN_PREFIX=system
ADMIN_JWT_GUARD=admin
ADMIN_JWT_TTL=60
ADMIN_JWT_REFRESH_TTL=20160
```

## 数据库分离

### Admins 表结构

```sql
CREATE TABLE `admins` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL COMMENT '登录用户名',
    `password` varchar(255) NOT NULL COMMENT '加密密码',
    `name` varchar(50) NOT NULL COMMENT '真实姓名',
    `email` varchar(100) DEFAULT NULL COMMENT '邮箱地址',
    `phone` varchar(20) DEFAULT NULL COMMENT '手机号码',
    `avatar` varchar(255) DEFAULT NULL COMMENT '头像路径',
    `status` tinyint NOT NULL DEFAULT '1' COMMENT '状态：1启用，0禁用',
    `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
    `last_login_ip` varchar(45) DEFAULT NULL COMMENT '最后登录IP',
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `admins_username_unique` (`username`),
    KEY `admins_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
```

### Users 表结构

```sql
CREATE TABLE `users` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 安全性说明

1. **完全隔离**：Admin 和 User 使用不同的数据表、guard 和 provider
2. **独立 JWT**：可以使用不同的 JWT secret 或配置
3. **权限分离**：Admin 系统有完整的 RBAC 权限控制，前端用户系统可独立实现
4. **操作日志**：Admin 系统记录所有操作，前端用户系统可独立记录

## 常见问题

### Q: Admin 模型继承 Authenticatable 会影响前端用户吗？

A: 不会。通过不同的 guard 和 provider 完全隔离，两者互不影响。

### Q: 可以同时登录前端用户和后台管理员吗？

A: 可以。两者使用不同的 JWT token，可以同时存在。

### Q: 如何在同一个项目中同时使用两个系统？

A: 只需确保路由使用正确的中间件：
- 后台路由使用 `auth:admin` 中间件
- 前端路由使用 `auth:api` 中间件

### Q: JWT token 会冲突吗？

A: 不会。两个系统可以使用相同的 JWT secret，也可以配置不同的 secret。

## 总结

本扩展包的 Admin 系统与前端用户系统完全分离，通过 Laravel 的多 guard 和多 provider 机制实现，遵循 Laravel 最佳实践，确保系统的安全性和可维护性。
