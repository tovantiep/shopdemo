<?php

namespace App\Components;

use Illuminate\Database\Eloquent\Model;

interface InterfaceService
{
    /**
     * Display a listing of the resource.
     */
    public function index(array $filter);

    /**
     * Store a newly created resource in storage.
     */
    public function store($record);

    /**
     * Display the specified resource.
     */
    public function show(Model $model);

    /**
     * Update the specified resource in storage.
     */
    public function update($record, Model $model);

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Model $model);
}
