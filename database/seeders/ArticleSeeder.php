<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Media;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Article::factory(15)->has(Media::factory(4))
            ->create();
    }
}
