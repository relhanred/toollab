<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FamilyController extends Controller
{
    const KEY_PHONE = 'phone';
    const KEY_ADDRESS = 'address';
    const KEY_ZIPCODE = 'zipcode';
    const KEY_CITY = 'city';
    const KEY_BIRTHDATE = 'birthdate';


    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'is_student' => 'boolean',
            'birthdate' => 'nullable|date|required_if:is_student,true'
        ]);

        $user = User::where('email', $request->email)->first();
        $responsibleRole = Role::where('slug', 'responsible')->first();

        if (!$responsibleRole) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le rôle de responsable n\'existe pas'
            ], 500);
        }

        DB::beginTransaction();
        try {
            if ($user) {
                $isAlreadyResponsible = $user->roles()
                    ->whereHas('role', function ($query) {
                        $query->where('slug', 'responsible');
                    })
                    ->exists();

                if ($isAlreadyResponsible) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cette adresse email est déjà associée à un responsable de famille'
                    ], 422);
                }
            } else {
                $user = User::create([
                    'first_name' => $request->firstname,
                    'last_name' => $request->lastname,
                    'email' => $request->email,
                    'password' => Hash::make(uniqid()),
                    'access' => true,
                ]);
            }

            $this->updateOrCreateUserInfo($user, self::KEY_PHONE, $request->phone);
            $this->updateOrCreateUserInfo($user, self::KEY_ADDRESS, $request->address);
            $this->updateOrCreateUserInfo($user, self::KEY_ZIPCODE, $request->zipcode);
            $this->updateOrCreateUserInfo($user, self::KEY_CITY, $request->city);

            $family = Family::create();

            $family->userRoles()->create([
                'user_id' => $user->id,
                'role_id' => $responsibleRole->id,
            ]);

            if ($request->is_student) {
                $studentRole = Role::where('slug', 'student')->first();

                if ($studentRole) {
                    $hasStudentRole = $user->roles()
                        ->where('role_id', $studentRole->id)
                        ->where('roleable_id', $family->id)
                        ->where('roleable_type', 'family')
                        ->exists();

                    if (!$hasStudentRole) {
                        $family->userRoles()->create([
                            'user_id' => $user->id,
                            'role_id' => $studentRole->id,
                        ]);
                    }

                    // Ajouter ou mettre à jour la date de naissance
                    $this->updateOrCreateUserInfo($user, self::KEY_BIRTHDATE, $request->birthdate);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Famille créée avec succès',
                'data' => [
                    'family_id' => $family->id,
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la création de la famille',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function updateOrCreateUserInfo(User $user, $key, $value)
    {
        if (is_null($value) || $value === '') {
            return;
        }

        UserInfo::updateOrCreate(
            [
                'user_id' => $user->id,
                'key' => $key
            ],
            [
                'value' => $value
            ]
        );
    }
}
