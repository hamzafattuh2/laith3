<?php

namespace App\Http\Controllers;
use App\Models\Tourist;
use App\Models\User;
use Illuminate\Support\Facades\DB; // أضف هذا السطر
use Illuminate\Support\Facades\Hash; // أضف هذا السطر
use Illuminate\Http\Request;

class TouristController extends Controller
{
    public function registerTourist(Request $request)
    {
        try {
            // التحقق من البيانات بما فيها الصورة
            $validatedData = $request->validate([

                //user
                'user_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'first_name' => 'required|string|max:50',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone_number' => 'required|string|max:20',
                'profile_image' => 'nullable|image|mimes:jpeg,png|max:2048',
                'type'=>'nullable|string',
                'gender' => 'required|in:male,female',
                'birth_date' => 'required|date',//2



                //tourist
                'nationality' => 'required|string|max:100',//1
                'emergency_contact' => 'required|string|max:20',//4
                'special_needs' => 'nullable|string',//3
            ]);


            // بدء المعاملة لضمان سلامة البيانات
            DB::beginTransaction();

            // تخزين صورة الملف الشخصي إذا وجدت
            $profilePicturePath = null;
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('public/profile_image');
                $profilePicturePath = str_replace('public/', '', $path);
            }

            // إنشاء المستخدم في جدول users
            $user = User::create([
                'user_name' => $validatedData['user_name'] ,
                'first_name' => $validatedData['first_name'] ,
                'last_name' => $validatedData['last_name'] ,
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'type' => 'tourist',
                'phone_number' => $validatedData['phone_number'],
                'profile_image' => $profilePicturePath ?? null ,
                'gender'=> $validatedData['gender'],
                'birth_date' => $validatedData['birth_date']?? null,

            ]);
            $user->generateCode();

            // إنشاء السائح في جدول tourists
            $tourist = Tourist::create([
                'user_id' => $user->id,
                'nationality' => $validatedData['nationality'],
                'emergency_contact' => $validatedData['emergency_contact'],
                'special_needs' => $validatedData['special_needs'] ?? null,
            ]);

            // إتمام المعاملة
            DB::commit();

            return response()->json([
                'message' => 'Tourist registered successfully',
                'user' => $user,
                'tourist' => $tourist,
                'profile_picture_url' => $profilePicturePath ? asset('storage/'.$profilePicturePath) : null
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function loginTourist(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);

            // البحث عن المرشد السياحي
            $tourist = User::where('email', $request->email)->first();
            if (!$tourist || $tourist->type != 'tourist') {
                return response()->json([
                    'message' => 'You are not a tourist'
                ], 401);
            }
            // التحقق من وجود المرشد وصحة كلمة المرور
            if (!$tourist || !Hash::check($request->password, $tourist->password)) {
                return response()->json([
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // إنشاء توكن جديد
            $token = $tourist->createToken('tourist_auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'tour_guide' => [
                      //user
                    'id' => $tourist->id,
                    'user_name' => $tourist->user_name ,
                    'name' => $tourist->first_name . ' ' . $tourist->last_name,
                    'email' => $tourist->email,
                    'type'=>$tourist->type,
                    'phone_number'=>$tourist->phone_number,
                    'gender'=>$tourist->gender,
                    'profile_image' => $tourist->guide_picture_path ? asset('storage/'.$tourist->guide_picture_path) : null,
                    'birth_date'=>$tourist->birth_date,
                    //tourist
                //     , 'nationality'=>$tourist->nationality,
                //    , 'special_needs'=>$tourist->special_needs,
                //    , 'emergency_contact'=>$tourist->emergency_contact,

                ],
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function logoutTourist(Request $request)
    {
        try {
            // الحصول على المستخدم الحالي (المرشد السياحي)
            $tourist = $request->user();

            // حذف التوكن الحالي
            $tourist->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful',
                'details' => [
                    'tour_guide_id' => $tourist->id,
                    'name' => $tourist->first_name . ' ' . $tourist->last_name,
                    'email' => $tourist->email
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
