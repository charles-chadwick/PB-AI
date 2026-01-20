<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $total_patients = Patient::count();
        $total_users = User::count();
        $new_patients_this_month = Patient::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $recent_patients = Patient::with(['created_by' => function ($query) {
                $query->select('id', 'first_name', 'last_name')
                    ->without(['created_by', 'updated_by', 'deleted_by']);
            }])
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'total_patients' => $total_patients,
            'total_users' => $total_users,
            'new_patients_this_month' => $new_patients_this_month,
            'recent_patients' => $recent_patients,
        ]);
    }
}
