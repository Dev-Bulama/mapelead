<?php

namespace App\Console\Commands;

use App\Models\InstructorCode;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateInstructorCodes extends Command
{
    protected $signature = 'instructor:codes
                            {count=1 : Number of codes to generate}
                            {--note= : Optional note to attach to all generated codes}';

    protected $description = 'Generate instructor registration codes for admin use';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $note  = $this->option('note');

        if ($count < 1 || $count > 200) {
            $this->error('Count must be between 1 and 200.');
            return self::FAILURE;
        }

        $generated = [];

        for ($i = 0; $i < $count; $i++) {
            do {
                $code = strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            } while (InstructorCode::where('code', $code)->exists());

            $generated[] = InstructorCode::create([
                'code'      => $code,
                'note'      => $note,
                'is_active' => true,
            ]);
        }

        $this->info("Generated {$count} instructor code(s):");
        $this->newLine();

        $rows = array_map(fn($c) => [$c->code, $c->note ?? '—', $c->created_at->toDateTimeString()], $generated);
        $this->table(['Code', 'Note', 'Created'], $rows);

        return self::SUCCESS;
    }
}
