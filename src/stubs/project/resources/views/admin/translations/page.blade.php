<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">@lang('admin/translations/translations.translations')</li>
</ol>

@if(session('trans_success'))
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-success alert-dismissable">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>{{session('trans_success')}}</strong>
            </div>
        </div>
    </div>
    <!-- /.row -->
@endif

<form autocomplete="off">
    <div class="row">

        {{-- SEARCH --}}
        <div class="form-group row col-lg-6">
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text"
                           name="search"
                           id="search"
                           placeholder="@lang('admin/translations/translations.search')"
                           value="@isset($search){{$search}}@endisset"
                           class="form-control "
                    />
                    <div class="input-group-append">
                        <button class="btn btn-primary"><i class="fas fa-search text-grey"
                                                           aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- END SEARCH --}}
    </div>
</form>

<form autocomplete="off">
    <div class="row">
        {{-- NAMESPACES--}}
        <div class="form-group row col-sm-4">
            <label for="filters[namespace]" class="col-form-label col-sm-3">@lang('admin/translations/translations.filter_namespace'):</label>
            <div class="col-sm-9">
                <select id="filters[namespace]"
                        name="filters[namespace]"
                        {{--                                data-action_all="{{marinarFullUrlWithQuery( ['filters' => ['namespace' => null]] )}}"--}}
                        {{--                                data-action="{{marinarFullUrlWithQuery( ['filters' => ['namespace' => '__VAL__']] )}}"--}}
                        class="form-control js_filter">
                    @foreach($namespaces as $namespace => $langPath)
                        <option value="{{$namespace}}"
                                @if($chNamespace == $namespace) selected="selected" @endif
                        >{{$namespace}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- END NAMESPACES--}}
        {{-- DIRECTORIES --}}
        <div class="form-group row col-sm-4">
            <label for="filters[dir]" class="col-form-label col-sm-3">@lang('admin/translations/translations.filter_dir'):</label>
            <div class="col-sm-9">
                <select id="filters[dir]"
                        name="filters[dir]"
                        {{--                                data-action_all="{{marinarFullUrlWithQuery( ['filters' => ['dir' => null]] )}}"--}}
                        {{--                                data-action="{{marinarFullUrlWithQuery( ['filters' => ['dir' => '__VAL__']] )}}"--}}
                        class="form-control js_filter">
                    <option value='all'>@lang('admin/translations/translations.filter_dir_all')</option>
                    <option value='in'
                            @if($chDir == 'in') selected="selected" @endif
                    >@lang('admin/translations/translations.filter_dir_in')</option>
                    @foreach($dirs as $dir)
                        <option value="{{$dir}}"
                                @if($chDir == $dir) selected="selected" @endif
                        >{{ $dir }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- END DIRECTORIES --}}
        {{-- GROUP --}}
        <div class="form-group row col-sm-4">
            <label for="filters[group]" class="col-form-label col-sm-3">@lang('admin/translations/translations.filter_group'):</label>
            <div class="col-sm-9">
                <select id="filters[group]"
                        name="filters[group]"
                        {{--                                data-action_all="{{marinarFullUrlWithQuery( ['filters' => ['group' => null]] )}}"--}}
                        {{--                                data-action="{{marinarFullUrlWithQuery( ['filters' => ['group' => '__VAL__']] )}}"--}}
                        class="form-control js_filter">
                    <option value='all'>@lang('admin/translations/translations.filter_group_all')</option>
                    @foreach($groups as $group)
                        <option value="{{$group}}"
                                @if($chGroup == $group) selected="selected" @endif
                        >{{ $group }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- END GROUP --}}
    </div>
</form>
<div class="table-responsive rounded">
    <table class="table table-sm table-striped">
        <thead class="thead-dark">
        <tr>
            <th scope="col" class="text-center">@lang('admin/translations/translations.key')</th>

            <th scope="col" class="text-center w-75">@lang('admin/translations/translations.value')</th>
        </tr>
        </thead>
        @foreach($groups as $group)
            @if($chGroup != 'all' && $chGroup != $group) @continue @endif
            @php
                $chDirGroup = dirname($group);
                $chDirGroup = $chDirGroup === '.'? '' : $chDirGroup;
            @endphp
            <tbody>
                <tr scope="row">
                    <td scope="col" colspan='2' style="cursor: pointer;"
                        class="text-center js_group @if($chGroup == $group || $loop->count == 1) bg-success @else bg-warning @endif"
                        data-namespace="{{$chNamespace}}"
                        data-dir="{{!in_array($chDir, ['in', 'all'])? trim($chDir.DIRECTORY_SEPARATOR.$chDirGroup, DIRECTORY_SEPARATOR) : $chDirGroup}}"
                        data-group_value="{{basename($group)}}"
                        data-group="{{$group}}">
                        <strong class="align-middle">{{$group}}</strong>
                    </td>
                </tr>
            </tbody>
            <tbody class="js_group_rows @if($chGroup != $group && $loop->count != 1) d-none @endif"
                   data-group="{{$group}}">
            </tbody>
        @endforeach
    </table>
</div>
