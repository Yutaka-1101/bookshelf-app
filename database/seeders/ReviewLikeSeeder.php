<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

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
        $review->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[1];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[3];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
        ]);

        $review = $reviews[4];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
        ]);

        $review = $reviews[7];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
        ]);

        $review = $reviews[8];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[2]->id,
            $users[4]->id,
        ]);

        $review = $reviews[10];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
            $users[4]->id,
        ]);

        $review = $reviews[12];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
        ]);

        $review = $reviews[13];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
        ]);

        $review = $reviews[15];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[3]->id,
            $users[4]->id,
        ]);

        $review = $reviews[16];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
            $users[4]->id,
        ]);

        $review = $reviews[18];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
        ]);

        $review = $reviews[20];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[2]->id,
            $users[4]->id,
        ]);

        $review = $reviews[21];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[23];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
        ]);

        $review = $reviews[24];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        $review = $reviews[25];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
        ]);

        $review = $reviews[27];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
        ]);

        $review = $reviews[28];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
            $users[2]->id,
            $users[4]->id,
        ]);

        $review = $reviews[30];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
        ]);

        $review = $reviews[31];
        $review->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[3]->id,
            $users[4]->id,
        ]);

    }
}
