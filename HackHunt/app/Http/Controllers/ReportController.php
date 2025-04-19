<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Program;
use App\Models\Report;
use App\Models\ReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
class ReportController extends Controller
{
    public function Store(StoreReportRequest $request, Program $program)
    {
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store('attachments', 'public');
            }
        }
        $report = Report::create([
            'uuid' => Uuid::uuid4()->toString(),
            'reporter' => Auth::id(),
            'program_id' => $program->id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'severity' => $request->severity,
            'attachments' => json_encode($attachments),
            'status' => 'New',
        ]);
        return response()->json(['message' => 'Report submitted', 'report_id' => $report->id], 201);
    }
    public function Update(Request $request, Report $report)
    {
        $oldStatus = $report->status;
        $report->update([
            'status' => $request->status,
            'points' => $request->points,
            'bounty' => $request->bounty,
            'triaged_at' => $request->status === 'Triaged' ? now() : null,
            'resolved_at' => $request->status === 'Resolved' ? now() : null,
            'rewarded' => $request->status === 'Resolved' ? true : false,
        ]);

        ReportLog::create([
            'report_id' => $report->id,
            'performed_by' => Auth::id(),
            'action' => 'status_updated',
            'details' => "Status changed from {$oldStatus} to {$request->status}. Points: " . ($request->points ?? 0) . ", Bounty: " . ($request->bounty ?? 0),
        ]);
        return response()->json(['message' => 'Report triaged'], 200);
    }
    public function publish(Report $report)
    {
        $report->update(['published' => true]);
        ReportLog::create([
            'report_id' => $report->id,
            'performed_by' => Auth::id(),
            'action' => 'published',
            'details' => 'Report marked as published.',
        ]);
        return response()->json(['message' => 'Report published'], 200);
    }
}