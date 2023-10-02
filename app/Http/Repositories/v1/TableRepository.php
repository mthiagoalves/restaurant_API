<?php

namespace App\Http\Repositories\v1;

use App\Http\Resources\v1\TableResource;
use App\Models\Table;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Validator;

class TableRepository
{
    use HttpResponses;

    public static function getAllTables()
    {
        return TableResource::collection(Table::all());
    }

    public static function getOneTable($tableId)
    {
        $table = Table::find($tableId);

        if (!$table) {
            $tableTrashed = Table::onlyTrashed()->find($tableId);

            if (!$tableTrashed) {
                return HttpResponses::error('Table not found', 404);
            }

            return HttpResponses::success('Table was deleted', 200, new TableResource($tableTrashed));
        }

        return new TableResource($table);
    }

    public static function storeTable($dataTable)
    {
        $validator = Validator::make($dataTable, [
            'number' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $tableValidated = $validator->validated();

        if (Table::where('number', $tableValidated['number'])->exists()) {
            return HttpResponses::error('Table already exist, please insert another number', 422);
        }

        $tableCreated = Table::create($tableValidated);

        if ($tableCreated) {
            return HttpResponses::success('Table created successfully', 200, new TableResource($tableCreated));
        }

        return HttpResponses::error('Something wrong to create table', 400);
    }

    public static function updateTable($dataTable, $tableId)
    {
        $validator = Validator::make($dataTable, [
            'number' => 'integer|required',
        ]);

        if ($validator->fails()) {
            return HttpResponses::error('Data invalid', 422, $validator->errors());
        }

        $tableAtUpdated = Table::find($tableId);

        if (!$tableAtUpdated) {
            $tableTrashed = Table::onlyTrashed()->find($tableId);

            if (!$tableTrashed) {
                return HttpResponses::error('Table not found', 404);
            }

            return HttpResponses::success('Table was deleted', 200, new TableResource($tableTrashed));
        }

        $tableValidated = $validator->validated();

        if (Table::where('number', $tableValidated['number'])->exists()) {
            return HttpResponses::error('Table already exist, please insert another number', 422);
        }

        $tableAtUpdated->update([
            "number" => $tableValidated['number'],
        ]);

        if ($tableAtUpdated) {
            return HttpResponses::success('Table has been updated', 200, new TableResource($tableAtUpdated));
        }

        return HttpResponses::error('Something wrong to update table', 422);
    }

    public static function sendToTrash($tableId)
    {
        $tableAtDeleted = Table::find($tableId);

        if (!$tableAtDeleted) {
            $tableTrashed = Table::onlyTrashed()->find($tableId);

            if (!$tableTrashed) {
                return HttpResponses::error('Table not found', 404);
            }

            return HttpResponses::success('Table was deleted', 200, new TableResource($tableTrashed));
        }

        $tableAtDeleted->delete();

        if ($tableAtDeleted) {
            return HttpResponses::success('User has been deleted', 200, new TableResource($tableAtDeleted));
        }
        return HttpResponses::error('Something wrong to delete table', 422);
    }

    public static function destroyTable($tableId)
    {
        $tableAtDestoyed = Table::find($tableId);

        if (!$tableAtDestoyed) {
            $tableTrashed = Table::onlyTrashed()->find($tableId);

            if (!$tableTrashed) {
                return HttpResponses::error('Table not found', 404);
            }

            return HttpResponses::success('Table was deleted', 200, new TableResource($tableTrashed));
        }

        $tableAtDestoyed->forceDelete();

        if ($tableAtDestoyed) {
            return HttpResponses::success('Table has been deleted', 200, new TableResource($tableAtDestoyed));
        }
        return HttpResponses::error('Something wrong to delete table', 422);
    }
}
