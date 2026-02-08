# 单元测试说明

## 测试环境要求

cheney/admin-system 是一个 Laravel Composer 扩展包，单元测试需要在完整的 Laravel 项目环境中运行。

## 测试文件位置

测试文件位于 `src/tests/Feature/` 目录下：

- `AuthTest.php` - 认证功能测试
- `AdminTest.php` - 用户管理测试
- `RoleTest.php` - 角色管理测试
- `PermissionTest.php` - 权限管理测试
- `MenuTest.php` - 菜单管理测试
- `OperationLogTest.php` - 操作日志测试

## 在 Laravel 项目中运行测试

### 1. 安装扩展包

在 Laravel 项目中安装本扩展包：

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

### 5. 配置环境变量

在 `.env` 文件中添加：

```env
JWT_SECRET=your-jwt-secret-key
```

生成 JWT Secret：

```bash
php artisan jwt:secret
```

### 6. 运行测试

在 Laravel 项目根目录下运行：

```bash
php artisan test --filter="Cheney\\AdminSystem\\Tests"
```

或者运行特定测试：

```bash
# 运行认证测试
php artisan test --filter="AuthTest"

# 运行用户管理测试
php artisan test --filter="AdminTest"

# 运行角色管理测试
php artisan test --filter="RoleTest"

# 运行权限管理测试
php artisan test --filter="PermissionTest"

# 运行菜单管理测试
php artisan test --filter="MenuTest"

# 运行操作日志测试
php artisan test --filter="OperationLogTest"
```

## 测试覆盖范围

### AuthTest（认证测试）

- ✅ 使用有效凭据登录
- ✅ 使用无效凭据无法登录
- ✅ 禁用账号无法登录
- ✅ 已认证用户可以退出登录
- ✅ 已认证用户可以获取个人信息

### AdminTest（用户管理测试）

- ✅ 已认证用户可以获取用户列表
- ✅ 已认证用户可以创建用户
- ✅ 已认证用户可以更新用户
- ✅ 已认证用户可以删除用户
- ✅ 已认证用户可以为用户分配角色
- ✅ 已认证用户可以重置用户密码

### RoleTest（角色管理测试）

- ✅ 已认证用户可以获取角色列表
- ✅ 已认证用户可以创建角色
- ✅ 已认证用户可以更新角色
- ✅ 已认证用户可以删除角色
- ✅ 已认证用户可以为角色分配权限

### PermissionTest（权限管理测试）

- ✅ 已认证用户可以获取权限列表
- ✅ 已认证用户可以创建权限
- ✅ 已认证用户可以更新权限
- ✅ 已认证用户可以删除权限

### MenuTest（菜单管理测试）

- ✅ 已认证用户可以获取菜单列表
- ✅ 已认证用户可以创建菜单
- ✅ 已认证用户可以更新菜单
- ✅ 已认证用户可以删除菜单
- ✅ 已认证用户可以获取菜单树
- ✅ 已认证用户可以获取用户菜单

### OperationLogTest（操作日志测试）

- ✅ 已认证用户可以获取日志列表
- ✅ 已认证用户可以删除日志
- ✅ 已认证用户可以获取统计信息

## 测试数据库配置

测试使用内存 SQLite 数据库，配置在 `phpunit.xml` 中：

```xml
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

## 测试数据工厂

测试使用 Laravel 的数据工厂来生成测试数据：

- `AdminFactory` - 生成测试用户数据
- `RoleFactory` - 生成测试角色数据
- `PermissionFactory` - 生成测试权限数据
- `MenuFactory` - 生成测试菜单数据
- `OperationLogFactory` - 生成测试操作日志数据

## 注意事项

1. **环境要求**：测试需要在 Laravel 项目环境中运行，不能在独立的 Composer 包环境中运行
2. **数据库**：测试使用内存 SQLite 数据库，每次测试都会重置
3. **认证**：测试需要有效的 JWT Token，测试会自动创建测试用户并生成 Token
4. **权限**：测试假设用户具有所有权限，实际使用时需要根据角色权限配置

## 持续集成

可以在 CI/CD 流程中运行测试：

```yaml
# GitHub Actions 示例
- name: Run Tests
  run: |
    composer install
    php artisan migrate
    php artisan test --filter="Cheney\\AdminSystem\\Tests"
```

## 测试覆盖率

要生成测试覆盖率报告：

```bash
php artisan test --coverage --filter="Cheney\\AdminSystem\\Tests"
```

覆盖率报告将生成在 `storage/framework/testing/coverage` 目录下。

## 故障排除

### 问题：找不到测试类

**解决方案**：确保已正确配置 `composer.json` 中的 `autoload-dev` 部分，并运行 `composer dump-autoload`

### 问题：数据库连接错误

**解决方案**：检查 `phpunit.xml` 中的数据库配置，确保使用内存 SQLite 数据库

### 问题：JWT 认证失败

**解决方案**：确保已生成 JWT Secret，并在 `.env` 文件中配置

### 问题：权限验证失败

**解决方案**：测试数据中已包含完整的角色和权限配置，确保已运行数据填充

## 贡献测试

欢迎贡献新的测试用例。请遵循以下规范：

1. 测试方法名以 `test_` 开头
2. 使用描述性的测试方法名
3. 每个测试方法只测试一个功能点
4. 使用断言验证结果
5. 使用数据工厂生成测试数据

示例：

```php
public function test_authenticated_admin_can_create_user()
{
    $admin = Admin::factory()->create(['status' => 1]);
    $token = auth('api')->login($admin);
    
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('/api/system/admins', [
        'username' => 'testuser',
        'password' => 'password123',
        'name' => '测试用户',
        'status' => 1,
    ]);
    
    $response->assertStatus(200);
    $response->assertJson([
        'code' => 10000,
        'message' => '创建成功',
    ]);
    
    $this->assertDatabaseHas('admins', [
        'username' => 'testuser',
        'name' => '测试用户',
    ]);
}
```

## 联系方式

如有测试相关问题，请提交 Issue 或 Pull Request。
