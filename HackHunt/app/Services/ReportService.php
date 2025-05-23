<?php

namespace App\Services;

use App\Helper\AuthenticateUser;
use App\Helper\ProgramValidation;
use App\Models\Users;
use App\Helper\JwtHelper;
use App\Models\Report;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Services\MailService;
use Illuminate\Support\Facades\Mail;


class ReportService
{
    public function store(Request $request, $program)
    {
        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file->getPathname());
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'video/mp4', 'application/pdf'];

                if (!in_array($mime, $allowedMimes)) {
                    return ['error' => 'One or more attachments have invalid file types.', 'code' => 422];
                }

                $attachments[] = $file->store('attachments', 'public');
            }
        }

        $reporter = AuthenticateUser::authenticatedUser($request);
        if (!$reporter) {
            return ['error' => 'No User Found with this data.', 'code' => 400];
        }

        $report = Report::create([
            'uuid' => Uuid::uuid4()->toString(),
            'reporter' => $reporter->uuid,
            'program_id' => $program->program_id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'severity' => $request->severity,
            'attachments' => json_encode($attachments),
            'status' => 'New',
        ]);
        return ['report_id' => $report->uuid];
    }


    public function reward($request, $report)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        if (!$user) {
            return ['error' => 'No User Found with this data.', 'code' => 400];
        }

        $isOwner = ProgramValidation::userIsOwnerOrAdmin($report->program_id, $user->uuid);

        if (!$isOwner && $user->role_id !== 3) {
            return ['error' => 'You are not authorized to reward this report.', 'code' => 403];
        }

        $researcher = Users::where('uuid', $report->reporter)->first();
        if (!$researcher) {
            return ['error' => 'Researcher not found.', 'code' => 404];
        }

        $report->bounty = $request->bounty;
        $report->points = $request->points;
        $report->rewarded = true;
        $report->save();

        $program = $report->program;
        $program->vulnerabilities_rewarded += 1;
        $program->number_of_reports += 1;
        $program->avg_bounty = $program->total_bounty / $program->vulnerabilities_rewarded;
        $program->save();

        $researcher->total_points += $request->points;
        $researcher->vulnerabilities_count+= 1;
        $researcher->save();

        AuthenticateUser::updateUsersRanks();

        $data = [
            'name' => ($researcher->name." [ ".$researcher->nickname." ]"),
            'program_name' => $program->name,
            'bounty' =>  "Bounty: {$request->bounty}",
            'points' => "Points: {$request->points}",
            'title' => $report->title
        ];

        Mail::to($researcher->email)->send(new MailService($data, 'bounty'));

        return ['message' => 'Report rewarded successfully.'];
    }
}
