<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CoupleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('couple.index', compact('user'));
    }

    public function generateCode()
    {
        $user = Auth::user();
        if ($user->hasPartner()) {
            return back()->with('error', 'Kamu sudah memiliki pasangan.');
        }
        $user->update(['partner_code' => strtoupper(Str::random(8))]);
        return back()->with('success', 'Kode undangan berhasil dibuat!');
    }

    public function regenerateCode()
    {
        $user = Auth::user();
        if ($user->hasPartner()) {
            return back()->with('error', 'Kamu sudah memiliki pasangan.');
        }
        $user->update(['partner_code' => strtoupper(Str::random(8))]);
        return back()->with('success', 'Kode undangan baru berhasil dibuat!');
    }

    public function connect(Request $request)
    {
        $request->validate([
            'partner_code' => 'required|string|size:8|exists:users,partner_code',
        ]);

        $user = Auth::user();

        if ($user->hasPartner()) {
            return back()->with('error', 'Kamu sudah terhubung dengan pasangan.');
        }

        $partner = User::where('partner_code', $request->partner_code)
            ->whereNull('partner_id')
            ->where('id', '!=', $user->id)
            ->first();

        if (!$partner) {
            return back()->with('error', 'Kode tidak valid atau pasangan sudah terhubung.');
        }

        if ($partner->hasPartner()) {
            return back()->with('error', 'Pasangan sudah terhubung dengan orang lain.');
        }

        $user->update(['partner_id' => $partner->id, 'partner_code' => null]);
        $partner->update(['partner_id' => $user->id, 'partner_code' => null]);

        return redirect()->route('dashboard')->with('success', 'Selamat! Kamu sekarang terhubung dengan ' . $partner->name . '!');
    }

    public function disconnect()
    {
        $user = Auth::user();
        if (!$user->hasPartner()) {
            return back()->with('error', 'Kamu belum memiliki pasangan.');
        }

        $partner = $user->partner;
        $user->update(['partner_id' => null]);
        if ($partner) {
            $partner->update(['partner_id' => null]);
        }

        return redirect()->route('couple.index')->with('success', 'Hubungan pasangan telah diputuskan.');
    }
}
