<?php

namespace App\Console\Commands;

use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessEmailSequences extends Command
{
    protected $signature = 'email:process-sequences
                           {--limit=100 : Maximum number of emails to send}
                           {--tenant= : Process sequences for specific tenant only}';

    protected $description = 'Process email sequences and send pending emails';

    public function __construct(protected EmailService $emailService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🚀 Starting email sequence processing...');
        
        $startTime = now();
        $limit = (int) $this->option('limit');
        $tenantId = $this->option('tenant');
        
        try {
            // Process sequences
            $emailsSent = $this->emailService->processSequences();
            
            $duration = $startTime->diffInSeconds(now());
            
            $this->info("✅ Email processing completed!");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Emails sent', $emailsSent],
                    ['Processing time', $duration . 's'],
                    ['Rate', $emailsSent > 0 ? round($emailsSent / max($duration, 1), 2) . ' emails/sec' : '0 emails/sec'],
                ]
            );
            
            if ($emailsSent > 0) {
                Log::info('Email sequences processed successfully', [
                    'emails_sent' => $emailsSent,
                    'duration_seconds' => $duration,
                ]);
            }
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error processing email sequences: ' . $e->getMessage());
            Log::error('Email sequence processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return self::FAILURE;
        }
    }
}
