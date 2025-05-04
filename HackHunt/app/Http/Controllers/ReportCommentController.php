<?php

namespace App\Http\Controllers;

use App\Helper\ProgramValidation;
use App\Models\Report;
use App\Models\ReportComment;
use Illuminate\Http\Request;
use App\Helper\AuthenticateUser;

class ReportCommentController extends Controller
{
   
    public function Store(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string|max:5000',
            'is_internal' => 'nullable|boolean',
        ]);

        $reporter = AuthenticateUser::authenticatedUser($request);
        if (!$reporter) {
            return response()->json(['error' => 'No User Found with this data.'], 400);
        }
        $program = $report->program;
        $owner = ProgramValidation::userIsOwnerOrAdmin($program->program_id, $reporter->uuid);

        if ($reporter->uuid !== $report->reporter_id && !$owner) {
            return response()->json(['error' => 'You are not authorized to comment on this report.'], 403);
        }

        if($request->is_internal && !$owner) {
            return response()->json(['error' => 'You are not authorized to add internal comments.'], 403);
        }

        ReportComment::create([
            'report_id' => $report->uuid,
            'user_id' => $reporter->uuid,
            'comment' => $request->comment,
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Comment added.');
    }

   
    public function restore(Request $request,Report $report)
    {   
        $current_user = AuthenticateUser::authenticatedUser($request);
        if (!$current_user) {
            return response()->json(['error' => 'No User Found with this data.'], 400);
        }
        $program = $report->program;
        $owner = ProgramValidation::userIsOwnerOrAdmin($program->program_id, $current_user->uuid);
        if ($current_user->uuid !== $report->reporter_id && !$owner) {
            return response()->json(['error' => 'You are not authorized to restore this report.'], 403);
        }

        $comments = $report->comments;
        return response()->json($comments);
    }
}

