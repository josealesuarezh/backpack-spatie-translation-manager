<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ url('/import') }}"><i class="las la-upload nav-icon"></i> Import Languages</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('language') }}'><i class='nav-icon las la-language'></i> Languages</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('languagetranslation') }}'><i class='nav-icon las la-book'></i> Language Translations</a></li>
<li class='nav-item'><a class='nav-link'>Test(test.required) => {{trans('test.required')}}</a></li>
