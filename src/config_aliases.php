<?php

$CONFIG_APP['aliases'] = [
    'DB'        => Illuminate\Database\Capsule\Manager::class,
    'Carbon'    => Illuminate\Support\Carbon::class,
    'CrudUrl'   => angelrove\membrillo\CrudUrl::class,
    'Login'     => angelrove\membrillo\Login\Login::class,
    'Messages'  => angelrove\membrillo\Messages::class,
    'Session'   => angelrove\membrillo\WApp\Session::class,
    'Local'     => angelrove\membrillo\WApp\Local::class,
    'Event'     => angelrove\membrillo\WObjectsStatus\Event::class,
    'WPage'     => angelrove\membrillo\WPage\WPage::class,
    'WList'     => angelrove\membrillo\WObjects\WList\WList::class,
    'WForm'     => angelrove\membrillo\WObjects\WForm\WForm::class,
    'CssJsLoad' => angelrove\utils\CssJsLoad::class,
];
