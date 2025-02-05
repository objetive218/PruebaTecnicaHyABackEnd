<?php

namespace App\Policies;

use App\Models\PostDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostDocumentPolicy
{
   
    public function modify(User $user, PostDocument $postDocument): Response
    {
        return $user->id=== $postDocument->user_id
        ? Response::allow()
        : Response::deny('Este archivo no te pertenece');
    }
}
