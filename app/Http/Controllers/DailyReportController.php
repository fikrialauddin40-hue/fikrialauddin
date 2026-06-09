<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\ReportDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DailyReport::where('user_id', $user->id);

        if ($request->filled('mood')) {
            $query->where('mood', $request->mood);
        }
        if ($request->filled('dari')) {
            $query->whereDate('report_date', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('report_date', '<=', $request->sampai);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(12);

        $partnerReports = collect();
        if ($user->hasPartner()) {
            $partnerReports = DailyReport::where('user_id', $user->partner_id)
                ->orderBy('report_date', 'desc')
                ->take(5)
                ->get();
        }

        return view('reports.index', compact('reports', 'partnerReports'));
    }

    public function create()
    {
        $todayReport = DailyReport::byUser(Auth::id())->today()->first();
        if ($todayReport) {
            return redirect()->route('reports.edit', $todayReport)
                ->with('info', 'Kamu sudah mengisi laporan hari ini. Silakan edit.');
        }
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'mood' => 'required|string|max:50',
            'wake_up_time' => 'nullable|date_format:H:i',
            'sleep_time' => 'nullable|date_format:H:i',
            'activity' => 'required|string|max:255',
            'description' => 'nullable|string',
            'gratitude' => 'nullable|string',
            'private_note' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $report = DailyReport::create([
            'user_id' => Auth::id(),
            'report_date' => $request->report_date,
            'mood' => $request->mood,
            'wake_up_time' => $request->wake_up_time,
            'sleep_time' => $request->sleep_time,
            'activity' => $request->activity,
            'description' => $request->description,
            'gratitude' => $request->gratitude,
            'private_note' => $request->private_note,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('reports/' . $report->id, 'public');
                ReportDocument::create([
                    'daily_report_id' => $report->id,
                    'file_path' => $path,
                    'keterangan' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('reports.index')->with('success', 'Laporan harian berhasil ditambahkan!');
    }

    public function show(DailyReport $report)
    {
        if ($report->user_id !== Auth::id() && $report->user_id !== Auth::user()?->partner_id) {
            abort(403);
        }
        return view('reports.show', compact('report'));
    }

    public function edit(DailyReport $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, DailyReport $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'report_date' => 'required|date',
            'mood' => 'required|string|max:50',
            'wake_up_time' => 'nullable|date_format:H:i',
            'sleep_time' => 'nullable|date_format:H:i',
            'activity' => 'required|string|max:255',
            'description' => 'nullable|string',
            'gratitude' => 'nullable|string',
            'private_note' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $report->update($request->only([
            'report_date', 'mood', 'wake_up_time', 'sleep_time',
            'activity', 'description', 'gratitude', 'private_note'
        ]));

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('reports/' . $report->id, 'public');
                ReportDocument::create([
                    'daily_report_id' => $report->id,
                    'file_path' => $path,
                    'keterangan' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('reports.index')->with('success', 'Laporan harian berhasil diperbarui!');
    }

    public function destroy(DailyReport $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        foreach ($report->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }
        Storage::disk('public')->deleteDirectory('reports/' . $report->id);

        $report->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    public function deleteDocument(ReportDocument $document)
    {
        $report = $document->dailyReport;
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
