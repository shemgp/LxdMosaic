<?php

namespace dhope0000\LXDClient\Controllers\InstanceSettings\Users;

use dhope0000\LXDClient\Tools\User\ResetPassword;

class ResetPasswordController
{
    private $resetPassword;

    public function __construct(ResetPassword $resetPassword)
    {
        $this->resetPassword = $resetPassword;
    }

    public function reset(int $targetUser, string $newPassword)
    {
        $this->resetPassword->reset($targetUser, $newPassword);
        return ["state"=>"success", "message"=>"Updated password"];
    }
}
