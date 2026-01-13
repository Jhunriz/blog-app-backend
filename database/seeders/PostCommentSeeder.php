<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class PostCommentSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            Post::factory(rand(1, 3))->create([
                'user_id' => $user->id
            ])->each(function ($post) {
                Comment::factory(rand(0, 5))->create([
                    'post_id' => $post->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                ]);
            });
        });
    }
}
