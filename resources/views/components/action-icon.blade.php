@props(['action'])

@switch($action)
    @case(ADD)
        <i class='fas fa-plus-circle'></i>
        @break
    @case(EDIT)
        <i class="fa-regular fa-pen-to-square"></i>
        @break
    @case(REMOVE)
        <i class='fa-regular fa-trash-can'></i>
        @break
    @case(DELETE)
        <i class='fa-solid fa-trash'></i>
        @break
    @case(RESTORE)
        <i class="fa-solid fa-arrow-rotate-left"></i>
        @break
    @case('view')
        <i class="fa-regular fa-eye"></i>
        @break
@endswitch
