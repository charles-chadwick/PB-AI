<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ImportPatientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patients:import {file=database/src/vb.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import patients from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file_path = base_path($this->argument('file'));

        if (!file_exists($file_path)) {
            $this->error("File not found: {$file_path}");
            return 1;
        }

        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('No users found in database. Please create users first.');
            return 1;
        }

        $this->info('Reading CSV file...');
        $csv_data = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $progress_bar = $this->output->createProgressBar(count($csv_data));
        $progress_bar->start();

        $imported_count = 0;
        $skipped_count = 0;

        // Temporarily disable mass assignment protection
        Model::unguard();

        foreach ($csv_data as $line) {
            $columns = str_getcsv($line);

            if (count($columns) < 3) {
                $skipped_count++;
                $progress_bar->advance();
                continue;
            }

            $full_name = trim($columns[1]);
            $avatar_filename = trim($columns[2]);

            // Parse name
            $name_parts = $this->parseName($full_name);

            // Generate random birthday from 1980s to 1 month ago
            $birthday = $this->randomBirthday();

            // Generate email
            $email = $this->generateEmail($name_parts['first_name'], $name_parts['last_name']);

            // Get random user
            $random_user = $users->random();

            // Get random created_at between user's created_at and yesterday
            $created_at = $this->randomCreatedAt($random_user->created_at);

            // Create patient
            $patient = Patient::create([
                'first_name' => $name_parts['first_name'],
                'middle_name' => $name_parts['middle_name'],
                'last_name' => $name_parts['last_name'],
                'email' => $email,
                'date_of_birth' => $birthday,
                'password' => Hash::make('password'),
                'created_by_id' => $random_user->id,
                'updated_by_id' => $random_user->id,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);

            // Attach avatar
            $avatar_path = base_path("database/avatars/venture-bros/{$avatar_filename}");
            if (file_exists($avatar_path)) {
                $patient->addMedia($avatar_path)->toMediaCollection('avatar');
            }

            $imported_count++;
            $progress_bar->advance();
        }

        $progress_bar->finish();
        $this->newLine();

        // Re-enable mass assignment protection
        Model::reguard();

        $this->info("Import complete!");
        $this->info("Imported: {$imported_count}");
        $this->info("Skipped: {$skipped_count}");

        return 0;
    }

    /**
     * Parse full name into first, middle, and last name.
     */
    private function parseName(string $full_name): array
    {
        $parts = explode(' ', $full_name);
        $first_name = $parts[0] ?? '';
        $last_name = $parts[count($parts) - 1] ?? '';
        $middle_name = null;

        if (count($parts) > 2) {
            $middle_name = implode(' ', array_slice($parts, 1, count($parts) - 2));
        }

        return [
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
        ];
    }

    /**
     * Generate random birthday from 1980-1989 to 1 month ago.
     */
    private function randomBirthday(): string
    {
        $start = Carbon::create(1980, 1, 1);
        $end = Carbon::now()->subMonth();

        $random_timestamp = mt_rand($start->timestamp, $end->timestamp);

        return Carbon::createFromTimestamp($random_timestamp)->format('Y-m-d');
    }

    /**
     * Generate email from name.
     */
    private function generateEmail(string $first_name, string $last_name): string
    {
        $base_email = strtolower($first_name . '.' . $last_name);
        $base_email = preg_replace('/[^a-z0-9.]/', '', $base_email);

        $email = $base_email . '@example.com';

        // Check if email exists and add number if needed
        $counter = 1;
        while (Patient::where('email', $email)->exists() || User::where('email', $email)->exists()) {
            $email = $base_email . $counter . '@example.com';
            $counter++;
        }

        return $email;
    }

    /**
     * Generate random created_at between user's created_at and yesterday.
     */
    private function randomCreatedAt(Carbon $user_created_at): Carbon
    {
        $start = $user_created_at;
        $end = Carbon::yesterday();

        if ($start->greaterThan($end)) {
            return $end;
        }

        $random_timestamp = mt_rand($start->timestamp, $end->timestamp);

        return Carbon::createFromTimestamp($random_timestamp);
    }
}
