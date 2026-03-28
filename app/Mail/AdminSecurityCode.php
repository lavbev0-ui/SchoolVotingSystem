<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminSecurityCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $adminName;

    public function __construct($code, $adminName)
    {
        $this->code      = $code;
        $this->adminName = $adminName;
    }

    public function build()
    {
        return $this->subject('Admin Security Code: ' . $this->code)
                    ->html("
                    <div style='font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 20px;'>
                        <div style='max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; border-top: 5px solid #dc2626;'>
                            <h2 style='color: #333;'>Hello, {$this->adminName}!</h2>
                            <p style='color: #555; font-size: 16px;'>Your admin login verification code for <strong>Enhance Voting System</strong>:</p>
                            <div style='background: #fff0f0; padding: 20px; text-align: center; border-radius: 8px; margin: 25px 0;'>
                                <span style='font-size: 32px; font-weight: bold; color: #dc2626; letter-spacing: 5px;'>{$this->code}</span>
                            </div>
                            <p style='font-size: 14px; color: #888;'>Do not share this code with anyone. This code expires in 5 minutes.</p>
                            <hr style='border: none; border-top: 1px solid #eee; margin-top: 20px;'>
                            <p style='text-align: center; font-size: 12px; color: #aaa;'>&copy; 2026 Catalino D. Cerezo National High School</p>
                        </div>
                    </div>
                    ");
    }
}