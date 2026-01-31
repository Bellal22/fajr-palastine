<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\NeedRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NeedRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_need_requests()
    {
        $this->actingAsAdmin();

        NeedRequest::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.need_requests.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_need_request_details()
    {
        $this->actingAsAdmin();

        $need_request = NeedRequest::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.need_requests.show', $need_request))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_need_requests_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.need_requests.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_need_request()
    {
        $this->actingAsAdmin();

        $need_requestsCount = NeedRequest::count();

        $response = $this->post(
            route('dashboard.need_requests.store'),
            NeedRequest::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $need_request = NeedRequest::all()->last();

        $this->assertEquals(NeedRequest::count(), $need_requestsCount + 1);

        $this->assertEquals($need_request->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_need_requests_edit_form()
    {
        $this->actingAsAdmin();

        $need_request = NeedRequest::factory()->create();

        $this->get(route('dashboard.need_requests.edit', $need_request))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_need_request()
    {
        $this->actingAsAdmin();

        $need_request = NeedRequest::factory()->create();

        $response = $this->put(
            route('dashboard.need_requests.update', $need_request),
            NeedRequest::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $need_request->refresh();

        $response->assertRedirect();

        $this->assertEquals($need_request->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_need_request()
    {
        $this->actingAsAdmin();

        $need_request = NeedRequest::factory()->create();

        $need_requestsCount = NeedRequest::count();

        $response = $this->delete(route('dashboard.need_requests.destroy', $need_request));

        $response->assertRedirect();

        $this->assertEquals(NeedRequest::count(), $need_requestsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_need_requests()
    {
        if (! $this->useSoftDeletes($model = NeedRequest::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        NeedRequest::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.need_requests.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_need_request_details()
    {
        if (! $this->useSoftDeletes($model = NeedRequest::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $need_request = NeedRequest::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.need_requests.trashed.show', $need_request));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_need_request()
    {
        if (! $this->useSoftDeletes($model = NeedRequest::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $need_request = NeedRequest::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.need_requests.restore', $need_request));

        $response->assertRedirect();

        $this->assertNull($need_request->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_need_request()
    {
        if (! $this->useSoftDeletes($model = NeedRequest::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $need_request = NeedRequest::factory()->create(['deleted_at' => now()]);

        $need_requestCount = NeedRequest::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.need_requests.forceDelete', $need_request));

        $response->assertRedirect();

        $this->assertEquals(NeedRequest::withoutTrashed()->count(), $need_requestCount - 1);
    }

    /** @test */
    public function it_can_filter_need_requests_by_name()
    {
        $this->actingAsAdmin();

        NeedRequest::factory()->create([
            'name' => 'Foo',
        ]);

        NeedRequest::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.need_requests.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('need_requests.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
