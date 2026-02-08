# API 文档

本文档详细描述了 cheney/admin-system 包的所有 API 接口。

## 基础信息

- **基础 URL**: `/api/system`
- **认证方式**: JWT Bearer Token
- **数据格式**: JSON
- **字符编码**: UTF-8

## 统一响应格式

所有接口统一返回以下格式：

```json
{
    "code": 10000,
    "message": "操作成功",
    "data": {}
}
```

### 状态码说明

| Code | 说明 |
|------|------|
| 10000 | 操作成功 |
| 20000 | 操作失败 |
| 20001 | 参数错误 |
| 20002 | 未授权 |
| 20003 | 禁止访问 |
| 20004 | 资源不存在 |
| 20005 | 服务器错误 |

### HTTP 状态码说明

| Status | 说明 |
|--------|------|
| 200 | 请求成功 |
| 201 | 创建成功 |
| 400 | 请求参数错误 |
| 401 | 未授权 |
| 403 | 禁止访问 |
| 404 | 资源不存在 |
| 422 | 验证失败 |
| 500 | 服务器错误 |

## 认证接口

### 1. 登录

用户登录获取访问令牌。

**接口地址**: `POST /auth/login`

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| username | string | 是 | 用户名 |
| password | string | 是 | 密码 |

**请求示例**:

```json
{
    "username": "admin",
    "password": "admin123"
}
```

**响应示例**:

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
            "phone": null,
            "avatar": null,
            "status": 1
        }
    }
}
```

### 2. 获取当前用户信息

获取当前登录用户的详细信息。

**接口地址**: `GET /auth/me`

**请求头**:

```
Authorization: Bearer {access_token}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "id": 1,
        "username": "admin",
        "name": "超级管理员",
        "email": "admin@example.com",
        "phone": null,
        "avatar": null,
        "status": 1,
        "roles": [
            {
                "id": 1,
                "name": "超级管理员",
                "code": "super_admin"
            }
        ],
        "permissions": [
            "user.manage",
            "role.manage",
            "permission.manage",
            "menu.manage"
        ]
    }
}
```

### 3. 退出登录

退出登录，使当前 Token 失效。

**接口地址**: `POST /auth/logout`

**请求头**:

```
Authorization: Bearer {access_token}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "退出成功",
    "data": null
}
```

### 4. 刷新 Token

刷新访问令牌。

**接口地址**: `POST /auth/refresh`

**请求头**:

```
Authorization: Bearer {access_token}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "刷新成功",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 86400
    }
}
```

### 5. 更新个人信息

更新当前用户的个人信息。

**接口地址**: `PUT /auth/profile`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 否 | 姓名 |
| email | string | 否 | 邮箱 |
| phone | string | 否 | 手机号 |
| avatar | string | 否 | 头像 URL |

**请求示例**:

```json
{
    "name": "更新后的姓名",
    "email": "newemail@example.com",
    "phone": "13800138000"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "更新成功",
    "data": {
        "id": 1,
        "username": "admin",
        "name": "更新后的姓名",
        "email": "newemail@example.com",
        "phone": "13800138000"
    }
}
```

### 6. 修改密码

修改当前用户的密码。

**接口地址**: `POST /auth/change-password`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| old_password | string | 是 | 旧密码 |
| new_password | string | 是 | 新密码 |

**请求示例**:

```json
{
    "old_password": "oldpassword123",
    "new_password": "newpassword123"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "密码修改成功",
    "data": null
}
```

## 用户管理接口

### 1. 获取用户列表

获取系统用户列表，支持分页和搜索。

**接口地址**: `GET /admins`

**请求头**:

```
Authorization: Bearer {access_token}
```

**查询参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| page | integer | 否 | 页码，默认 1 |
| per_page | integer | 否 | 每页数量，默认 15 |
| keyword | string | 否 | 搜索关键词（用户名、姓名、邮箱、手机号） |
| status | integer | 否 | 状态：1-启用，0-禁用 |
| role_id | integer | 否 | 角色ID筛选 |
| sort_by | string | 否 | 排序字段，默认 id |
| sort_order | string | 否 | 排序方向：asc/desc，默认 desc |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "username": "admin",
                "name": "超级管理员",
                "email": "admin@example.com",
                "phone": null,
                "avatar": null,
                "status": 1,
                "last_login_at": "2024-01-01 10:00:00",
                "last_login_ip": "127.0.0.1",
                "created_at": "2024-01-01 00:00:00",
                "roles": [
                    {
                        "id": 1,
                        "name": "超级管理员",
                        "code": "super_admin"
                    }
                ]
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### 2. 获取用户详情

获取指定用户的详细信息。

**接口地址**: `GET /admins/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "id": 1,
        "username": "admin",
        "name": "超级管理员",
        "email": "admin@example.com",
        "phone": null,
        "avatar": null,
        "status": 1,
        "last_login_at": "2024-01-01 10:00:00",
        "last_login_ip": "127.0.0.1",
        "created_at": "2024-01-01 00:00:00",
        "roles": [
            {
                "id": 1,
                "name": "超级管理员",
                "code": "super_admin"
            }
        ],
        "permissions": [
            "user.manage",
            "role.manage",
            "permission.manage",
            "menu.manage"
        ]
    }
}
```

### 3. 创建用户

创建新用户。

**接口地址**: `POST /admins`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| username | string | 是 | 用户名，50字符以内，唯一 |
| password | string | 是 | 密码，最少6位 |
| name | string | 是 | 姓名，50字符以内 |
| email | string | 否 | 邮箱，100字符以内，唯一 |
| phone | string | 否 | 手机号，20字符以内 |
| avatar | string | 否 | 头像 URL |
| status | integer | 否 | 状态：1-启用，0-禁用，默认 1 |

**请求示例**:

```json
{
    "username": "testuser",
    "password": "password123",
    "name": "测试用户",
    "email": "test@example.com",
    "phone": "13800138000",
    "status": 1
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "创建成功",
    "data": {
        "id": 2,
        "username": "testuser",
        "name": "测试用户",
        "email": "test@example.com",
        "phone": "13800138000",
        "status": 1,
        "created_at": "2024-01-01 12:00:00"
    }
}
```

### 4. 更新用户

更新指定用户的信息。

**接口地址**: `PUT /admins/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 否 | 姓名 |
| email | string | 否 | 邮箱 |
| phone | string | 否 | 手机号 |
| avatar | string | 否 | 头像 URL |
| status | integer | 否 | 状态：1-启用，0-禁用 |

**请求示例**:

```json
{
    "name": "更新后的姓名",
    "email": "newemail@example.com",
    "phone": "13900139000"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "更新成功",
    "data": {
        "id": 2,
        "username": "testuser",
        "name": "更新后的姓名",
        "email": "newemail@example.com",
        "phone": "13900139000",
        "updated_at": "2024-01-01 13:00:00"
    }
}
```

### 5. 删除用户

删除指定用户（软删除）。

**接口地址**: `DELETE /admins/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "删除成功",
    "data": null
}
```

### 6. 分配角色

为用户分配角色。

**接口地址**: `POST /admins/{id}/roles`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| role_ids | array | 是 | 角色ID数组 |

**请求示例**:

```json
{
    "role_ids": [1, 2, 3]
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "角色分配成功",
    "data": {
        "admin_id": 2,
        "roles": [
            {
                "id": 1,
                "name": "超级管理员",
                "code": "super_admin"
            },
            {
                "id": 2,
                "name": "管理员",
                "code": "admin"
            }
        ]
    }
}
```

### 7. 重置密码

重置指定用户的密码。

**接口地址**: `POST /admins/{id}/reset-password`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| password | string | 是 | 新密码，最少6位 |

**请求示例**:

```json
{
    "password": "newpassword123"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "密码重置成功",
    "data": null
}
```

### 8. 修改状态

修改用户状态（启用/禁用）。

**接口地址**: `POST /admins/{id}/change-status`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 用户ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| status | integer | 是 | 状态：1-启用，0-禁用 |

**请求示例**:

```json
{
    "status": 0
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "状态修改成功",
    "data": {
        "id": 2,
        "status": 0
    }
}
```

## 角色管理接口

### 1. 获取角色列表

获取系统角色列表，支持分页和搜索。

**接口地址**: `GET /roles`

**请求头**:

```
Authorization: Bearer {access_token}
```

**查询参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| page | integer | 否 | 页码，默认 1 |
| per_page | integer | 否 | 每页数量，默认 15 |
| keyword | string | 否 | 搜索关键词（角色名称、编码） |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "超级管理员",
                "code": "super_admin",
                "description": "拥有所有权限",
                "created_at": "2024-01-01 00:00:00",
                "permissions_count": 10,
                "admins_count": 1
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### 2. 获取角色详情

获取指定角色的详细信息。

**接口地址**: `GET /roles/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 角色ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "id": 1,
        "name": "超级管理员",
        "code": "super_admin",
        "description": "拥有所有权限",
        "created_at": "2024-01-01 00:00:00",
        "permissions": [
            {
                "id": 1,
                "name": "用户管理",
                "code": "user.manage"
            }
        ],
        "admins": [
            {
                "id": 1,
                "username": "admin",
                "name": "超级管理员"
            }
        ]
    }
}
```

### 3. 创建角色

创建新角色。

**接口地址**: `POST /roles`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 是 | 角色名称，50字符以内 |
| code | string | 是 | 角色编码，50字符以内，唯一 |
| description | string | 否 | 角色描述 |

**请求示例**:

```json
{
    "name": "编辑",
    "code": "editor",
    "description": "内容编辑角色"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "创建成功",
    "data": {
        "id": 2,
        "name": "编辑",
        "code": "editor",
        "description": "内容编辑角色",
        "created_at": "2024-01-01 12:00:00"
    }
}
```

### 4. 更新角色

更新指定角色的信息。

**接口地址**: `PUT /roles/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 角色ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 否 | 角色名称 |
| code | string | 否 | 角色编码 |
| description | string | 否 | 角色描述 |

**请求示例**:

```json
{
    "name": "高级编辑",
    "description": "高级内容编辑角色"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "更新成功",
    "data": {
        "id": 2,
        "name": "高级编辑",
        "code": "editor",
        "description": "高级内容编辑角色",
        "updated_at": "2024-01-01 13:00:00"
    }
}
```

### 5. 删除角色

删除指定角色。

**接口地址**: `DELETE /roles/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 角色ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "删除成功",
    "data": null
}
```

### 6. 分配权限

为角色分配权限。

**接口地址**: `POST /roles/{id}/permissions`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 角色ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| permission_ids | array | 是 | 权限ID数组 |

**请求示例**:

```json
{
    "permission_ids": [1, 2, 3, 4]
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "权限分配成功",
    "data": {
        "role_id": 2,
        "permissions": [
            {
                "id": 1,
                "name": "用户管理",
                "code": "user.manage"
            },
            {
                "id": 2,
                "name": "角色管理",
                "code": "role.manage"
            }
        ]
    }
}
```

### 7. 分配用户

为角色分配用户。

**接口地址**: `POST /roles/{id}/admins`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 角色ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| admin_ids | array | 是 | 用户ID数组 |

**请求示例**:

```json
{
    "admin_ids": [1, 2, 3]
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "用户分配成功",
    "data": {
        "role_id": 2,
        "admins": [
            {
                "id": 1,
                "username": "admin",
                "name": "超级管理员"
            },
            {
                "id": 2,
                "username": "testuser",
                "name": "测试用户"
            }
        ]
    }
}
```

## 权限管理接口

### 1. 获取权限列表

获取系统权限列表，支持分页和搜索。

**接口地址**: `GET /permissions`

**请求头**:

```
Authorization: Bearer {access_token}
```

**查询参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| page | integer | 否 | 页码，默认 1 |
| per_page | integer | 否 | 每页数量，默认 15 |
| keyword | string | 否 | 搜索关键词（权限名称、编码） |
| type | string | 否 | 权限类型：menu/button/api |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "用户管理",
                "code": "user.manage",
                "type": "menu",
                "description": "用户管理权限",
                "created_at": "2024-01-01 00:00:00"
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### 2. 获取权限详情

获取指定权限的详细信息。

**接口地址**: `GET /permissions/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 权限ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "id": 1,
        "name": "用户管理",
        "code": "user.manage",
        "type": "menu",
        "description": "用户管理权限",
        "created_at": "2024-01-01 00:00:00",
        "roles": [
            {
                "id": 1,
                "name": "超级管理员",
                "code": "super_admin"
            }
        ]
    }
}
```

### 3. 创建权限

创建新权限。

**接口地址**: `POST /permissions`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 是 | 权限名称，50字符以内 |
| code | string | 是 | 权限编码，50字符以内，唯一 |
| type | string | 否 | 权限类型：menu/button/api，默认 menu |
| description | string | 否 | 权限描述 |

**请求示例**:

```json
{
    "name": "用户管理",
    "code": "user.manage",
    "type": "menu",
    "description": "用户管理权限"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "创建成功",
    "data": {
        "id": 1,
        "name": "用户管理",
        "code": "user.manage",
        "type": "menu",
        "description": "用户管理权限",
        "created_at": "2024-01-01 12:00:00"
    }
}
```

### 4. 更新权限

更新指定权限的信息。

**接口地址**: `PUT /permissions/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 权限ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| name | string | 否 | 权限名称 |
| code | string | 否 | 权限编码 |
| type | string | 否 | 权限类型 |
| description | string | 否 | 权限描述 |

**请求示例**:

```json
{
    "name": "用户管理（更新）",
    "description": "用户管理权限（更新）"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "更新成功",
    "data": {
        "id": 1,
        "name": "用户管理（更新）",
        "code": "user.manage",
        "type": "menu",
        "description": "用户管理权限（更新）",
        "updated_at": "2024-01-01 13:00:00"
    }
}
```

### 5. 删除权限

删除指定权限。

**接口地址**: `DELETE /permissions/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 权限ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "删除成功",
    "data": null
}
```

## 菜单管理接口

### 1. 获取菜单列表

获取系统菜单列表，支持分页和搜索。

**接口地址**: `GET /menus`

**请求头**:

```
Authorization: Bearer {access_token}
```

**查询参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| page | integer | 否 | 页码，默认 1 |
| per_page | integer | 否 | 每页数量，默认 15 |
| keyword | string | 否 | 搜索关键词（菜单名称） |
| parent_id | integer | 否 | 父级菜单ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "parent_id": 0,
                "name": "系统管理",
                "path": "/system",
                "icon": "setting",
                "sort": 1,
                "status": 1,
                "created_at": "2024-01-01 00:00:00"
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### 2. 获取菜单树

获取菜单树形结构。

**接口地址**: `GET /menus/tree`

**请求头**:

```
Authorization: Bearer {access_token}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": [
        {
            "id": 1,
            "parent_id": 0,
            "name": "系统管理",
            "path": "/system",
            "icon": "setting",
            "sort": 1,
            "status": 1,
            "children": [
                {
                    "id": 2,
                    "parent_id": 1,
                    "name": "用户管理",
                    "path": "/system/user",
                    "icon": "user",
                    "sort": 1,
                    "status": 1,
                    "children": []
                }
            ]
        }
    ]
}
```

### 3. 获取当前用户菜单

获取当前登录用户的菜单列表。

**接口地址**: `GET /user-menus`

**请求头**:

```
Authorization: Bearer {access_token}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": [
        {
            "id": 1,
            "parent_id": 0,
            "name": "系统管理",
            "path": "/system",
            "icon": "setting",
            "sort": 1,
            "status": 1,
            "children": [
                {
                    "id": 2,
                    "parent_id": 1,
                    "name": "用户管理",
                    "path": "/system/user",
                    "icon": "user",
                    "sort": 1,
                    "status": 1,
                    "children": []
                }
            ]
        }
    ]
}
```

### 4. 获取菜单详情

获取指定菜单的详细信息。

**接口地址**: `GET /menus/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 菜单ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "id": 1,
        "parent_id": 0,
        "name": "系统管理",
        "path": "/system",
        "icon": "setting",
        "sort": 1,
        "status": 1,
        "permission_id": 1,
        "created_at": "2024-01-01 00:00:00",
        "parent": null,
        "children": [
            {
                "id": 2,
                "name": "用户管理",
                "path": "/system/user"
            }
        ]
    }
}
```

### 5. 创建菜单

创建新菜单。

**接口地址**: `POST /menus`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| parent_id | integer | 否 | 父级菜单ID，默认 0 |
| name | string | 是 | 菜单名称，50字符以内 |
| path | string | 否 | 菜单路径 |
| icon | string | 否 | 菜单图标 |
| sort | integer | 否 | 排序，默认 0 |
| status | integer | 否 | 状态：1-启用，0-禁用，默认 1 |
| permission_id | integer | 否 | 关联权限ID |

**请求示例**:

```json
{
    "parent_id": 0,
    "name": "用户管理",
    "path": "/system/user",
    "icon": "user",
    "sort": 1,
    "status": 1,
    "permission_id": 1
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "创建成功",
    "data": {
        "id": 2,
        "parent_id": 0,
        "name": "用户管理",
        "path": "/system/user",
        "icon": "user",
        "sort": 1,
        "status": 1,
        "permission_id": 1,
        "created_at": "2024-01-01 12:00:00"
    }
}
```

### 6. 更新菜单

更新指定菜单的信息。

**接口地址**: `PUT /menus/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 菜单ID |

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| parent_id | integer | 否 | 父级菜单ID |
| name | string | 否 | 菜单名称 |
| path | string | 否 | 菜单路径 |
| icon | string | 否 | 菜单图标 |
| sort | integer | 否 | 排序 |
| status | integer | 否 | 状态：1-启用，0-禁用 |
| permission_id | integer | 否 | 关联权限ID |

**请求示例**:

```json
{
    "name": "用户管理（更新）",
    "icon": "user-outlined"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "更新成功",
    "data": {
        "id": 2,
        "parent_id": 0,
        "name": "用户管理（更新）",
        "path": "/system/user",
        "icon": "user-outlined",
        "sort": 1,
        "status": 1,
        "updated_at": "2024-01-01 13:00:00"
    }
}
```

### 7. 删除菜单

删除指定菜单。

**接口地址**: `DELETE /menus/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 菜单ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "删除成功",
    "data": null
}
```

## 操作日志接口

### 1. 获取操作日志列表

获取系统操作日志列表，支持分页和搜索。

**接口地址**: `GET /operation-logs`

**请求头**:

```
Authorization: Bearer {access_token}
```

**查询参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| page | integer | 否 | 页码，默认 1 |
| per_page | integer | 否 | 每页数量，默认 15 |
| keyword | string | 否 | 搜索关键词（操作模块、操作描述） |
| admin_id | integer | 否 | 管理员ID筛选 |
| module | string | 否 | 模块名称筛选 |
| action | string | 否 | 操作类型筛选 |
| start_date | string | 否 | 开始日期，格式：Y-m-d |
| end_date | string | 否 | 结束日期，格式：Y-m-d |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "admin_id": 1,
                "module": "用户管理",
                "action": "创建用户",
                "description": "创建了用户 testuser",
                "ip": "127.0.0.1",
                "user_agent": "Mozilla/5.0...",
                "created_at": "2024-01-01 12:00:00",
                "admin": {
                    "id": 1,
                    "username": "admin",
                    "name": "超级管理员"
                }
            }
        ],
        "per_page": 15,
        "total": 1
    }
}
```

### 2. 获取操作日志详情

获取指定操作日志的详细信息。

**接口地址**: `GET /operation-logs/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 日志ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "id": 1,
        "admin_id": 1,
        "module": "用户管理",
        "action": "创建用户",
        "description": "创建了用户 testuser",
        "ip": "127.0.0.1",
        "user_agent": "Mozilla/5.0...",
        "request_data": "{\"username\":\"testuser\"}",
        "created_at": "2024-01-01 12:00:00",
        "admin": {
            "id": 1,
            "username": "admin",
            "name": "超级管理员"
        }
    }
}
```

### 3. 删除操作日志

删除指定操作日志。

**接口地址**: `DELETE /operation-logs/{id}`

**请求头**:

```
Authorization: Bearer {access_token}
```

**路径参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 日志ID |

**响应示例**:

```json
{
    "code": 10000,
    "message": "删除成功",
    "data": null
}
```

### 4. 清空操作日志

清空所有操作日志。

**接口地址**: `POST /operation-logs/clear`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| days | integer | 否 | 保留最近多少天的日志，默认 30 |

**请求示例**:

```json
{
    "days": 30
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "清空成功",
    "data": {
        "deleted_count": 100
    }
}
```

### 5. 获取日志统计

获取操作日志统计数据。

**接口地址**: `GET /operation-logs/statistics`

**请求头**:

```
Authorization: Bearer {access_token}
```

**查询参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| start_date | string | 否 | 开始日期，格式：Y-m-d |
| end_date | string | 否 | 结束日期，格式：Y-m-d |

**响应示例**:

```json
{
    "code": 10000,
    "message": "获取成功",
    "data": {
        "total": 1000,
        "by_module": [
            {
                "module": "用户管理",
                "count": 300
            },
            {
                "module": "角色管理",
                "count": 200
            }
        ],
        "by_action": [
            {
                "action": "创建",
                "count": 400
            },
            {
                "action": "更新",
                "count": 300
            }
        ],
        "by_admin": [
            {
                "admin_id": 1,
                "username": "admin",
                "count": 500
            }
        ],
        "by_date": [
            {
                "date": "2024-01-01",
                "count": 100
            }
        ]
    }
}
```

### 6. 导出操作日志

导出操作日志为 Excel 文件。

**接口地址**: `POST /operation-logs/export`

**请求头**:

```
Authorization: Bearer {access_token}
```

**请求参数**:

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| start_date | string | 否 | 开始日期，格式：Y-m-d |
| end_date | string | 否 | 结束日期，格式：Y-m-d |
| admin_id | integer | 否 | 管理员ID筛选 |
| module | string | 否 | 模块名称筛选 |

**请求示例**:

```json
{
    "start_date": "2024-01-01",
    "end_date": "2024-01-31",
    "module": "用户管理"
}
```

**响应示例**:

```json
{
    "code": 10000,
    "message": "导出成功",
    "data": {
        "download_url": "http://example.com/exports/operation_logs_20240101.xlsx"
    }
}
```

## 错误处理

所有接口在发生错误时会返回相应的错误信息：

### 参数验证错误

```json
{
    "code": 20001,
    "message": "参数验证失败",
    "data": {
        "username": ["用户名不能为空"],
        "password": ["密码最少6位"]
    }
}
```

### 未授权

```json
{
    "code": 20002,
    "message": "未授权，请先登录",
    "data": null
}
```

### 禁止访问

```json
{
    "code": 20003,
    "message": "没有权限访问该资源",
    "data": null
}
```

### 资源不存在

```json
{
    "code": 20004,
    "message": "资源不存在",
    "data": null
}
```

### 服务器错误

```json
{
    "code": 20005,
    "message": "服务器内部错误",
    "data": null
}
```

## 注意事项

1. 所有需要认证的接口都需要在请求头中携带 JWT Token
2. Token 格式：`Authorization: Bearer {access_token}`
3. Token 默认有效期为 24 小时，过期后需要重新登录或刷新 Token
4. 分页参数默认值：page=1，per_page=15
5. 日期格式统一使用 `Y-m-d H:i:s` 格式
6. 所有接口返回的日期时间均为 UTC+8 时区
7. 删除操作为软删除，数据不会真正从数据库中删除
8. 权限验证基于用户角色和角色权限的关联关系
