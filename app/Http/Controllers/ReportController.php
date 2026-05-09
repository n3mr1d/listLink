<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Link;
use App\Enum\Report as ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class ReportController extends Controller
{
    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'link_id' => 'required|exists:links,id',
            'type' => ['required', new Enum(ReportType::class)],
            'message' => 'nullable|string|max:1000',
        ]);

        Report::create([
            'link_id' => $request->link_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Report submitted successfully. Thank you for your feedback!');
    }

    /**
     * Admin: Display a listing of reports.
     */
    public function index()
    {
        $reports = Report::with(['link', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Admin: Accept a report.
     */
    public function accept($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'accepted']);

        return back()->with('success', 'Report marked as accepted.');
    }

    /**
     * Admin: Reject a report (or just keep it as pending/delete).
     */
    public function reject($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'rejected']);

        return back()->with('success', 'Report marked as rejected.');
    }

    /**
     * Admin: Delete a report.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return back()->with('success', 'Report deleted.');
    }
}
