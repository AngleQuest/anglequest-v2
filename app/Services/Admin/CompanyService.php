<?php

namespace App\Services\Admin;

use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Company;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Mail\OpenAccountEmail;
use App\Models\IndividualPlan;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CompanyService
{
    use ApiResponder;
    public function getAll()
    {
        $companies =  Company::latest('id')->get();
        if ($companies->isEmpty()) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($companies);
    }

    public  function store($data)
    {
        $company = Company::create([
            'name' => $data->name,
            'email' => $data->email,
            'administrator_name' => $data->administrator_name,
            'website' => $data->website ?? null,
        ]);
        $password = Str::random(6);
        $user = User::create([
            'company_id' => $company->id,
            'email' => strtolower($data->email),
            'password' => Hash::make($data->password),
            'role' => UserRole::BUSINESS
        ]);
        $detail = [
            'email' => $data->email,
            'password' => $password,
        ];
        ActivityLog::createRow($user->email, 'New Account created by Admin');
        Mail::to($user->email)->queue(new OpenAccountEmail($detail));
        return $this->successResponse($company);
    }


    public function edit($id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($company);
    }

    public function update($id, $data)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->errorResponse('No record found', 422);
        }

        if (!empty($data->email) && User::where('email', $data->email)->where('id', '!=', $id)->exists()) {
            return $this->errorResponse("Email already exists.",422);
        }

        $company->update([
            'name' => $data->name,
            'email' => $data->email,
            'administrator_name' => $data->administrator_name,
            'website' => $data->website,
        ]);
        return $this->successResponse($company);
    }

    public function delete($id)
    {
        $company = Company::find($id);
        if (!$company) {
            return $this->errorResponse('No record found', 422);
        }

        $employees = User::where('company_id', $company->id)->get();
        if (count($employees) > 0) {
            foreach ($employees as $employee) {
                IndividualProfile::where('user_id', $employee->id)->delete();
                $employee->delete();
            }
        }

        $company->delete();
        return $this->successResponse('Company Deleted with all assigned employees successfully');
    }
}
