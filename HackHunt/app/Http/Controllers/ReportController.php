<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Users;
use App\Models\Program;
use App\Helper\AuthenticateUser;
use App\Helper\ProgramValidation;
use App\Models\Report;
use App\Models\ReportLog;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getReportData(Request $request,Report $report){
        $data = $report->getReportData();
        return response()->json($data);
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
    $reporter = AuthenticateUser::authenticatedUser($request);
    if (!$reporter) {
        return response()->json(['error' => 'No User Found with this data.'], 400);
    }

    $program_uuid = $report->program_id;
    $isValid = ProgramValidation::userOwnsProgram($program_uuid, $reporter->uuid);
    if (!$isValid) {
        return response()->json(['error' => 'You can’t do this.'], 400);
    }

    $oldStatus = $report->status;

    $data = $request->only([
        'status',
        'points',
        'bounty',
        'severity',
        'title',
        'type',
        'description',
        'attachments',
        'published'
    ]);

    if ($request->filled('status')) {
        if ($request->status === 'Triaged') {
            $data['triaged_at'] = now();
        } elseif ($request->status === 'Resolved') {
            $data['resolved_at'] = now();
        }
    }

    if ($request->filled('points')) {
        $data['rewarded'] = true;
    }

    $report->update($data);


    ReportLog::create([
        'report_id' => $report->uuid,
        'performed_by' => $reporter->uuid,
        'action' => 'status_updated',
        'details' => "Status changed from {$oldStatus} to " . ($request->status ?? $oldStatus) .
            ". Points: " . ($request->points ?? $report->points) .
            ", Bounty: " . ($request->bounty ?? $report->bounty),
    ]);

    return response()->json(['message' => 'Report updated'], 200);
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