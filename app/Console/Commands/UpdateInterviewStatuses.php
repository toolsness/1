<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Interview;
use App\Models\InterviewSchedule;
use App\Enum\InterviewStatus;
use App\Enum\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdateInterviewStatuses extends Command
{
    protected $signature = 'app:update-interview-statuses';
    protected $description = 'Update interview statuses and reservation statuses based on scheduled times';

    public function handle()
    {
        try {
            $now = Carbon::now();

            DB::beginTransaction();

            $interviews = Interview::with('interviewSchedule')
                ->where('status', InterviewStatus::INTERVIEW_CONFIRMED)
                ->get();

            $updatedCount = 0;

            foreach ($interviews as $interview) {
                if ($interview->interviewSchedule) {
                    $scheduleDate = Carbon::parse($interview->interviewSchedule->interview_date);
                    $scheduleTime = Carbon::parse($interview->interviewSchedule->interview_start_time);

                    $scheduleDateTime = $scheduleDate->setTime(
                        $scheduleTime->hour,
                        $scheduleTime->minute,
                        $scheduleTime->second
                    );

                    if ($scheduleDateTime <= $now) {
                        $interview->status = InterviewStatus::INTERVIEW_CONDUCTED;
                        $interview->save();

                        $interview->interviewSchedule->reservation_status = ReservationStatus::COMPLETE;
                        $interview->interviewSchedule->save();

                        $updatedCount++;
                    }
                }
            }

            DB::commit();

            $this->info("Interview statuses updated successfully. {$updatedCount} interviews were updated.");
            Log::info("Updated {$updatedCount} interviews to INTERVIEW_CONDUCTED status and their schedules to COMPLETE.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("An error occurred: " . $e->getMessage());
            Log::error("Error updating interview statuses: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
