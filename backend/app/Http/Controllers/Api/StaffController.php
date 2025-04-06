<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvitationToken;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Notifications\StaffInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    /**
     * Créer un nouvel utilisateur staff (admin ou registar)
     */
    public function createStaffUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,registar',
            'school_id' => 'required|exists:schools,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Les données fournies sont incorrectes',
                'errors' => $validator->errors()
            ], 422);
        }

        $userRole = auth()->user()->roles()
            ->whereIn('role_id', function ($query) {
                $query->select('id')
                    ->from('roles')
                    ->whereIn('slug', ['director']);
            })
            ->where('roleable_type', 'school')
            ->where('roleable_id', $request->school_id)
            ->first();

        if (!$userRole) {
            return response()->json([
                'message' => 'Vous n\'avez pas les droits nécessaires pour effectuer cette action'
            ], 403);
        }

        $school = School::findOrFail($request->school_id);

        $role = Role::where('slug', $request->role)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Le rôle spécifié n\'existe pas'
            ], 422);
        }

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

        // Envoyer l'email d'invitation
        $user->notify(new StaffInvitation($school->name, $role->name, $token));

        return response()->json([
            'message' => 'Utilisateur créé avec succès. Un email d\'invitation a été envoyé.',
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
