<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class TenantRegistrationTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_self_service_registration_creates_tenant_admin_and_free_subscription(): void
    {
        $response = $this->post('/demarrer', [
            'company_name' => 'Acme SAS',
            'admin_name' => 'Jean Dupont',
            'admin_email' => 'jean@acme.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $tenant = Tenant::where('name', 'Acme SAS')->first();
        $this->assertNotNull($tenant, 'Le tenant aurait dû être créé.');

        $admin = User::where('email', 'jean@acme.test')->first();
        $this->assertNotNull($admin);
        $this->assertSame($tenant->id, $admin->tenant_id);
        $this->assertTrue($admin->hasRole('admin'));

        $this->assertSame('free', $tenant->currentPlan()?->slug);
        $this->assertTrue($tenant->activeSubscription()?->isActive());

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');
    }

    public function test_registration_fails_with_duplicate_admin_email(): void
    {
        $tenant = $this->createTenantOnPlan();
        User::factory()->create(['email' => 'taken@acme.test', 'tenant_id' => $tenant->id]);

        $response = $this->post('/demarrer', [
            'company_name' => 'Beta SAS',
            'admin_name' => 'Marie',
            'admin_email' => 'taken@acme.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('admin_email');
        $this->assertNull(Tenant::where('name', 'Beta SAS')->first());
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post('/demarrer', [
            'company_name' => 'Gamma SAS',
            'admin_name' => 'Paul',
            'admin_email' => 'paul@gamma.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Mismatch!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertNull(Tenant::where('name', 'Gamma SAS')->first());
    }
}
