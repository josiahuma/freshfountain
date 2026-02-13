<?php
// gimscare-upgrade/app/Filament/Resources/Pages/Schemas/PageForm.php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // =========================================================
                // HEADER
                // =========================================================
                Section::make('Header')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('sections.header.logo')
                            ->label('Header Logo')
                            ->disk('public')
                            ->directory('site/header')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state),

                        TextInput::make('sections.header.site_name')
                            ->label('Site Name (fallback if no logo)')
                            ->default('Gims Care Solutions'),
                    ]),

                // =========================================================
                // PAGE DETAILS
                // =========================================================
                Section::make('Page Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('template')
                            ->options([
                                'home' => 'Homepage (Hero + Sections)',
                                'service' => 'Service Page',
                                'services_index' => 'Services Listing Page',
                                'about' => 'About Page',
                                'leaders' => 'Leaders Page',
                                'contact' => 'Contact Page',
                                'jobs' => 'Job Portal Page',
                                'blog' => 'Blog Page',
                                'course' => 'Course Page',
                                'courses_index' => 'Courses Listing Page',
                                'units' => 'Units Page',
                                'units_index' => 'Units Listing Page',
                            ])
                            ->default('service')
                            ->live(),

                        Toggle::make('is_published')
                            ->default(true),
                    ]),

                    // =========================================================
                    // ABOUT PAGE CONTENT
                    // =========================================================
                    Section::make('Content (About Page)')
                        ->visible(fn ($get) => $get('template') === 'about')
                        ->columns(2)
                        ->schema([

                            FileUpload::make('banner_image')
                                ->label('Page Banner Image (optional)')
                                ->disk('public')
                                ->directory('pages/banners')
                                ->visibility('public')
                                ->image()
                                ->imageEditor()
                                ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                ->columnSpanFull(),

                            Textarea::make('excerpt')
                                ->label('Short Intro (optional)')
                                ->rows(3)
                                ->helperText('Shows under the page title on the banner.')
                                ->columnSpanFull(),

                            // Mission
                            Textarea::make('sections.about.mission')
                                ->label('Our Mission')
                                ->rows(5)
                                ->required()
                                ->columnSpanFull(),

                            // Vision
                            Textarea::make('sections.about.vision')
                                ->label('Our Vision')
                                ->rows(5)
                                ->required()
                                ->columnSpanFull(),

                            // Verse (optional)
                            Textarea::make('sections.about.verse')
                                ->label('Scripture / Quote (optional)')
                                ->rows(3)
                                ->placeholder("But you, dear friends, must build each other up...\n— Jude 1:20")
                                ->columnSpanFull(),

                            // Core values list
                            Repeater::make('sections.about.core_values')
                                ->label('Core Values')
                                ->defaultItems(6)
                                ->schema([
                                    TextInput::make('value')
                                        ->label('Value')
                                        ->required(),
                                ])
                                ->columnSpanFull(),
                        ]),

                        // =========================================================
                        // LEADERS PAGE CONTENT
                        // =========================================================
                        Section::make('Content (Leaders Page)')
                            ->visible(fn ($get) => $get('template') === 'leaders')
                            ->columns(2)
                            ->schema([
                                FileUpload::make('banner_image')
                                    ->label('Page Banner Image (optional)')
                                    ->disk('public')
                                    ->directory('pages/banners')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                    ->columnSpanFull(),

                                Textarea::make('excerpt')
                                    ->label('Short Intro (optional)')
                                    ->rows(3)
                                    ->helperText('This shows under the page title on the banner.')
                                    ->columnSpanFull(),

                                // -----------------------------
                                // Pastors
                                // -----------------------------
                                Section::make('Pastors')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('sections.leaders.pastors')
                                            ->label('Pastors')
                                            ->default([])
                                            ->reorderable()
                                            ->collapsed()
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Photo')
                                                    ->disk('public')
                                                    ->directory('pages/leaders')
                                                    ->visibility('public')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                                    ->required(),

                                                TextInput::make('name')->required()->label('Name'),
                                                TextInput::make('position')->required()->label('Position / Title')->default('Pastor'),
                                                TextInput::make('unit')->label('Unit (optional)')->placeholder('e.g. Senior Pastor / Lead Pastor'),
                                            ])
                                            ->columns(2),
                                    ]),

                                // -----------------------------
                                // Trustees
                                // -----------------------------
                                Section::make('Trustees')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('sections.leaders.trustees')
                                            ->label('Trustees')
                                            ->default([])
                                            ->reorderable()
                                            ->collapsed()
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Photo')
                                                    ->disk('public')
                                                    ->directory('pages/leaders')
                                                    ->visibility('public')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                                    ->required(),

                                                TextInput::make('name')->required()->label('Name'),
                                                TextInput::make('position')->required()->label('Position / Title')->default('Trustee'),
                                                TextInput::make('unit')->label('Unit (optional)')->placeholder('e.g. Board of Trustees'),
                                            ])
                                            ->columns(2),
                                    ]),

                                // -----------------------------
                                // Ministers
                                // -----------------------------
                                Section::make('Ministers')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('sections.leaders.ministers')
                                            ->label('Ministers')
                                            ->default([])
                                            ->reorderable()
                                            ->collapsed()
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Photo')
                                                    ->disk('public')
                                                    ->directory('pages/leaders')
                                                    ->visibility('public')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                                    ->required(),

                                                TextInput::make('name')->required()->label('Name'),
                                                TextInput::make('position')->required()->label('Position / Title')->default('Minister'),
                                                TextInput::make('unit')->label('Unit (optional)')->placeholder('e.g. Worship / Prayer / Media'),
                                            ])
                                            ->columns(2),
                                    ]),

                                // -----------------------------
                                // Unit Heads
                                // -----------------------------
                                Section::make('Unit Heads')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('sections.leaders.unit_heads')
                                            ->label('Unit Heads')
                                            ->default([])
                                            ->reorderable()
                                            ->collapsed()
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Photo')
                                                    ->disk('public')
                                                    ->directory('pages/leaders')
                                                    ->visibility('public')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                                    ->required(),

                                                TextInput::make('name')->required()->label('Name'),
                                                TextInput::make('unit')->required()->label('Unit Led')->placeholder('e.g. Choir / Protocol / Media'),
                                                TextInput::make('position')->label('Position / Title')->default('Unit Head'),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),

                    // =========================================================
                    // COURSE PAGE CONTENT
                    // =========================================================
                    Section::make('Content (Course Page)')
                        ->visible(fn ($get) => $get('template') === 'course')
                        ->columns(2)
                        ->schema([
                            FileUpload::make('banner_image')
                                ->label('Course Banner Image (used on course page + courses index tile)')
                                ->disk('public')
                                ->directory('pages/courses/banners')
                                ->visibility('public')
                                ->image()
                                ->imageEditor()
                                ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                ->columnSpanFull(),

                            Textarea::make('excerpt')
                                ->label('Short Intro (optional)')
                                ->rows(3)
                                ->helperText('Shows under the headline on the course page and card.'),

                            TextInput::make('sections.course.kicker')
                                ->label('Kicker (small top text)')
                                ->default('COURSE'),

                            TextInput::make('sections.course.headline')
                                ->label('Headline')
                                ->default('Grow in faith and understanding')
                                ->columnSpanFull(),

                            Textarea::make('sections.course.body')
                                ->label('Body text')
                                ->rows(4)
                                ->columnSpanFull(),

                            TextInput::make('sections.course.button_text')
                                ->label('Button text')
                                ->default('Take Course'),

                            TextInput::make('sections.course.button_link')
                                ->label('Button link (external)')
                                ->default('https://courses.freshfountain.org')
                                ->url()
                                ->columnSpanFull(),
                        ]),


                    // =========================================================
                    // UNIT PAGE CONTENT
                    // =========================================================
                    Section::make('Content (Unit Page)')
                        ->visible(fn ($get) => $get('template') === 'units')
                        ->columns(2)
                        ->schema([
                            FileUpload::make('banner_image')
                                ->label('Unit Banner Image (used on unit page + units index tile)')
                                ->disk('public')
                                ->directory('pages/units/banners')
                                ->visibility('public')
                                ->image()
                                ->imageEditor()
                                ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                ->columnSpanFull(),

                            Textarea::make('excerpt')
                                ->label('Short Intro (optional)')
                                ->rows(3)
                                ->helperText('Shows under the headline on the unit page and card.'),

                            TextInput::make('sections.units.kicker')
                                ->label('Kicker (small top text)')
                                ->default('UNITS & TEAMS'),

                            TextInput::make('sections.units.headline')
                                ->label('Headline')
                                ->default('Grow in faith and understanding')
                                ->columnSpanFull(),

                            Textarea::make('sections.units.body')
                                ->label('Body text')
                                ->rows(4)
                                ->columnSpanFull(),

                            TextInput::make('sections.units.button_text')
                                ->label('Button text')
                                ->default('Join Unit'),

                            TextInput::make('sections.units.button_link')
                                ->label('Button link (external)')
                                ->default('https://courses.freshfountain.org')
                                ->url()
                                ->columnSpanFull(),
                        ]),

                // =========================================================
                // SERVICE PAGE CONTENT
                // =========================================================
                Section::make('Content (Service Pages)')
                    ->visible(fn ($get) => $get('template') === 'service')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('banner_image')
                            ->label('Page Banner Image (optional)')
                            ->disk('public')
                            ->directory('pages/banners')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Short Intro (optional)')
                            ->rows(3)
                            ->helperText('This shows under the page title on the banner.'),

                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // =========================================================
                // CONTACT PAGE CONTENT
                // =========================================================
                Section::make('Content (Contact Page)')
                    ->visible(fn ($get) => $get('template') === 'contact')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('banner_image')
                            ->label('Page Banner Image (optional)')
                            ->disk('public')
                            ->directory('pages/banners')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Short Intro (optional)')
                            ->rows(3)
                            ->helperText('This shows under the page title on the banner.'),

                        Textarea::make('sections.contact.address')
                            ->label('Head Office Address')
                            ->rows(4)
                            ->columnSpanFull(),

                        TextInput::make('sections.contact.phone_primary')
                            ->label('Phone (Primary)'),

                        TextInput::make('sections.contact.phone_secondary')
                            ->label('Phone (Secondary)'),

                        TextInput::make('sections.contact.email')
                            ->label('Email'),

                        TextInput::make('sections.contact.form_title')
                            ->label('Form Title')
                            ->default('Send us a message')
                            ->columnSpanFull(),

                        Textarea::make('sections.contact.map_embed')
                            ->label('Google Map Embed (iframe HTML)')
                            ->rows(6)
                            ->helperText('Paste the full iframe embed code from Google Maps.')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Optional Body Content (below form)')
                            ->columnSpanFull(),
                    ]),

                // =========================================================
                // JOB PORTAL PAGE CONTENT (Recruitment instructions page)
                // =========================================================
                Section::make('Content (Job Portal Page)')
                    ->visible(fn ($get) => $get('template') === 'jobs')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('banner_image')
                            ->label('Page Banner Image (optional)')
                            ->disk('public')
                            ->directory('pages/banners')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Short Intro (optional)')
                            ->rows(3)
                            ->helperText('This shows under the page title on the banner.'),

                        RichEditor::make('content')
                            ->label('Intro Content (optional)')
                            ->columnSpanFull(),

                        // ✅ IMPORTANT:
                        // We REMOVED the "Job Listings" repeater from here.
                        // Job vacancies must come from JobListingResource (job_listings table),
                        // not from a static repeater stored inside the Page.
                    ]),

                // =========================================================
                // BLOG PAGE CONTENT
                // =========================================================
                Section::make('Content (Blog Page)')
                    ->visible(fn ($get) => $get('template') === 'blog')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('banner_image')
                            ->label('Page Banner Image (optional)')
                            ->disk('public')
                            ->directory('pages/banners')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Short Intro (optional)')
                            ->rows(3)
                            ->helperText('This shows under the page title on the banner.'),

                        Repeater::make('sections.blog_posts.items')
                            ->label('Blog Posts')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Featured Image')
                                    ->disk('public')
                                    ->directory('pages/blog')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state),

                                TextInput::make('title')
                                    ->required()
                                    ->label('Post Title'),

                                TextInput::make('date')
                                    ->label('Date')
                                    ->placeholder('May 27, 2026'),

                                Textarea::make('excerpt')
                                    ->label('Post Excerpt')
                                    ->rows(3),

                                TextInput::make('link')
                                    ->label('Link')
                                    ->placeholder('/blog/my-post-slug'),
                            ])
                            ->columns(2),
                    ]),

                // =========================================================
                // HOMEPAGE BUILDER
                // =========================================================
                Section::make('Homepage Builder')
                    ->visible(fn ($get) => $get('template') === 'home')
                    ->schema([

                        // -----------------------------------------------------
                        // HERO
                        // -----------------------------------------------------
                        Section::make('Hero')
                            ->columns(2)
                            ->schema([
                                Select::make('sections.hero.type')
                                    ->label('Hero Type')
                                    ->options([
                                        'image' => 'Single Image',
                                        'video' => 'Background Video',
                                        'slider' => 'Image Slider (3 slides)',
                                    ])
                                    ->default('image')
                                    ->live(),

                                TextInput::make('sections.hero.kicker')
                                    ->label('Small Top Text (optional)')
                                    ->placeholder('Welcome to'),

                                TextInput::make('sections.hero.title')
                                    ->label('Hero Title')
                                    ->required(),

                                Textarea::make('sections.hero.subtitle')
                                    ->label('Hero Subtitle')
                                    ->rows(3),

                                TextInput::make('sections.hero.primary_button_text')
                                    ->label('Primary Button Text')
                                    ->default('Our Services'),

                                TextInput::make('sections.hero.primary_button_link')
                                    ->label('Primary Button Link')
                                    ->default('/services'),

                                TextInput::make('sections.hero.secondary_button_text')
                                    ->label('Secondary Button Text')
                                    ->default('Contact Us'),

                                TextInput::make('sections.hero.secondary_button_link')
                                    ->label('Secondary Button Link')
                                    ->default('/contact'),

                                FileUpload::make('sections.hero.background_image')
                                    ->label('Hero Background Image')
                                    ->disk('public')
                                    ->directory('pages/home')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                    ->visible(fn ($get) => data_get($get('sections'), 'hero.type') === 'image'),

                                FileUpload::make('sections.hero.video_file')
                                    ->label('Hero Video (MP4 upload)')
                                    ->disk('public')
                                    ->directory('pages/home/videos')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['video/mp4'])
                                    ->maxSize(102400)
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                    ->visible(fn ($get) => data_get($get('sections'), 'hero.type') === 'video'),

                                TextInput::make('sections.hero.video_url')
                                    ->label('Hero Video URL (YouTube/Vimeo) - optional')
                                    ->helperText('If you upload MP4, you can leave this empty.')
                                    ->visible(fn ($get) => data_get($get('sections'), 'hero.type') === 'video'),

                                Repeater::make('sections.hero.slides')
                                    ->label('Hero Slides (exactly 3)')
                                    ->defaultItems(3)
                                    ->minItems(3)
                                    ->maxItems(3)
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Slide Image')
                                            ->disk('public')
                                            ->directory('pages/home/slides')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                            ->required(),

                                        TextInput::make('heading')
                                            ->label('Slide Heading')
                                            ->required(),

                                        Textarea::make('text')
                                            ->label('Slide Text')
                                            ->rows(2),

                                        TextInput::make('button_text')
                                            ->label('Button Text')
                                            ->default('Learn more'),

                                        TextInput::make('button_link')
                                            ->label('Button Link')
                                            ->default('/services'),
                                    ])
                                    ->visible(fn ($get) => data_get($get('sections'), 'hero.type') === 'slider'),
                            ]),

                        // -----------------------------------------------------
                        // ABOUT
                        // -----------------------------------------------------
                        Section::make('About Section')
                            ->columns(2)
                            ->schema([
                                TextInput::make('sections.about.kicker')
                                    ->label('Small Top Text')
                                    ->default('About Us'),

                                TextInput::make('sections.about.title')
                                    ->label('Title')
                                    ->default('Exceptional Service')
                                    ->required(),

                                RichEditor::make('sections.about.body')
                                    ->label('Body Text')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'link',
                                        'h2',
                                        'h3',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),

                                FileUpload::make('sections.about.image')
                                    ->label('About Image')
                                    ->disk('public')
                                    ->directory('pages/home/about')
                                    ->visibility('public')
                                    ->image()
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                    ->imageEditor(),

                                TextInput::make('sections.about.button_text')
                                    ->label('Button Text')
                                    ->default('Learn more'),

                                TextInput::make('sections.about.button_link')
                                    ->label('Button Link')
                                    ->default('/about'),
                            ]),

                        // -----------------------------------------------------
                        // SERVICES
                        // -----------------------------------------------------
                        Section::make('Services Section')
                            ->schema([
                                TextInput::make('sections.services.title')
                                    ->label('Section Title')
                                    ->default('Our Services'),

                                Repeater::make('sections.services.items')
                                    ->label('Service Tiles')
                                    ->defaultItems(6)
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Service Image')
                                            ->disk('public')
                                            ->directory('pages/home/services')
                                            ->visibility('public')
                                            ->image()
                                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                            ->imageEditor(),

                                        TextInput::make('title')->required(),
                                        Textarea::make('description')->rows(2),
                                        TextInput::make('link')->default('/services'),
                                    ])
                                    ->columns(2),
                            ]),

                        // -----------------------------------------------------
                        // PRACTICES
                        // -----------------------------------------------------
                        Section::make('Best Practices (Bullets + Video)')
                            ->columns(2)
                            ->schema([
                                TextInput::make('sections.practices.title')
                                    ->label('Title')
                                    ->default('We Follow Best Practices')
                                    ->required(),

                                Repeater::make('sections.practices.bullets')
                                    ->label('Bullet Points')
                                    ->defaultItems(4)
                                    ->schema([
                                        TextInput::make('value')
                                            ->label('Bullet')
                                            ->required(),
                                    ])
                                    ->afterStateHydrated(function ($state, $set) {
                                        if (is_array($state) && isset($state[0]) && is_string($state[0])) {
                                            $set('sections.practices.bullets', collect($state)->map(fn ($v) => ['value' => $v])->all());
                                        }
                                    })
                                    ->dehydrateStateUsing(function ($state) {
                                        if (! is_array($state)) return [];

                                        if (isset($state[0]) && is_string($state[0])) {
                                            return array_values(array_filter($state));
                                        }

                                        return collect($state)
                                            ->pluck('value')
                                            ->filter()
                                            ->values()
                                            ->all();
                                    }),

                                TextInput::make('sections.practices.button_text')
                                    ->label('Button Text')
                                    ->default('Get in touch'),

                                TextInput::make('sections.practices.button_link')
                                    ->label('Button Link')
                                    ->default('/contact'),

                                FileUpload::make('sections.practices.video_file')
                                    ->label('Video File (MP4) - optional')
                                    ->disk('public')
                                    ->directory('pages/home/practices')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['video/mp4'])
                                    ->maxSize(102400)
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state),

                                TextInput::make('sections.practices.video_url')
                                    ->label('Video URL (YouTube/Vimeo) - optional')
                                    ->helperText('If you upload MP4, you can leave this empty.'),
                            ]),

                        // -----------------------------------------------------
                        // FLIP CARDS
                        // -----------------------------------------------------
                        Section::make('Flip Cards (3 cards)')
                            ->schema([
                                Repeater::make('sections.cards.items')
                                    ->label('Cards')
                                    ->defaultItems(3)
                                    ->minItems(3)
                                    ->maxItems(3)
                                    ->schema([
                                        TextInput::make('title')->required()->default('Why Choose Us'),
                                        TextInput::make('subtitle')->default('Click To Learn More'),

                                        FileUpload::make('image')
                                            ->label('Front Image (nurse / icon)')
                                            ->disk('public')
                                            ->directory('pages/home/cards')
                                            ->visibility('public')
                                            ->image()
                                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                            ->imageEditor(),

                                        TextInput::make('front_bg')
                                            ->label('Front Background Color (hex)')
                                            ->default('#ef4444')
                                            ->helperText('Example: #ef4444'),

                                        TextInput::make('back_title')->label('Back Title')->default('More Info'),
                                        Textarea::make('back_text')->label('Back Text')->rows(5),

                                        TextInput::make('link')->label('Link (optional)')->default('/about'),
                                        TextInput::make('link_text')->label('Link Text')->default('Learn more'),
                                    ])
                                    ->columns(2),
                            ]),

                        // -----------------------------------------------------
                        // REVIEWS
                        // -----------------------------------------------------
                        Section::make('Reviews / Testimonials')
                            ->schema([
                                TextInput::make('sections.reviews.kicker')
                                    ->label('Small Top Text')
                                    ->default('Testimonials'),

                                TextInput::make('sections.reviews.title')
                                    ->label('Title')
                                    ->default('What people say about us'),

                                Textarea::make('sections.reviews.embed_html')
                                    ->label('Widget Embed HTML')
                                    ->rows(10)
                                    ->helperText('Paste homecare.co.uk / Trustpilot widget embed code here.'),
                            ]),

                        // -----------------------------------------------------
                        // BLOG SECTION SETTINGS
                        // -----------------------------------------------------
                        Section::make('Blog Section (Cards pulled from Blog Page)')
                            ->columns(2)
                            ->schema([
                                TextInput::make('sections.blog.title')
                                    ->label('Section Title')
                                    ->default('News & Updates'),

                                TextInput::make('sections.blog.button_text')
                                    ->label('Button Text (optional)')
                                    ->default('View all posts'),

                                TextInput::make('sections.blog.button_link')
                                    ->label('Button Link (optional)')
                                    ->default('/blog'),
                            ]),

                        // -----------------------------------------------------
                        // AFFILIATION LOGOS
                        // -----------------------------------------------------
                        Section::make('Affiliation Logos')
                            ->schema([
                                Repeater::make('sections.logos.items')
                                    ->label('Logos')
                                    ->defaultItems(6)
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Logo Image')
                                            ->disk('public')
                                            ->directory('pages/home/logos')
                                            ->visibility('public')
                                            ->image()
                                            ->imageEditor()
                                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? collect($state)->values()->first() : $state)
                                            ->required(),

                                        TextInput::make('link')
                                            ->label('Link (optional)')
                                            ->placeholder('https://...'),
                                    ])
                                    ->columns(2),
                            ]),

                        // -----------------------------------------------------
                        // FOOTER
                        // -----------------------------------------------------
                        Section::make('Footer')
                            ->schema([
                                TextInput::make('sections.footer.company_name')
                                    ->label('Company Name')
                                    ->default('Gims Care Solutions'),

                                Textarea::make('sections.footer.about_text')
                                    ->label('About Text')
                                    ->rows(4)
                                    ->default('Gims Care Solutions is dedicated to delivering high-quality home care with a personal approach, tailored to every client’s needs.'),

                                Repeater::make('sections.footer.links')
                                    ->label('Footer Links')
                                    ->defaultItems(4)
                                    ->schema([
                                        TextInput::make('label')->required()->default('Home'),
                                        TextInput::make('url')->required()->default('/'),
                                    ])
                                    ->columns(2),
                                    
                                Repeater::make('sections.footer.social_links')
                                    ->label('Social Media Links')
                                    ->helperText('Add your social profiles (these will appear in the site footer).')
                                    ->default([])
                                    ->collapsed()
                                    ->reorderable()
                                    ->schema([
                                        Select::make('platform')
                                            ->label('Platform')
                                            ->options([
                                                'facebook'  => 'Facebook',
                                                'instagram' => 'Instagram',
                                                'twitter'   => 'X (Twitter)',
                                                'linkedin'  => 'LinkedIn',
                                                'youtube'   => 'YouTube',
                                                'tiktok'    => 'TikTok',
                                                'whatsapp'  => 'WhatsApp',
                                            ])
                                            ->required(),

                                        TextInput::make('url')
                                            ->label('Profile URL')
                                            ->placeholder('https://...')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(2),

                                Textarea::make('sections.footer.address')
                                    ->label('Office Address')
                                    ->rows(4)
                                    ->default("Unit 10, The Old Courthouse,\nOrsett Road, Grays\nEssex RM17 5DD"),

                                TextInput::make('sections.footer.phone_primary')
                                    ->label('Phone (Primary)')
                                    ->default('+44 (0) 7380894484'),

                                TextInput::make('sections.footer.phone_secondary')
                                    ->label('Phone (Secondary)')
                                    ->default('+44 (0) 3330115406'),

                                TextInput::make('sections.footer.email')
                                    ->label('Email')
                                    ->default('admin@gimscare.co.uk'),

                                TextInput::make('sections.footer.website')
                                    ->label('Website')
                                    ->default('https://gimscare.co.uk'),
                            ])
                            ->columns(2),

                        // -----------------------------------------------------
                        // CTA
                        // -----------------------------------------------------
                        Section::make('Call To Action (CTA)')
                            ->columns(2)
                            ->schema([
                                TextInput::make('sections.cta.title')
                                    ->label('CTA Title')
                                    ->default('Need support? Contact us today.'),

                                TextInput::make('sections.cta.button_text')
                                    ->label('CTA Button Text')
                                    ->default('Contact Us'),

                                TextInput::make('sections.cta.button_link')
                                    ->label('CTA Button Link')
                                    ->default('/contact'),
                            ]),
                    ]),
            ]);
    }
}
