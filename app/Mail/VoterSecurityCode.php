<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoterSecurityCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $voterName;

    public function __construct($code, $voterName)
    {
        $this->code = $code;
        $this->voterName = $voterName;
    }

    public function build()
{
    return $this->subject('Your Voting Security Code: ' . $this->code)
                ->html("
                <div style='font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 20px;'>
                    <div style='max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; border-top: 5px solid #4f46e5;'>
                        <h2 style='color: #333;'>Hello, {$this->voterName}!</h2>
                        <p style='color: #555; font-size: 16px;'>To proceed with your vote in the <strong>Enhance Voting System</strong>, please use the security code below:</p>
                        <div style='background: #f0f0ff; padding: 20px; text-align: center; border-radius: 8px; margin: 25px 0;'>
                            <span style='font-size: 32px; font-weight: bold; color: #4f46e5; letter-spacing: 5px;'>{$this->code}</span>
                        </div>
                        <p style='font-size: 14px; color: #888;'>For your protection, do not share this code with anyone. This code is required to verify your identity and prevent unauthorized voting.</p>
                        <hr style='border: none; border-top: 1px solid #eee; margin-top: 20px;'>
                        <p style='text-align: center; font-size: 12px; color: #aaa;'>&copy; 2026 Catalino D. Cerezo National High School</p>
                    </div>
                </div>
                ");
}
}