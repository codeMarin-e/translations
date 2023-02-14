@php $allowChanges = $authUser->can('update', \Waavi\Translation\Models\Translation::class); @endphp
@foreach($translations as $item => $trans_value)
    @if(is_array($trans_value)) @continue @endif
    @php
        $transKey = $chNamespace == '*'?
            "{$dirGroup}.{$item}" :
            "{$chNamespace}::{$dirGroup}.{$item}";
        $translation = trans($transKey);
    @endphp
    @if(!is_null($search) && strpos($item, $search) === false && strpos($translation, $search) === false) @continue @endif
    <tr scope="row">
        <td scope="col" class="text-center">
            <strong class="align-middle">{{$item}}</strong>
            <div class="spinner-border spinner-border-sm text-danger js_item_loader float-right d-none"
                 data-group="{{$chGroup}}"
                 data-item="{{$item}}"
                 role="status">
                <span class="sr-only">Loading...</span>
            </div>

        </td>
        <td scope="col" class="text-center w-75">
            <div class="form-group mb-0">
                <form autocomplete="off"
                      method="POST"
                      action="{{route("{$route_namespace}.translations.update")}}">
                    @method('PATCH')
                    @csrf
                    <input type="hidden"
                           name="namespace"
                           value="{{$chNamespace}}" />
                    <input type="hidden"
                           name="dir"
                           value="{{$chDir}}" />
                    <input type="hidden"
                           name="group"
                           value="{{$chGroup}}" />
                    <input type="hidden"
                           name="item"
                           value="{{$item}}" />
                    <input type="text"
                           name="text"
                           value="{{$translation}}"
                           data-group="{{$chGroup}}"
                           data-item="{{$item}}"
                           @if(!$allowChanges) readonly='readonly' @endif
                           class="form-control js_translation" />
                </form>
            </div>
        </td>
    </tr>
@endforeach
