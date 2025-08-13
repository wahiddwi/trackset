@foreach($childs as $child)
    <tr>
        <td>@for($i = 0; $i < $count; $i++)<i class="fa fa-chevron-right"></i>@endfor {{$child->mod_name}}</td>
        <td>{{$child->mod_desc}}</td>
        <td class="text-center"><div class="form-check"><input class="form-check-input prv_check" {{$disable_upd}} name="{{$child->mod_code . '[]'}}" id="{{$child->mod_code . '_view'}}" type="checkbox" value="view"></div></td>
        <td class="text-center"><div class="form-check"><input class="form-check-input prv_check" {{$disable_upd}} name="{{$child->mod_code . '[]'}}" id="{{$child->mod_code . '_create'}}" type="checkbox" value="create"></div></td>
        <td class="text-center"><div class="form-check"><input class="form-check-input prv_check" {{$disable_upd}} name="{{$child->mod_code . '[]'}}" id="{{$child->mod_code . '_update'}}" type="checkbox" value="update"></div></td>
        <td class="text-center"><div class="form-check"><input class="form-check-input prv_check" {{$disable_upd}} name="{{$child->mod_code . '[]'}}" id="{{$child->mod_code . '_print'}}" type="checkbox" value="print"></div></td>
        <td class="text-center"><div class="form-check"><input class="form-check-input prv_check" {{$disable_upd}} name="{{$child->mod_code . '[]'}}" id="{{$child->mod_code . '_post'}}" type="checkbox" value=post></div></td>
        <td class="text-center"><div class="form-check"><input class="form-check-input prv_check" {{$disable_upd}} name="{{$child->mod_code . '[]'}}" id="{{$child->mod_code . '_delete'}}" type="checkbox" value=delete></div></td>
    </tr>
    @if(count($child->children))
        @include('master.role.manageChild',['childs' => $child->children()->orderBy('mod_order', 'asc')->get(), 'count' => $count+1])
    @endif
@endforeach
