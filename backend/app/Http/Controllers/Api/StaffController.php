<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\InvitationToken;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Notifications\StaffInvitation;
use Illuminate\Support\Str;

class StaffController extends Controller
{

    public function createStaffUser(StaffRequest $request)
    {
        $school = School::findOrFail($request->school_id);
        $role = Role::where('slug', $request->role)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Le rôle spécifié n\'existe pas'
            ], 422);
        }

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            $existingRole = $school->userRoles()
                ->where('user_id', $existingUser->id)
                ->first();

            if ($existingRole) {
                $existingRole->update([
                    'role_id' => $role->id,
                ]);

                $message = 'Le rôle de l\'utilisateur existant a été mis à jour.';
            } else {
                $school->userRoles()->create([
                    'user_id' => $existingUser->id,
                    'role_id' => $role->id,
                ]);

                $message = 'Utilisateur existant ajouté à l\'école avec le rôle spécifié.';
            }

            $user = $existingUser;
        } else {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt(Str::random(32)),
                'access' => true,
            ]);

            $school->userRoles()->create([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

            $token = Str::random(64);

            InvitationToken::create([
                'email' => $user->email,
                'token' => $token,
                'expires_at' => now()->addDays(7),
            ]);

            $user->notify(new StaffInvitation($school->name, $role->name, $token));

            $message = 'Utilisateur créé avec succès. Un email d\'invitation a été envoyé.';
        }

        return response()->json([
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $role->name,
            ]
        ], 201);
    }
}
