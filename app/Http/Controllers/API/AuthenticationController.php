<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AuthenticationController extends Controller
{
    #[OA\Post(
        path: "/api/register",
        summary: "Register",
        description: "Creates a new user account",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "User object that needs to be created",
            content: new OA\JsonContent(
                required: ["name", "email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "jaycee@gmail.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Pass1234!")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Registration successful",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    /**
     * Register a new account.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:4',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'response_code' => 201,
                'status' => 'success',
                'message' => 'Successfully registered',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Registration failed',
            ], 500);
        }
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Login",
        description: "Authenticates a user and returns an access token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "User object that needs to be created",
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "jaycee@gmail.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Pass1234!")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Login successful",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    /**
     * Login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);


        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $accessToken = $user->createToken($user->email . 'authToken');

                return response()->json([
                    'response_code' => 200,
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $accessToken->plainTextToken,
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Login failed' . $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/user",
        summary: "Get user information.",
        security: [["sanctum" => []]],
        description: "Returns information about the authenticated user",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "User information retrieved successfully",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/User")
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            )
        ]
    )]
    /**
     * Get paginated user list (authenticated).
     */
    public function userInfo(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Fetched user information successfully',
                'data_user_list' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('User Information Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'Failed to fetch user information',
            ], 500);
        }
    }

    // public function userList()
    // {
    //     try {
    //         $users = User::latest()->paginate(10);

    //         return response()->json([
    //             'response_code' => 200,
    //             'status' => 'success',
    //             'message' => 'Fetched user list successfully',
    //             'data_user_list' => $users,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('User List Error: ' . $e->getMessage());

    //         return response()->json([
    //             'response_code' => 500,
    //             'status' => 'error',
    //             'message' => 'Failed to fetch user list',
    //         ], 500);
    //     }
    // }

    #[OA\Post(
        path: "/api/logout",
        security: [["sanctum" => []]],
        summary: "Logout",
        description: "Logs out the authenticated user and revokes their token",
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Logout successful",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    /**
     * Logout the user and revoke token.
     */
    public function logOut(Request $request)
    {
        try {
            if (Auth::check()) {
                Auth::user()->tokens()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status' => 'success',
                    'message' => 'Successfully logged out',
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'An error occurred during logout',
            ], 500);
        }
    }
}
