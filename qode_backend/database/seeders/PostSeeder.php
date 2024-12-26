<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Services\ElasticsearchService;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(1)->create();
        $batchSize = 1000;
        $totalPosts = 200000;
        $faker = Faker::create();
        $id = 0;
        for ($i = 0; $i < $totalPosts / $batchSize; $i++) {
            $posts = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $posts[] = [
                    'id' => ++$id,
                    'title' => $faker->sentence(),
                    'excerpt' => $faker->text(100),
                    'description' => $faker->paragraph(3, true),
                    'image' => '',
                    'keywords' => implode(',', $faker->words(5)),
                    'meta_title' => $faker->sentence,
                    'meta_description' => $faker->text(150),
                    'published_at' => now(),
                    'author_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Post::insert($posts);
            app(ElasticsearchService::class)->bulkIndex('posts' , $posts);
            echo "Inserted and indexed " . (($i + 1) * $batchSize) . " posts...\n";
        }
    }
}
