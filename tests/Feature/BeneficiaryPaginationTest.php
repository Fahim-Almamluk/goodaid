<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeneficiaryPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_per_page_is_100()
    {
        // create a user
        $user = User::factory()->create(['role' => 'admin']);

        // seed more than 150 beneficiaries
        for ($i = 1; $i <= 150; $i++) {
            Beneficiary::create([
                'name' => 'Beneficiary ' . $i,
                'phone' => '000000' . $i,
                'relationship' => 'زوج/ة',
                'national_id' => 'NID' . $i,
                'residence_status' => 'resident',
                'number_of_members' => 1,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($user)->get(route('beneficiaries.index'));

        $response->assertStatus(200);

        // Ensure English summary is not present and the small footer summary was removed
        $responseContent = $response->getContent();
        $this->assertStringNotContainsString('Showing', $responseContent);
        // The on-page gray summary should not be present (we expect the paginator only)
        $this->assertStringNotContainsString('عرض <strong>', $responseContent);
        // Pagination navigation markup should be present
        $this->assertStringContainsString('<nav role="navigation"', $responseContent);
        // Per-page selector should be removed as requested
        $this->assertStringNotContainsString('id="per_page_select"', $responseContent);
        // The centered Arabic summary above the paginator should be present
        $this->assertStringContainsString('عرض <strong>', $responseContent);
        // Ensure distributions link was removed from header/menu
        $this->assertStringNotContainsString('التوزيعات', $responseContent);

        $beneficiaries = $response->viewData('beneficiaries');

        $this->assertEquals(100, $beneficiaries->perPage());
        $this->assertEquals(150, $beneficiaries->total());
    }

    public function test_per_page_param_changes_page_size()
    {
        $user = User::factory()->create(['role' => 'admin']);
        for ($i = 1; $i <= 300; $i++) {
            Beneficiary::create([
                'name' => 'Beneficiary ' . $i,
                'phone' => '000000' . $i,
                'relationship' => 'زوج/ة',
                'national_id' => 'NID' . $i,
                'residence_status' => 'resident',
                'number_of_members' => 1,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($user)->get(route('beneficiaries.index', ['per_page' => 50]));

        $response->assertStatus(200);
        $beneficiaries = $response->viewData('beneficiaries');
        $this->assertEquals(50, $beneficiaries->perPage());
    }
}
