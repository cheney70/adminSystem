# 更新日志

本文档记录了 cheney/admin-system 包的所有重要更改。

## [1.0.0] - 2024-01-01

### 新增
- 初始版本发布
- 用户管理功能（增删改查、角色分配、密码重置、状态管理）
- 角色管理功能（增删改查、权限分配、用户分配）
- 权限管理功能（增删改查、权限分组）
- 菜单管理功能（增删改查、树形结构展示、权限关联）
- 操作日志功能（记录用户操作、日志查询、统计、导出）
- JWT 认证机制
- RBAC 权限控制系统
- Swagger API 文档自动生成
- 完整的单元测试覆盖
- Service 层架构设计
- CORS 跨域支持
- Facade 门面类支持
- 数据库迁移和填充
- 统一 API 响应格式

### 技术栈
- Laravel 8+
- PHP 7.4+
- MySQL 5.7+
- Redis
- JWT Auth
- Swagger

### 数据库表
- admins - 管理员表
- roles - 角色表
- permissions - 权限表
- menus - 菜单表
- role_admin - 角色管理员关联表
- permission_role - 权限角色关联表
- operation_logs - 操作日志表

### API 端点
- `/api/system/auth/*` - 认证相关接口
- `/api/system/admins` - 用户管理接口
- `/api/system/roles` - 角色管理接口
- `/api/system/permissions` - 权限管理接口
- `/api/system/menus` - 菜单管理接口
- `/api/system/operation-logs` - 操作日志接口

### 配置
- JWT 配置
- 路由前缀配置
- 中间件配置
- 分页配置
- 操作日志配置
- 上传配置
- 默认管理员配置

### 中间件
- JWT 认证中间件
- 权限检查中间件
- 操作日志中间件
- CORS 中间件

### Facade
- AdminAuth - 认证服务门面
- AdminUser - 用户管理门面
- AdminRole - 角色管理门面
- AdminPermission - 权限管理门面
- AdminMenu - 菜单管理门面
- AdminOperationLog - 操作日志门面

### 单元测试
- AuthTest - 认证功能测试
- AdminTest - 用户管理测试
- RoleTest - 角色管理测试
- PermissionTest - 权限管理测试
- MenuTest - 菜单管理测试
- OperationLogTest - 操作日志测试

### 默认数据
- 默认管理员账号：admin / admin123
- 默认角色：超级管理员、管理员、编辑
- 默认权限：用户管理、角色管理、权限管理、菜单管理
- 默认菜单：系统管理、用户管理、角色管理、权限管理、菜单管理、操作日志

---

## 版本说明

版本号遵循 [语义化版本](https://semver.org/lang/zh-CN/) 规范。

- **主版本号**：不兼容的 API 修改
- **次版本号**：向下兼容的功能性新增
- **修订号**：向下兼容的问题修正
