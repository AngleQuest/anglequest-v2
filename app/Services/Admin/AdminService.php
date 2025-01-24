<?php

namespace App\Services\Admin;

use App\Models\AdminBank;
use App\Models\Plan;
use App\Traits\ApiResponder;

class AdminService
{
    use ApiResponder;
    public function getAccountDetails()
    {
        $admin_bank =  AdminBank::first();
        if (!$admin_bank) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($admin_bank);
    }

    public  function updateDetails($data)
    {
        $admin_bank =  AdminBank::first();
        if ($admin_bank) {
            $admin_bank->update([
                'account_name' => $data->account_name,
                'account_number' => $data->account_number,
                'bank_name' => $data->bank_name,
                'country' => $data->country,
            ]);
        }
        $admin_bank = AdminBank::create([
            'account_name' => $data->account_name,
            'account_number' => $data->account_number,
            'bank_name' => $data->bank_name,
            'country' => $data->country,
        ]);
        return $this->successResponse($admin_bank, 200);
    }
}
