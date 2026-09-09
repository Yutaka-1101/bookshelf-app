<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $user = $users[0];
        $user->favoriteBooks()->syncWithoutDetaching([
            $books[0]->id,
            $books[1]->id,
            $books[2]->id,
        ]);

        $user = $users[1];
        $user->favoriteBooks()->syncWithoutDetaching([
            $books[3]->id,
            $books[4]->id,
            $books[5]->id,
        ]);

        $user = $users[2];
        $user->favoriteBooks()->syncWithoutDetaching([
            $books[6]->id,
            $books[7]->id,
            $books[8]->id,
        ]);

        $user = $users[3];
        $user->favoriteBooks()->syncWithoutDetaching([
            $books[9]->id,
            $books[10]->id,
            $books[0]->id,
        ]);

        $user = $users[4];
        $user->favoriteBooks()->syncWithoutDetaching([
            $books[1]->id,
            $books[2]->id,
            $books[3]->id,
        ]);
    }
}
