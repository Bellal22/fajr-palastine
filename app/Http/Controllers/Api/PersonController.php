<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Routing\Controller;
use App\Http\Resources\SelectResource;
use App\Http\Resources\PersonResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PersonController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display a listing of the people.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $people = Person::filter()->simplePaginate();

        return PersonResource::collection($people);
    }

    /**
     * Display the specified person.
     *
     * @param \App\Models\Person $person
     * @return \App\Http\Resources\PersonResource
     */
    public function show(Person $person)
    {
        return new PersonResource($person);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function select()
    {
        $people = Person::filter()->simplePaginate();

        return SelectResource::collection($people);
    }
}
