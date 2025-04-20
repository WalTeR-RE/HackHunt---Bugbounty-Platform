<?php

namespace App\Services;
use Illuminate\Http\Request;

use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgramService
{
    public function updateProgram(Request $request, $uuid)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'bounty_range' => 'nullable|string',
            'number_of_reports' => 'nullable|integer',
            'avg_bounty' => 'nullable|numeric',
            'vulnerabilities_rewarded' => 'integer',
            'fast_description' => 'nullable|string',
            'rewards' => 'nullable|string',
            'target_description' => 'nullable|string',
            'scope' => 'nullable|string',
            'description_rules' => 'nullable|string',
            'status' => 'string',
        ]);

        $program = Program::where('program_id', $uuid)->update([
            'name' => $request->input('name'),
            'bounty_range' => $request->input('bounty_range'),
            'number_of_reports' => $request->input('number_of_reports'),
            'avg_bounty' => $request->input('avg_bounty'),
            'vulnerabilities_rewarded' => $request->input('vulnerabilities_rewarded'),
            'fast_description' => $request->input('fast_description'),
            'rewards' => $request->input('rewards'),
            'target_description' => $request->input('target_description'),
            'scope' => $request->input('scope'),
            'description_rules' => $request->input('description_rules'),
            'status' => $request->input('status'),
        ]);

        return $program;



    }
}
