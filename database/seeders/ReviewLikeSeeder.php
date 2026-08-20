<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Review;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        $review = $reviews[0];
        $review->users()->syncWithoutDetaching([
            $users[1]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[1];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[3];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
        ]);

        $review = $reviews[4];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
        ]);

        $review = $reviews[7];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
        ]);

        $review = $reviews[8];
        $review->users()->syncWithoutDetaching([
            $users[2]->id,
            $users[4]->id,
        ]);

        $review = $reviews[10];
        $review->users()->syncWithoutDetaching([
            $users[1]->id,
            $users[4]->id,
        ]);

        $review = $reviews[12];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
        ]);

        $review = $reviews[13];
        $review->users()->syncWithoutDetaching([
            $users[1]->id,
        ]);

        $review = $reviews[15];
        $review->users()->syncWithoutDetaching([
            $users[3]->id,
            $users[4]->id,
        ]);

        $review = $reviews[16];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
            $users[4]->id,
        ]);

        $review = $reviews[18];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
        ]);

        $review = $reviews[20];
        $review->users()->syncWithoutDetaching([
            $users[2]->id,
            $users[4]->id,
        ]);

        $review = $reviews[21];
        $review->users()->syncWithoutDetaching([
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[23];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
        ]);

        $review = $reviews[24];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[25];
        $review->users()->syncWithoutDetaching([
            $users[1]->id,
        ]);

        $review = $reviews[27];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
        ]);

        $review = $reviews[28];
        $review->users()->syncWithoutDetaching([
            $users[1]->id,
            $users[2]->id,
            $users[4]->id,
        ]);

        $review = $reviews[30];
        $review->users()->syncWithoutDetaching([
            $users[1]->id,
        ]);

        $review = $reviews[31];
        $review->users()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
            $users[4]->id,
        ]);

    }
}