<?php

namespace App\Console\Commands;

use App\Mail\ClickMail;
use Illuminate\Console\Command;
use App\Models\Clicks;
use Carbon\Carbon;
use App\Models\Comment;
use Illuminate\Support\Facades\Mail;

class SaveClickCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-click-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $article_count = Clicks::count();
        Clicks::whereNotNull('id')->delete();
        $comment_count = Comment::whereDate('created_at', Carbon::today())->count();
        Mail::to('egordem@list.ru')->send(new ClickMail($article_count, $comment_count));
    }
}
