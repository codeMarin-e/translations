@if($authUser->can('view', \Waavi\Translation\Models\Translation::class))
    {{--   Translations --}}
    <li class="nav-item @if(request()->route()->named("{$whereIam}.translations.*")) active @endif">
        <a class="nav-link " href="{{route("{$whereIam}.translations.index")}}">
            <i class="fa fa-fw fa-flag mr-1"></i>
            <span>@lang("admin/translations/translations.sidebar")</span>
        </a>
    </li>
@endif
