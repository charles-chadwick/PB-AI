<?php

/** @noinspection DuplicatedCode */

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

use function fake;

class UserTableSeeder extends Seeder
{
    /**
     * Populates the users table with Rick and Morty character data.
     *
     * This method loads character data from a local JSON cache file and creates
     * user records for characters with names containing at least two words.
     * Avatars are loaded from local storage.
     */
    public function run(): void
    {
        $admin_user = User::factory()
            ->create([
                'role' => UserRole::Admin,
                'first_name' => 'Doofus',
                'last_name' => 'Rick',
                'email' => 'doofus.rick@example.com',
                'created_at' => '2020-01-01 00:00:00',
            ]);

        $admin_user->assignRole('Admin');

        $this->addMedia($admin_user, 'https://rickandmortyapi.com/api/character/avatar/103.jpeg');

        $characters = $this->getCharacters();

        // split into two arrays
        $users = $characters->slice(0, 25);
        $characters->slice(25, 100);

        echo "\nAdding Users\n";

        // create the users
        $users->each(function ($character, $index) use ($admin_user) {
            // generate the user and set the causer resolver
            CauserResolver::setCauser($admin_user);
            $created_at = fake()->dateTimeBetween($admin_user->created_at, '-1 year');

            $name_parts = collect(explode(' ', $character['name']));
            $first_name = str($name_parts->shift())->title();
            $last_name = str($name_parts->pop())->title();
            $middle_name = $name_parts->isNotEmpty() ? str($name_parts->implode(' '))->title() : null;

            $role = match (true) {
                $index <= 3 => UserRole::Admin,
                $index <= 7 => UserRole::Doctor,
                $index <= 10 => UserRole::Nurse,
                default => UserRole::FrontDesk,
            };

            $role_name = match ($role) {
                UserRole::Admin => 'Admin',
                UserRole::Doctor => 'Doctor',
                UserRole::Nurse => 'Nurse',
                default => 'Front Desk',
            };

            $staff_user = User::factory()
                ->create([
                    'role' => $role,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => str($first_name.'.'.$last_name.rand(100, 999).'@example.com')
                        ->lower()
                        ->remove([
                            ' ',
                            '\'',
                        ]),
                    'created_by_id' => $admin_user,
                    'updated_by_id' => $admin_user,
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                ]);

            $staff_user->assignRole($role_name);

            activity()
                ->performedOn($staff_user)
                ->useLog('Database')
                ->log('Created');

            $this->addMedia($staff_user, $character['image']);
            echo '.';
        });

        echo "\n";
    }

    private function getCharacters(): ?Collection
    {
        $character_json_path = database_path('src/characters.json');

        if (! file_exists($character_json_path)) {
            return null;
        }

        $data = collect(json_decode(file_get_contents($character_json_path), true));

        return $data
            ->filter(function ($character) {
                return count(explode(' ', $character['name'])) >= 2
                       && ! FilterData::hasBadWords($character['name']);
            })
            ->map(function ($character) {
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
                    ->replaceMatches('/[.()]/', '');

                return $character;
            })
            ->unique('name')
            ->shuffle();
    }

    private function addMedia($model, $image): void
    {
        $avatar_path = database_path('avatars/rick-and-morty/'.md5($image).'.jpeg');

        if (! file_exists($avatar_path)) {
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
