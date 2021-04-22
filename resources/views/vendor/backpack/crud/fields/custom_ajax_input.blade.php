<!-- custom_ajax_input -->
@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
{{--{{dd( )}}--}}
<input

    type="text"
    name="{{ $field['name'] }}"
    value="{{isset($field['value']) ? (isset($field['value'][(app()->getLocale())]) ? $field['value'][(app()->getLocale())] : '') : ''}}"
    @include('crud::fields.inc.attributes')
>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD EXTRA JS --}}
    {{-- push things in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script type="text/javascript">
            $(document).on('change','[name="language"]',function () {
                language = $('[name="language"]').val();
                id = "{{$entry->id}}";
                console.log(language + id)
                $.ajax('/translation/' + id + '/' + language, {
                    type: 'GET',  // http method
                    success: function (data) {
                        $('[name="{{ $field["name"] }}"]').val(data);
                    }
                });
            })

        </script>

    @endpush
@endif
