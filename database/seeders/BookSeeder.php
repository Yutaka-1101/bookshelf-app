<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $book = Book::firstOrCreate(
            ['isbn' => '9784101010014'],
            [
                'user_id' => $user->id,
                'author' => '夏目漱石',
                'title' => '吾輩は猫である',
                'published_at' => '1905-01-01',
                'description' => '吾輩は猫であるの書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=1',
            ]
        );
        $genre = Genre::where('name', '小説')->first();
        $book->genres()->sync([$genre->id]);

        $book = Book::firstOrCreate(
            ['isbn' => '9784422100524'],
            [
                'user_id' => $user->id,
                'author' => 'D・カーネギー',
                'title' => '人を動かす',
                'published_at' => '1936-10-01',
                'description' => '人を動かすの書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=2',
            ]
        );
        $genre = Genre::whereIn('name', ['ビジネス', '自己啓発'])->get();
        $book->genres()->sync($genre->pluck('id'));

        $book = Book::firstOrCreate(
            ['isbn' => '9784873115658'],
            [
                'user_id' => $user->id,
                'author' => 'Dustin Boswell',
                'title' => 'リーダブルコード',
                'published_at' => '2012-06-23',
                'description' => 'リーダブルコードの書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=3',
            ]
        );
        $genre = Genre::where('name', '技術書')->first();
        $book->genres()->sync([$genre->id]);

        $book = Book::firstOrCreate(
            ['isbn' => '9784863940246'],
            [
                'user_id' => $user->id,
                'author' => 'スティーブン・R・コヴィー',
                'title' => '7つの習慣',
                'published_at' => '2013-08-30',
                'description' => '7つの習慣の書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=4',
            ]
        );
        $genre = Genre::whereIn('name', ['ビジネス', '自己啓発'])->get();
        $book->genres()->sync($genre->pluck('id'));

        $book = Book::firstOrCreate(
            ['isbn' => '9784101010021'],
            [
                'user_id' => $user->id,
                'author' => '夏目漱石',
                'title' => '坊っちゃん',
                'published_at' => '1906-04-01',
                'description' => '坊っちゃんの書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=5',
            ]
        );
        $genre = Genre::where('name', '小説')->first();
        $book->genres()->sync([$genre->id]);

        $book = Book::firstOrCreate(
            ['isbn' => '9784309226712'],
            [
                'user_id' => $user->id,
                'author' => 'ユヴァル・ノア・ハラリ',
                'title' => 'サピエンス全史',
                'published_at' => '2016-09-08',
                'description' => 'サピエンス全史の書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=6',
            ]
        );
        $genre = Genre::whereIn('name', ['歴史', '科学'])->get();
        $book->genres()->sync($genre->pluck('id'));

        $book = Book::firstOrCreate(
            ['isbn' => '9784048930598'],
            [
                'user_id' => $user->id,
                'author' => 'Robert C. Martin',
                'title' => 'Clean Code',
                'published_at' => '2017-12-18',
                'description' => 'Clean Codeの書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=7',
            ]
        );
        $genre = Genre::where('name', '技術書')->first();
        $book->genres()->sync([$genre->id]);

        $book = Book::firstOrCreate(
            ['isbn' => '9784478025819'],
            [
                'user_id' => $user->id,
                'author' => '岸見一郎・古賀史健',
                'title' => '嫌われる勇気',
                'published_at' => '2013-12-13',
                'description' => '嫌われる勇気の書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=8',
            ]
        );
        $genre = Genre::where('name', '自己啓発')->first();
        $book->genres()->sync([$genre->id]);

        $book = Book::firstOrCreate(
            ['isbn' => '9784163902302'],
            [
                'user_id' => $user->id,
                'author' => '又吉直樹',
                'title' => '火花',
                'published_at' => '2015-03-11',
                'description' => '火花の書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=9',
            ]
        );
        $genre = Genre::where('name', '小説')->first();
        $book->genres()->sync([$genre->id]);

        $book = Book::firstOrCreate(
            ['isbn' => '9784822289607'],
            [
                'user_id' => $user->id,
                'author' => 'ハンス・ロスリング',
                'title' => 'FACTFULNESS',
                'published_at' => '2019-01-11',
                'description' => 'FACTFULNESSの書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=10',
            ]
        );
        $genre = Genre::whereIn('name', ['ビジネス', '科学'])->get();
        $book->genres()->sync($genre->pluck('id'));

        $book = Book::firstOrCreate(
            ['isbn' => '9784822251468'],
            [
                'user_id' => $user->id,
                'author' => 'マルク・レビンソン',
                'title' => 'コンテナ物語',
                'published_at' => '2007-01-18',
                'description' => 'コンテナ物語の書籍説明です',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=11',
            ]
        );
        $genre = Genre::whereIn('name', ['ビジネス', '歴史'])->get();
        $book->genres()->sync($genre->pluck('id'));
    }
}
