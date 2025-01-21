<?php

namespace App\Services\Business;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Mail\EmailVerification;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeService
{
    use ApiResponder;
    public static function getAllEmployees()
    {
        $user = Auth::user();
        return User::where('company_id', $user->company->id)->where('id', '!=', $user->id)->latest('id')->get();
    }

    public function store($data)
    {
        $user = Auth::user();
        $employee = '';
        DB::beginTransaction();
        if ($data) {
            $employee = User::create([
                'email' => strtolower($data->email),
                'password' => Hash::make('password12345'),
                'role' => UserRole::INDIVIDUAL,
                'email_verified_at' => now(),
                'company_id' => $user->company->id
            ]);

            IndividualProfile::create([
                'user_id' => $employee->id,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'gender' => $data->gender,
                'current_role' => $data->current_role,
                'target_role' => $data->target_role,
                'specialization' => $data->specialization,
            ]);

            if ($employee) {
                DB::commit();
                $detail = [
                    'name' => $user->company->name,
                    'email' => $employee->email,
                    'password' => 'password12345'
                ];
                if ($employee) Mail::to($employee->email)->send(new EmailInvitation($detail));
                return $employee;
            }
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }
    public function emailInvitaion($data)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($data->email); $i++) {
                $email = User::where('email', $data->email[$i])->first();
                if ($email) {
                    return $this->errorResponse('Email already taken', 422);
                }
                $employee = User::create([
                    'email' => $data->email[$i],
                    'company_id' => $user->company->id,
                    'password' => Hash::make('password12345'),
                    'role' => UserRole::INDIVIDUAL,
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]);
                IndividualProfile::create([
                    'user_id' => $employee->id,
                    'email' => $data->email[$i],
                ]);

                $detail = [
                    'name' => $user->company->name,
                    'email' => $employee->email,
                    'password' => 'password12345'
                ];
                if ($employee) Mail::to($employee->email)->send(new EmailInvitation($detail));

                set_time_limit(60 * 60);
            }


            DB::commit();
            return $this->successResponse('Invitation Sent successfully', 200);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
        }
    }
    public function UploadEmployees($data)
    {
        $user = Auth::user();
        // Load the file
        $file = $data->file('file');

        if ($data->file) {
            DB::beginTransaction();
            try {

                $spreadsheet = IOFactory::load($file->getPathname());

                // Get the first worksheet
                $sheet = $spreadsheet->getActiveSheet();

                // Loop through rows and store data
                foreach ($sheet->getRowIterator(2) as $row) { // Start from row 2 (skip header)
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $email = User::where('email', $rowData[2])->first();
                    if ($email) {
                        return $this->errorResponse('Email already exist', 422);
                    }

                    // Create account for the employee
                    $employee = User::create([
                        'email' => strtolower($rowData[2]),
                        'password' => Hash::make('password12345'),
                        'role' => UserRole::INDIVIDUAL,
                        'email_verified_at' => now()->toDateString(),
                        'company_id' => $user->company->id
                    ]);

                    // Create employee profile
                    IndividualProfile::create([
                        'user_id' => $employee->id,
                        'first_name' => $rowData[0],
                        'last_name' => $rowData[1],
                        'email' => $rowData[2],
                        'specialization' => $rowData[3],
                        'current_role' => $rowData[4],
                        'target_role' => $rowData[5],
                    ]);

                    $detail = [
                        'name' => $user->company->name,
                        'email' => $rowData[2],
                        'password' => 'password12345'
                    ];

                    if ($employee) Mail::to($employee->email)->send(new EmailInvitation($detail));
                    set_time_limit(60 * 60);
                }
                DB::commit();
                return $this->successResponse('File imported successfully', 200);
            } catch (\Exception $e) {
                return $e;
                DB::rollBack();
            }
            return $this->errorResponse('File Upload failed', 422);
        }
    }

    public function delete($id)
    {
        $user = Auth::user();
        $employee = User::where('company_id', $user->company->id)->find($id);

        if ($id == $user->id) return $this->errorResponse('This user can not be deleted', 422);
        if (!$employee) return $this->errorResponse('No record found', 422);

        $employee->delete();
        return $this->successResponse('Employee deleted', 200);
    }

    public function bulkDelete($data)
    {
        $user = Auth::user();
        $employees = User::whereIn('id', $data->user_ids)->where('company_id', $user->company->id)->get();
        if ($employees->isEmpty()) {
            return $this->errorResponse('No record found', 422);
        }
        foreach ($employees as $employee) {
            if ($employee->id == $user->id) return $this->errorResponse('This user can not be deleted', 422);
            $employee->save();
            $employee->delete();
        }

        return $this->successResponse('Employees deleted Successfully', 200);
    }
}
