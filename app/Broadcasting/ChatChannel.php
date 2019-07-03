<?php

namespace App\Broadcasting;

use App\Models\User;

class ChatChannel
{

    public function __construct()
    {

    }

    public function join(User $user, $rebase_user_id)
    {
        return $user->rebase_user_id === $rebase_user_id;
    }
}
