<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\CauserResolver;

use function fake;

class AppointmentSeeder extends Seeder
{
    private array $episode_titles = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user to act as creator
        $admin_user = User::where('role', 'admin')->first();

        if (! $admin_user) {
            echo "No admin user found. Please run UserTableSeeder first.\n";
            return;
        }

        // Load episode titles from CSV files
        $this->loadEpisodeTitles();

        if (empty($this->episode_titles)) {
            echo "No episode titles found in CSV files.\n";
            return;
        }

        // Get all patients and users
        $patients = Patient::all();
        $users = User::all();

        if ($patients->isEmpty()) {
            echo "No patients found. Please run PatientTableSeeder first.\n";
            return;
        }

        if ($users->isEmpty()) {
            echo "No users found. Please run UserTableSeeder first.\n";
            return;
        }

        echo "\nCreating appointments for patients\n";

        $appointment_count = 0;

        // Create appointments for each patient
        $patients->each(function ($patient) use ($admin_user, $users, &$appointment_count) {
            CauserResolver::setCauser($admin_user);

            // Create 3-8 appointments per patient
            $num_appointments = rand(3, 8);

            for ($i = 0; $i < $num_appointments; $i++) {
                // Get random appointment date between patient creation and 1 year from now
                $start_date = Carbon::parse($patient->created_at);
                $end_date = Carbon::now()->addYear();

                $appointment_date = $this->getRandomWeekday($start_date, $end_date);

                // Get random time between 8am and 4pm
                $start_hour = rand(8, 15); // 8am to 3pm (so end time can be up to 4pm)
                $start_minute = rand(0, 3) * 15; // 0, 15, 30, or 45

                $start_time = Carbon::parse($appointment_date)
                    ->setTime($start_hour, $start_minute);

                // End time is 30-120 minutes after start
                $duration_minutes = rand(2, 8) * 15; // 30, 45, 60, 75, 90, 105, or 120 minutes
                $end_time = $start_time->copy()->addMinutes($duration_minutes);

                // Make sure end time doesn't go past 4pm
                if ($end_time->hour >= 16) {
                    $end_time->setTime(16, 0);
                }

                // Get random title and type
                $title = $this->episode_titles[array_rand($this->episode_titles)];
                $type = AppointmentType::cases()[array_rand(AppointmentType::cases())]->value;

                // Get random status (70% scheduled, 20% completed, 5% cancelled, 5% no_show)
                $status = $this->getRandomStatus($appointment_date);

                // Create appointment
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'title' => $title,
                    'type' => $type,
                    'description' => fake()->optional(0.6)->paragraph(),
                    'appointment_date' => $appointment_date->format('Y-m-d'),
                    'start_time' => $start_time->format('H:i'),
                    'end_time' => $end_time->format('H:i'),
                    'status' => $status,
                    'created_by_id' => $admin_user->id,
                    'updated_by_id' => $admin_user->id,
                ]);

                // Assign 1-3 random users to the appointment
                $num_users = rand(1, min(3, $users->count()));
                $assigned_users = $users->random($num_users)->pluck('id');
                $appointment->users()->attach($assigned_users);

                activity()
                    ->performedOn($appointment)
                    ->useLog('Database')
                    ->log('Created');

                $appointment_count++;
                echo '.';
            }
        });

        echo "\nCreated {$appointment_count} appointments\n";
    }

    /**
     * Load episode titles from CSV files.
     */
    private function loadEpisodeTitles(): void
    {
        // Load Simpsons episodes
        $simpsons_path = database_path('src/simpsons-episodes.csv');
        if (file_exists($simpsons_path)) {
            $lines = file($simpsons_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $fields = str_getcsv($line);
                if (count($fields) >= 1) {
                    $this->episode_titles[] = trim($fields[0], '"');
                }
            }
        }

        // Load Venture Bros episodes
        $vb_path = database_path('src/venture_bros_episodes.csv');
        if (file_exists($vb_path)) {
            $lines = file($vb_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $is_first_line = true;
            foreach ($lines as $line) {
                // Skip header row
                if ($is_first_line) {
                    $is_first_line = false;
                    continue;
                }

                $fields = str_getcsv($line);
                if (count($fields) >= 3) {
                    $this->episode_titles[] = $fields[2]; // Title is in 3rd column
                }
            }
        }
    }

    /**
     * Get a random weekday date between start and end dates.
     */
    private function getRandomWeekday(Carbon $start_date, Carbon $end_date): Carbon
    {
        $max_attempts = 100;
        $attempts = 0;

        do {
            $random_date = Carbon::parse(fake()->dateTimeBetween($start_date, $end_date));
            $attempts++;

            // Check if it's a weekday (Monday = 1, Friday = 5)
            if ($random_date->dayOfWeek >= 1 && $random_date->dayOfWeek <= 5) {
                return $random_date;
            }
        } while ($attempts < $max_attempts);

        // If we couldn't find a weekday, just force it to be Monday
        return $random_date->next(Carbon::MONDAY);
    }

    /**
     * Get random status based on appointment date.
     * Past appointments are more likely to be completed or cancelled.
     * Future appointments are always scheduled.
     */
    private function getRandomStatus(Carbon $appointment_date): AppointmentStatus
    {
        $now = Carbon::now();

        // Future appointments are always scheduled
        if ($appointment_date->isFuture()) {
            return AppointmentStatus::Scheduled;
        }

        // Past appointments
        $rand = rand(1, 100);

        if ($rand <= 70) {
            return AppointmentStatus::Completed;
        } elseif ($rand <= 85) {
            return AppointmentStatus::Cancelled;
        } elseif ($rand <= 95) {
            return AppointmentStatus::NoShow;
        } else {
            return AppointmentStatus::Scheduled;
        }
    }
}
