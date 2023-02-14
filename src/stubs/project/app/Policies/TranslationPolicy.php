<?php

namespace App\Policies;

use App\Models\User;

class TranslationPolicy
{
    public function before(User $user, $ability) {
        // @HOOK_POLICY_BEFORE
        if($user->hasRole('Super Admin', 'admin') )
            return true;
    }

    public function view(User $user) {
        // @HOOK_POLICY_VIEW
        return $user->hasPermissionTo('translations.view', request()->whereIam());
    }

    public function update(User $user) {
        // @HOOK_POLICY_UPDATE
        if( !$user->hasPermissionTo('translation.update', request()->whereIam()) )
            return false;
        return true;
    }

    // @HOOK_POLICY_END


}
