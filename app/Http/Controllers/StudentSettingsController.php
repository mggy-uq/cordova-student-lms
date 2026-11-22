<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.edit', [
            'student' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->getKey()),
            ],
        ]);

        $student->update([
            'name' => trim($validated['name']),
            'email' => mb_strtolower(trim($validated['email'])),
        ]);

        return redirect()
            ->route('settings.edit')
            ->with('profile_status', 'Your profile information has been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $student->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $student->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();

        return redirect()
            ->route('settings.edit')
            ->with('password_status', 'Your password has been changed.');
    }
}
