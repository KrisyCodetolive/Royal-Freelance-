<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    private function submitIdentity(array $overrides = []): \Illuminate\Testing\TestResponse
    {
        return $this->post('/demarrer', array_merge([
            'company_name' => 'Acme SAS',
            'admin_name' => 'Jean Dupont',
            'admin_email' => 'jean@acme.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ], $overrides));
    }

    public function test_step_one_redirects_to_plan_selection_without_creating_the_tenant_yet(): void
    {
        $response = $this->submitIdentity();

        $response->assertRedirect(route('tenant.register.plan'));
        $this->assertNull(Tenant::where('name', 'Acme SAS')->first());
        $this->assertNull(User::where('email', 'jean@acme.test')->first());
    }

    public function test_plan_selection_page_redirects_back_to_step_one_without_identity_in_session(): void
    {
        $response = $this->get(route('tenant.register.plan'));

        $response->assertRedirect(route('tenant.register'));
    }

    public function test_full_registration_on_free_plan_creates_tenant_admin_as_owner(): void
    {
        $this->submitIdentity();

        $response = $this->post(route('tenant.register.plan.store'), [
            'plan' => 'free',
            'cycle' => 'monthly',
        ]);

        $tenant = Tenant::where('name', 'Acme SAS')->first();
        $this->assertNotNull($tenant, 'Le tenant aurait dû être créé.');

        $admin = User::where('email', 'jean@acme.test')->first();
        $this->assertNotNull($admin);
        $this->assertSame($tenant->id, $admin->tenant_id);
        $this->assertTrue($admin->hasRole('owner'));
        $this->assertTrue($admin->isAdmin(), 'Un Owner doit aussi avoir accès au panel admin.');

        $this->assertSame('free', $tenant->currentPlan()?->slug);
        $this->assertTrue($tenant->activeSubscription()?->isActive());
        $this->assertNull($tenant->activeSubscription()?->ends_at, 'Le plan Gratuit ne doit jamais expirer.');

        $this->assertTrue(Hash::check('Password123!', $admin->password), 'Le mot de passe transitant par la session doit rester utilisable pour se connecter.');

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');
    }

    public function test_registration_on_paid_plan_activates_it_immediately_with_mock_payment(): void
    {
        $this->submitIdentity(['admin_email' => 'paid@acme.test']);

        $this->post(route('tenant.register.plan.store'), [
            'plan' => 'starter',
            'cycle' => 'yearly',
        ]);

        $tenant = Tenant::where('name', 'Acme SAS')->first();
        $subscription = $tenant->activeSubscription();

        $this->assertSame('starter', $tenant->currentPlan()?->slug);
        $this->assertSame('yearly', $subscription->cycle);
        $this->assertNotNull($subscription->ends_at, 'Un plan payant a une date de fin de cycle même mockée.');
        $this->assertTrue($subscription->ends_at->isFuture());
    }

    public function test_registration_fails_with_duplicate_admin_email(): void
    {
        $tenant = $this->createTenantOnPlan();
        User::factory()->create(['email' => 'taken@acme.test', 'tenant_id' => $tenant->id]);

        $response = $this->submitIdentity(['admin_email' => 'taken@acme.test']);

        $response->assertSessionHasErrors('admin_email');
        $this->assertNull(Tenant::where('name', 'Acme SAS')->first());
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->submitIdentity(['password_confirmation' => 'Mismatch!']);

        $response->assertSessionHasErrors('password');
        $this->assertNull(Tenant::where('name', 'Acme SAS')->first());
    }

    public function test_plan_store_rejects_unknown_plan_slug(): void
    {
        $this->submitIdentity();

        $response = $this->post(route('tenant.register.plan.store'), [
            'plan' => 'does-not-exist',
            'cycle' => 'monthly',
        ]);

        $response->assertSessionHasErrors('plan');
        $this->assertNull(Tenant::where('name', 'Acme SAS')->first());
    }
}
