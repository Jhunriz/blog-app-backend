<?php

namespace App\Constants;

class RoutePaths
{
    public const INDEX = '/';
    public const STORE = '/store';
    public const SHOW = '/show/{id}';
    public const EDIT = '/edit/{id}';
    public const DELETE = '/delete/{id}';
    public const UPDATE = '/update/{id}';
    public const RESTORE = '/restore/{id}';
    public const RESTORE_ALL = '/restore-all';
    public const FORCE_DELETE = '/force-delete';

    public const REGISTER = '/register';
    public const LOGIN = '/login';
    public const LOGOUT = '/logout';
}
