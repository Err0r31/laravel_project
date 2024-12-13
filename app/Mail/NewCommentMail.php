<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use App\Models\Comment;
use App\Models\Article;

class NewCommentMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $article;

    public function __construct(public Comment $comment) {
        $this->article = Article::findOrFail($comment->article_id);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'egordem@list.ru'), env('MAIL_FROM_NAME', 'Новый комментарий')),
            subject: 'New Comment Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.newcomment',
            with:[
                'article'=>$this->article,
                'comment'=>$this->comment->desc,
            ]
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path().'/preview.jpg'),
        ];
    }
}