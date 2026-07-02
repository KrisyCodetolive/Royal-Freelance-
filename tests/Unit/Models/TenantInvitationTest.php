<?php

namespace Tests\Unit\Models;

use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SetsUpSaasTestData;
use Tests\TestCase;

class TenantInvitationTest extends TestCase
{
    use RefreshDatabase, SetsUpSaasTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRolesAndPlans();
    }

    public function test_invitation_auto_assigns_tenant_id_from_authenticated_admin(): void
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        $this->actingAs($admin);

        $invitation = TenantInvitation::create([
            'email' => 'commercial@example.com',
            'invited_by' => $admin->id,
            'role' => 'commercial',
            'expires_at' => now()->addDays(7),
        ]);

        $this->assertSame($tenant->id, $invitation->tenant_id);
    }

    public function test_invitation_is_valid_when_fresh(): void
    {
        $invitation = $this->makeInvitation();

        $this->assertTrue($invitation->isValid());
        $this->assertFalse($invitation->isExpired());
        $this->assertFalse($invitation->isUsed());
    }

    public function test_invitation_is_invalid_once_expired(): void
    {
        $invitation = $this->makeInvitation(['expires_at' => now()->subDay()]);

        $this->assertTrue($invitation->isExpired());
        $this->assertFalse($invitation->isValid());
    }

    public function test_invitation_is_invalid_once_used(): void
    {
        $tenant = $this->createTenantOnPlan();
        $commercial = $this->createCommercialForTenant($tenant);
        $invitation = $this->makeInvitation();

        $invitation->markUsedBy($commercial);

        $this->assertTrue($invitation->fresh()->isUsed());
        $this->assertFalse($invitation->fresh()->isValid());
        $this->assertSame($commercial->id, $invitation->fresh()->used_by);
    }

    public function test_invite_url_contains_the_token(): void
    {
        $invitation = $this->makeInvitation();

        $this->assertStringContainsString($invitation->token, $invitation->invite_url);
    }

    public function test_invitation_from_one_tenant_is_invisible_to_another_tenant_admin(): void
    {
        $tenantA = $this->createTenantOnPlan();
        $adminA = $this->createAdminForTenant($tenantA);
        $tenantB = $this->createTenantOnPlan();
        $adminB = $this->createAdminForTenant($tenantB);

        $this->actingAs($adminA);
        $invitation = TenantInvitation::create([
            'email' => 'leak-test@example.com',
            'invited_by' => $adminA->id,
            'role' => 'commercial',
            'expires_at' => now()->addDays(7),
        ]);

        $this->actingAs($adminB);
        $this->assertFalse(
            TenantInvitation::where('id', $invitation->id)->exists(),
            'Une invitation du tenant A ne doit jamais être visible depuis le tenant B.'
        );
    }

    private function makeInvitation(array $attributes = []): TenantInvitation
    {
        $tenant = $this->createTenantOnPlan();
        $admin = $this->createAdminForTenant($tenant);

        return TenantInvitation::create(array_merge([
            'tenant_id' => $tenant->id,
            'email' => 'invite@example.com',
            'invited_by' => $admin->id,
            'role' => 'commercial',
            'expires_at' => now()->addDays(7),
        ], $attributes));
    }
}
