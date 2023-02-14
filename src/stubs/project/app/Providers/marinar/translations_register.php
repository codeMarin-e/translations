<?php
\Illuminate\Support\Facades\Gate::policy(\Waavi\Translation\Models\Translation::class, \App\Policies\TranslationPolicy::class);

if(request()->whereIAm() == 'admin') {
    config(['translator.cache.enabled' => false]);
}
