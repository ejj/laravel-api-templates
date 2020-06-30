<?php

namespace Preferred\Domain\Users\Listeners\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Neves\Events\Contracts\TransactionalEvent;
use Preferred\Domain\Users\Entities\User;
use Ramsey\Uuid\Uuid;

class UserObserver implements TransactionalEvent
{
    public function creating(Model $model)
    {
        $model->setAttribute('id', $model->getAttribute('id') ?? Uuid::uuid4()->toString());
    }

    public function updated(User $user)
    {
        Cache::forget($user->id);

        if ($user->is_active) {
            Cache::put($user->id, $user, 60);
        }

        Cache::tags('users:' . $user->id)->flush();
    }
}
