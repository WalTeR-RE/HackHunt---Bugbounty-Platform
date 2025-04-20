<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportComment;
use Illuminate\Http\Request;

class ReportCommentController extends Controller
{
   
    public function Store(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string|max:5000',
            'is_internal' => 'nullable|boolean',
        ]);

        ReportComment::create([
            'report_id' => $report->uuid,
            'user_id' => auth()->user()->uuid,
            'comment' => $request->comment,
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Comment added.');
    }

   
    public function Publish(Report $report)
    {
        $comments = $report->comments;
        return response()->json($comments);
    }
}

