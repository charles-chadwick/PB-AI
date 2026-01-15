<?php

/** @noinspection DuplicatedCode */

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

use function fake;

class PatientTableSeeder extends Seeder
{
    /**
     * Populates the patients table with Venture Brothers character data.
     *
     * This method loads character data from the vb.csv file and creates
     * patient records. Avatars are loaded from the venture-bros directory.
     */
    public function run(): void
    {
        // Get the first admin user to act as creator
        $admin_user = User::where('role', 'admin')->first();

        if (! $admin_user) {
            echo "No admin user found. Please run UserTableSeeder first.\n";
            return;
        }

        $characters = $this->getCharacters();

        if ($characters->isEmpty()) {
            echo "No characters found in CSV file.\n";
            return;
        }

        echo "\nAdding Patients from Venture Brothers\n";

        // Create the patients
        $characters->each(function ($character) use ($admin_user) {
            // Set the causer resolver
            CauserResolver::setCauser($admin_user);
            $created_at = fake()->dateTimeBetween('-5 years', '-1 year');

            $name_parts = collect(explode(' ', $character['name']));
            $first_name = str($name_parts->shift())->title();
            $last_name = str($name_parts->pop())->title();
            $middle_name = $name_parts->isNotEmpty() ? str($name_parts->implode(' '))->title() : null;

            // Generate email from name
            $email_name = str($first_name);
            if ($middle_name) {
                $email_name = $email_name->append('.')->append($middle_name);
            }
            $email_name = $email_name->append('.')->append($last_name);

            $email = str($email_name.rand(100, 999).'@venturebros.com')
                ->lower()
                ->remove([
                    ' ',
                    '\'',
                    '.',
                    '(',
                    ')',
                ]);

            // Generate a random date of birth (between 18 and 90 years old)
            $date_of_birth = fake()->dateTimeBetween('-90 years', '-18 years')->format('Y-m-d');

            $patient = Patient::factory()
                ->create([
                    'first_name' => $first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'date_of_birth' => $date_of_birth,
                    'created_by_id' => $admin_user->id,
                    'updated_by_id' => $admin_user->id,
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                ]);

            activity()
                ->performedOn($patient)
                ->useLog('Database')
                ->log('Created');

            $this->addMedia($patient, $character['avatar_filename']);
            echo '.';
        });

        echo "\n";
    }

    private function getCharacters()
    {
        $csv_path = database_path('src/vb.csv');

        if (! file_exists($csv_path)) {
            return collect();
        }

        $lines = file($csv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return collect($lines)
            ->map(function ($line) {
                // Parse CSV line (handle quoted fields)
                $fields = str_getcsv($line);

                if (count($fields) < 3) {
                    return null;
                }

                return [
                    'url' => $fields[0],
                    'name' => $fields[1],
                    'avatar_filename' => $fields[2],
                ];
            })
            ->filter()
            ->filter(function ($character) {
                // Only include characters with at least two name parts
                return count(explode(' ', $character['name'])) >= 2
                       && ! FilterData::hasBadWords($character['name']);
            })
            ->map(function ($character) {
                // Clean up name
                $character['name'] = str($character['name'])
                    ->replace([
                        'Mrs.',
                        'Mr.',
                        'Dr.',
                    ], [
                        'Missus',
                        'Mister',
                        'Doctor',
                    ])
                    ->replaceMatches('/[()]/', '');

                return $character;
            })
            ->unique('name');
    }

    private function addMedia($model, $avatar_filename): void
    {
        $avatar_path = database_path('avatars/venture-bros/'.$avatar_filename);

        if (! file_exists($avatar_path)) {
            echo "\nWarning: Avatar not found: {$avatar_path}\n";
            return;
        }

        try {
            $model->addMedia($avatar_path)
                ->preservingOriginal()
                ->toMediaCollection('avatar');
        } catch (FileDoesNotExist|FileIsTooBig $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }
}
