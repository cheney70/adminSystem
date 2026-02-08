<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\OperationLog;

class OperationLogTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);
    }

    public function test_authenticated_admin_can_get_log_list()
    {
        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/operation-logs');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_delete_log()
    {
        $token = auth('api')->login($this->admin);
        
        $log = OperationLog::factory()->create([
            'admin_id' => $this->admin->id,
            'username' => $this->admin->username,
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/system/operation-logs/' . $log->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('operation_logs', [
            'id' => $log->id,
        ]);
    }

    public function test_authenticated_admin_can_get_statistics()
    {
        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/operation-logs/statistics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'total',
                'success',
                'failed',
                'module_stats',
                'action_stats',
            ],
        ]);
    }
}
