<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Users;
use App\Models\Program;
use App\Helper\AuthenticateUser;
use App\Models\Report;
use App\Models\ReportLog;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function store(Request $request, Program $program)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'required|string',
            'severity' => 'required|in:P1,P2,P3,P4,P5',
            'attachments.*' => 'nullable|file|mimes:jpg,png,mp4,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->reportService->store($request, $program);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['code'] ?? 400);
        }

        return response()->json([
            'message' => 'Report submitted',
            'report_id' => $result['report_id']
        ], 201);
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
    public function publish(Request $request,Report $report)
    {
        $reporter = AuthenticateUser::authenticatedUser($request);
        if (!$reporter) {
            return ['error' => 'No User Found with this data.', 'code' => 400];
        }
        $report->update(['published' => true]);
        ReportLog::create([
            'report_id' => $report->uuid,
            'performed_by' => $reporter->uuid,
            'action' => 'published',
            'details' => 'Report marked as published.',
        ]);
        return response()->json(['message' => 'Report published'], 200);
    }
}