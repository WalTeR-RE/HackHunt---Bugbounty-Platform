<?php

namespace App\Http\Controllers;

use App\Helper\ProgramValidation;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\AuthenticateUser;

class ReportCommentController extends Controller
{
   
    public function Store(Request $request, Report $report)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'is_internal' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reporter = AuthenticateUser::authenticatedUser($request);
        if (!$reporter) {
            return response()->json(['error' => 'No User Found with this data.'], 400);
        }
        $program = $report->program;
        $owner = ProgramValidation::userIsOwnerOrAdmin($program->program_id, $reporter->uuid);

        if ($reporter->uuid !== $report->reporter && !$owner) {
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
        if ($current_user->uuid !== $report->reporter && !$owner) {
            return response()->json(['error' => 'You are not authorized to restore this report.'], 403);
        }

        $comments = $report->comments;
        $Users = [];
        foreach ($comments as $comment) {
            $user = Users::where('uuid', $comment->user_id)->first();
            if ($user) {
                $Users[] = [
                    'uuid' => $user->uuid,
                    'nickname' => $user->nickname,
                    'profile_picture' => $user->profile_picture,
                ];
            }
        }

        if($current_user->role_id >= 2){
        $comments = $comments->map(function ($comment) use ($Users) {
            $user = collect($Users)->firstWhere('uuid', $comment->user_id);
            return [
                'uuid' => $comment->uuid,
                'user' => $user,
                'comment' => $comment->comment,
                'is_internal' => $comment->is_internal,
                'created_at' => $comment->created_at,
            ];
        });
    }
    else{
        $comments = $comments
    ->filter(function ($comment) {
        return !$comment->is_internal;
    })
    ->map(function ($comment) use ($Users) {
        $user = collect($Users)->firstWhere('uuid', $comment->user_id);
        return [
            'uuid' => $comment->uuid,
            'user' => $user,
            'comment' => $comment->comment,
            'is_internal' => $comment->is_internal,
            'created_at' => $comment->created_at,
        ];
    });

    }
        $comments = $comments->sortByDesc('created_at')->values()->all();

        return response()->json($comments);
    }
}

