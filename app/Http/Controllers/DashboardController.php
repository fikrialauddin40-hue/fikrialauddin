<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $partner = $user->partner;

        $today = today();
        $myTodayReport = DailyReport::byUser($user->id)->today()->first();

        $partnerTodayReport = null;
        if ($partner) {
            $partnerTodayReport = DailyReport::byUser($partner->id)->today()->first();
        }

        $myTotalReports = DailyReport::where('user_id', $user->id)->count();
        $partnerTotalReports = $partner ? DailyReport::where('user_id', $partner->id)->count() : 0;

        $myReports = DailyReport::where('user_id', $user->id)
            ->orderBy('report_date', 'desc')
            ->take(5)
            ->get();

        $partnerRecentReports = null;
        if ($partner) {
            $partnerRecentReports = DailyReport::where('user_id', $partner->id)
                ->orderBy('report_date', 'desc')
                ->take(5)
                ->get();
        }

        $myStreak = $this->calculateStreak($user->id);

        $partnerStreak = $partner ? $this->calculateStreak($partner->id) : 0;

        $anniversary = $user->anniversary_date;
        $daysTogether = $anniversary ? Carbon::parse($anniversary)->diffInDays(today()) : 0;

        $weekDates = [];
        $myWeekData = [];
        $partnerWeekData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $weekDates[] = $date->format('D');

            $myDayReport = DailyReport::byUser($user->id)->whereDate('report_date', $date)->first();
            $myWeekData[] = $myDayReport ? 1 : 0;

            if ($partner) {
                $partnerDayReport = DailyReport::byUser($partner->id)->whereDate('report_date', $date)->first();
                $partnerWeekData[] = $partnerDayReport ? 1 : 0;
            }
        }

        $totalAllReports = DailyReport::count();

        return view('dashboard', compact(
            'user',
            'partner',
            'myTodayReport',
            'partnerTodayReport',
            'myTotalReports',
            'partnerTotalReports',
            'myReports',
            'partnerRecentReports',
            'myStreak',
            'partnerStreak',
            'daysTogether',
            'weekDates',
            'myWeekData',
            'partnerWeekData',
            'totalAllReports',
        ));
    }

    private function calculateStreak($userId)
    {
        $streak = 0;
        $date = today();

        while (true) {
            $report = DailyReport::byUser($userId)->whereDate('report_date', $date)->first();
            if (!$report) break;
            $streak++;
            $date->subDay();
        }

        return $streak;
    }
}
