<?php

namespace Tests\Feature;

use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class CommercialInvitationRegistrationTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    private function makeInvitation(int $tenantId, int $adminId, array $overrides = []): TenantInvitation
    {
        return TenantInvitation::create(array_merge([
            'tenant_id' => $tenantId,
            'email' => 'new-commercial@example.com',
            'invited_by' => $adminId,
            'role' => 'commercial',
            'expires_at' => now()->addDays(7),
        ], $overrides));
    }

    public function test_registration_with_valid_invitation_attaches_commercial_to_correct_tenant(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);
        $invitation = $this->makeInvitation($tenant->id, $admin->id);

        $response = $this->post('/register', [
            'name' => 'Nouveau Commercial',
            'email' => $invitation->email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'shop_name' => 'Ma Boutique',
            'invitation' => $invitation->token,
        ]);

        $commercial = User::where('email', $invitation->email)->first();
        $this->assertNotNull($commercial);
        $this->assertSame($tenant->id, $commercial->tenant_id);
        $this->assertTrue($commercial->hasRole('commercial'));

        $this->assertTrue($invitation->fresh()->isUsed());
        $this->assertSame($commercial->id, $invitation->fresh()->used_by);

        $this->assertAuthenticatedAs($commercial);
        $response->assertRedirect(route('commercial.dashboard'));
    }

    public function test_registration_without_invitation_token_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Token',
            'email' => 'no-token@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'shop_name' => 'Boutique',
        ]);

        $response->assertSessionHasErrors('invitation');
        $this->assertNull(User::where('email', 'no-token@example.com')->first());
    }

    public function test_registration_with_unknown_token_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Bad Token',
            'email' => 'bad-token@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'shop_name' => 'Boutique',
            'invitation' => 'not-a-real-token',
        ]);

        $response->assertSessionHasErrors('invitation');
        $this->assertNull(User::where('email', 'bad-token@example.com')->first());
    }

    public function test_registration_with_expired_invitation_is_rejected(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);
        $invitation = $this->makeInvitation($tenant->id, $admin->id, [
            'email' => 'expired@example.com',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->post('/register', [
            'name' => 'Expired User',
            'email' => 'expired@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'shop_name' => 'Boutique',
            'invitation' => $invitation->token,
        ]);

        $response->assertSessionHasErrors('invitation');
        $this->assertNull(User::where('email', 'expired@example.com')->first());
    }

    public function test_registration_with_already_used_invitation_is_rejected(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);
        $firstUser = $this->createCommercialForTenant($tenant);
        $invitation = $this->makeInvitation($tenant->id, $admin->id, ['email' => 'reuse@example.com']);
        $invitation->markUsedBy($firstUser);

        $response = $this->post('/register', [
            'name' => 'Second User',
            'email' => 'reuse@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'shop_name' => 'Boutique',
            'invitation' => $invitation->token,
        ]);

        $response->assertSessionHasErrors('invitation');
    }
}
