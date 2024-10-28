<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = http_build_query([]);
        $token = Hash::make($this->user->email);

        DB::table('users_reset_password')->upsert([
            "user_id" => $this->user->id,
            "reset_password_token" => $token,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ], ["user_id"], ["reset_password_token", "updated_at"]);

        return $this->from('no-reply@sitama-elektro.polines.ac.id')
            ->replyTo(NULL)
            ->view('email.reset-password')
            ->with(
                [
                    'nama'       => $this->user->name,
                    'email'      => $this->user->email,
                    'reset_link' => route('reset-password') . "?token=" . $token
                ]
            );
    }
}
