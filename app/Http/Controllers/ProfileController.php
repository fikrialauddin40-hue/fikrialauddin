<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();

        if ($request->has('gender')) {
            $data['gender'] = $request->gender;
        }
        if ($request->has('birth_date')) {
            $data['birth_date'] = $request->birth_date;
        }
        if ($request->has('anniversary_date')) {
            $data['anniversary_date'] = $request->anniversary_date;
        }
        if ($request->has('bio')) {
            $data['bio'] = $request->bio;
        }
        if ($request->has('no_hp')) {
            $data['no_hp'] = $request->no_hp;
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('avatars', 'public');
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
