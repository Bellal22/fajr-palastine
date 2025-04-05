<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_complaints()
    {
        $this->actingAsAdmin();

        Complaint::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.complaints.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_complaint_details()
    {
        $this->actingAsAdmin();

        $complaint = Complaint::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.complaints.show', $complaint))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_complaints_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.complaints.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_complaint()
    {
        $this->actingAsAdmin();

        $complaintsCount = Complaint::count();

        $response = $this->post(
            route('dashboard.complaints.store'),
            Complaint::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $complaint = Complaint::all()->last();

        $this->assertEquals(Complaint::count(), $complaintsCount + 1);

        $this->assertEquals($complaint->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_complaints_edit_form()
    {
        $this->actingAsAdmin();

        $complaint = Complaint::factory()->create();

        $this->get(route('dashboard.complaints.edit', $complaint))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_complaint()
    {
        $this->actingAsAdmin();

        $complaint = Complaint::factory()->create();

        $response = $this->put(
            route('dashboard.complaints.update', $complaint),
            Complaint::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $complaint->refresh();

        $response->assertRedirect();

        $this->assertEquals($complaint->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_complaint()
    {
        $this->actingAsAdmin();

        $complaint = Complaint::factory()->create();

        $complaintsCount = Complaint::count();

        $response = $this->delete(route('dashboard.complaints.destroy', $complaint));

        $response->assertRedirect();

        $this->assertEquals(Complaint::count(), $complaintsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_complaints()
    {
        if (! $this->useSoftDeletes($model = Complaint::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Complaint::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.complaints.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_complaint_details()
    {
        if (! $this->useSoftDeletes($model = Complaint::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $complaint = Complaint::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.complaints.trashed.show', $complaint));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_complaint()
    {
        if (! $this->useSoftDeletes($model = Complaint::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $complaint = Complaint::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.complaints.restore', $complaint));

        $response->assertRedirect();

        $this->assertNull($complaint->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_complaint()
    {
        if (! $this->useSoftDeletes($model = Complaint::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $complaint = Complaint::factory()->create(['deleted_at' => now()]);

        $complaintCount = Complaint::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.complaints.forceDelete', $complaint));

        $response->assertRedirect();

        $this->assertEquals(Complaint::withoutTrashed()->count(), $complaintCount - 1);
    }

    /** @test */
    public function it_can_filter_complaints_by_name()
    {
        $this->actingAsAdmin();

        Complaint::factory()->create([
            'name' => 'Foo',
        ]);

        Complaint::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.complaints.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('complaints.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
