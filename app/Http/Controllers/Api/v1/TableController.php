<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Repositories\v1\TableRepository;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TableRepository::getAllTables();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataTable = $request->all();

        return TableRepository::storeTable($dataTable);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $tableId)
    {
        return TableRepository::getOneTable($tableId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $tableId)
    {
        $dataTable = $request->all();

        return TableRepository::updateTable($dataTable, $tableId);
    }

    /**
     * Remove the specified resource from DB.
     */
    public function sendToTrash(string $tableId)
    {
        return TableRepository::sendToTrash($tableId);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $tableId)
    {
        return TableRepository::destroyTable($tableId);
    }
}
