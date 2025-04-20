<?php

namespace App\Services;

use App\Helper\AuthenticateUser;
use App\Models\Users;
use App\Helper\JwtHelper;
use App\Models\Report;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;


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

        return ['report_id' => $report->id];
    }
}
