<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'rooted-generation'],
            [
                'title' => 'Rooted Generation',
                'template' => 'rooted_generation',
                'excerpt' => 'Fresh Fountain’s youth ministry — rooted in Christ, grounded in faith, growing in purpose and fruitful in our world.',
                'seo_title' => 'Rooted Generation | Fresh Fountain Youth Ministry',
                'seo_description' => 'Rooted Generation helps young people build a lasting relationship with Christ, discover identity and purpose, grow as leaders and find a welcoming Christian community.',
                'is_published' => true,
                'sections' => [
                    'rooted' => [
                        'kicker' => 'Fresh Fountain Youth Ministry',
                        'headline' => 'Rooted. Grounded. Growing. Fruitful.',
                        'intro' => 'A Christ-centred community where young people can belong, grow in faith, discover purpose and confidently influence their world for Christ.',
                        'connect_title' => 'Rooted Generation Connect',
                        'connect_schedule' => 'Every second Sunday of the month',
                        'connect_time' => '2:00 PM',
                        'connect_location' => 'Fresh Fountain Centre, 7 Gregory Boulevard, NG7 6LB, Nottingham',
                        'summit_title' => 'Thrive Summit',
                        'summit_date' => 'Sunday, 11 October 2026',
                        'summit_time' => '2:00 PM',
                        'summit_location' => 'Fresh Fountain Centre, 7 Gregory Boulevard, Sherwood House, NG7 6LB',
                        'summit_intro' => 'A practical and welcoming summit helping international students and young people navigate life in the UK with greater confidence, useful resources and a community to lean on.',
                    ],
                ],
            ]
        );
    }

    public function down(): void
    {
        Page::query()->where('slug', 'rooted-generation')->where('template', 'rooted_generation')->delete();
    }
};
