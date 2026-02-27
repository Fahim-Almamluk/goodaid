<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class BeneficiaryAuthController extends Controller
{
    /**
     * Show verification form (check national ID)
     */
    public function showVerificationForm()
    {
        // If already logged in, redirect to dashboard
        if (Session::has('beneficiary_id')) {
            return redirect()->route('beneficiary.dashboard');
        }
        
        return view('beneficiary.verify');
    }

    /**
     * Verify national ID
     */
    public function verify(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string|size:9',
        ], [
            'national_id.required' => 'رقم الهوية مطلوب',
            'national_id.size' => 'رقم الهوية يجب أن يكون 9 أرقام',
        ]);

        $beneficiary = Beneficiary::where('national_id', $request->national_id)
            ->where('is_active', true)
            ->first();

        if (!$beneficiary) {
            return back()->withErrors([
                'national_id' => 'رقم الهوية غير موجود في النظام أو الحساب غير مفعّل',
            ])->withInput();
        }

        // Store beneficiary ID in session for next step
        Session::put('beneficiary_verification_id', $beneficiary->id);
        Session::put('beneficiary_verification_name', $beneficiary->name);

        // Check if password is already set
        if ($beneficiary->has_set_password) {
            // Redirect to login
            return redirect()->route('beneficiary.login');
        } else {
            // Redirect to set password
            return redirect()->route('beneficiary.set-password');
        }
    }

    /**
     * Show set password form
     */
    public function showSetPasswordForm()
    {
        if (!Session::has('beneficiary_verification_id')) {
            return redirect()->route('beneficiary.verify');
        }

        $beneficiaryId = Session::get('beneficiary_verification_id');
        $beneficiary = Beneficiary::find($beneficiaryId);

        if (!$beneficiary || $beneficiary->has_set_password) {
            return redirect()->route('beneficiary.verify');
        }

        return view('beneficiary.set-password', [
            'beneficiary' => $beneficiary,
        ]);
    }

    /**
     * Set password
     */
    public function setPassword(Request $request)
    {
        if (!Session::has('beneficiary_verification_id')) {
            return redirect()->route('beneficiary.verify');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 6 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $beneficiaryId = Session::get('beneficiary_verification_id');
        $beneficiary = Beneficiary::find($beneficiaryId);

        if (!$beneficiary) {
            return redirect()->route('beneficiary.verify');
        }

        // Set password
        $beneficiary->password = Hash::make($request->password);
        $beneficiary->password_set_at = now();
        $beneficiary->has_set_password = true;
        $beneficiary->save();

        // Clear verification session
        Session::forget('beneficiary_verification_id');
        Session::forget('beneficiary_verification_name');

        // Login beneficiary
        Session::put('beneficiary_id', $beneficiary->id);
        Session::put('beneficiary_name', $beneficiary->name);

        return redirect()->route('beneficiary.dashboard')
            ->with('success', 'تم إنشاء كلمة المرور بنجاح. مرحباً بك في النظام!');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Session::has('beneficiary_id')) {
            return redirect()->route('beneficiary.dashboard');
        }

        return view('beneficiary.login');
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string|size:9',
            'password' => 'required|string',
        ], [
            'national_id.required' => 'رقم الهوية مطلوب',
            'national_id.size' => 'رقم الهوية يجب أن يكون 9 أرقام',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $beneficiary = Beneficiary::where('national_id', $request->national_id)
            ->where('is_active', true)
            ->first();

        if (!$beneficiary || !$beneficiary->has_set_password) {
            return back()->withErrors([
                'national_id' => 'رقم الهوية غير موجود أو لم يتم إنشاء كلمة مرور بعد',
            ])->withInput();
        }

        if (!Hash::check($request->password, $beneficiary->password)) {
            return back()->withErrors([
                'password' => 'كلمة المرور غير صحيحة',
            ])->withInput();
        }

        // Login beneficiary
        Session::put('beneficiary_id', $beneficiary->id);
        Session::put('beneficiary_name', $beneficiary->name);

        return redirect()->route('beneficiary.dashboard')
            ->with('success', 'مرحباً بك في النظام');
    }

    /**
     * Logout
     */
    public function logout()
    {
        Session::forget('beneficiary_id');
        Session::forget('beneficiary_name');
        
        return redirect()->route('beneficiary.verify')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Get current beneficiary
     */
    public static function getCurrentBeneficiary()
    {
        if (!Session::has('beneficiary_id')) {
            return null;
        }

        return Beneficiary::find(Session::get('beneficiary_id'));
    }
}
