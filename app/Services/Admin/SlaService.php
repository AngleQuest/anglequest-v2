<?php

namespace App\Services\Admin;

use App\Models\Sla;

class SlaService
{
    public static function getAll()
    {
        return Sla::latest('id')->get();
    }
    public static function store($data)
    {
        return Sla::create([
            'name' => $data->name,
            'features' => json_encode($data->features)
        ]);
    }
    public static function edit($id)
    {
        $sla = Sla::find($id);
        return $sla;
    }
    public static function update($id, $data)
    {
        $sla = Sla::find($id);
        $sla->update([
            'name' => $data->name,
            'features' => json_encode($data->features)
        ]);
    }
    public static function delete($id)
    {
        $sla = Sla::find($id);
        $sla->delete();
    }
}
