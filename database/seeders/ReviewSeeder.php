<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[0]->id,
            'rating' => 5,
            'comment' => '吾輩は猫である、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[0]->id,
            'rating' => 4,
            'comment' => '吾輩は猫である、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[0]->id,
            'rating' => 3,
            'comment' => '吾輩は猫である、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[1]->id,
            'rating' => 5,
            'comment' => '人を動かす、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[1]->id,
            'rating' => 4,
            'comment' => '人を動かす、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[1]->id,
            'rating' => 3,
            'comment' => '人を動かす、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[2]->id,
            'rating' => 5,
            'comment' => 'リーダブルコード、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[2]->id,
            'rating' => 4,
            'comment' => 'リーダブルコード、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[2]->id,
            'rating' => 3,
            'comment' => 'リーダブルコード、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[3]->id,
            'rating' => 5,
            'comment' => '7つの習慣、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[3]->id,
            'rating' => 4,
            'comment' => '7つの習慣、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[3]->id,
            'rating' => 3,
            'comment' => '7つの習慣、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[4]->id,
            'rating' => 5,
            'comment' => '坊っちゃん、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[4]->id,
            'rating' => 4,
            'comment' => '坊っちゃん、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[4]->id,
            'rating' => 3,
            'comment' => '坊っちゃん、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[5]->id,
            'rating' => 5,
            'comment' => 'サピエンス全史、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[5]->id,
            'rating' => 4,
            'comment' => 'サピエンス全史、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[5]->id,
            'rating' => 3,
            'comment' => 'サピエンス全史、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[6]->id,
            'rating' => 5,
            'comment' => 'Clean Code、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[6]->id,
            'rating' => 4,
            'comment' => 'Clean Code、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[6]->id,
            'rating' => 3,
            'comment' => 'Clean Code、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[7]->id,
            'rating' => 5,
            'comment' => '嫌われる勇気、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[7]->id,
            'rating' => 4,
            'comment' => '嫌われる勇気、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[7]->id,
            'rating' => 3,
            'comment' => '嫌われる勇気、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[8]->id,
            'rating' => 5,
            'comment' => '火花、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[8]->id,
            'rating' => 4,
            'comment' => '火花、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[8]->id,
            'rating' => 3,
            'comment' => '火花、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[9]->id,
            'rating' => 5,
            'comment' => 'FACTFULNESS、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[9]->id,
            'rating' => 4,
            'comment' => 'FACTFULNESS、面白かったです'
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[9]->id,
            'rating' => 3,
            'comment' => 'FACTFULNESS、楽しめました'
        ]);

        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[10]->id,
            'rating' => 5,
            'comment' => 'コンテナ物語、とても面白かったです'
        ]);

        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[10]->id,
            'rating' => 4,
            'comment' => 'コンテナ物語、面白かったです'
        ]);
    }
}
