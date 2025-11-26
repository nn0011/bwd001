<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
		'/billing/reading/update_previous_reading',
		'/billing/reading/update_previous_reading',
		'/billing/reading/update_current_reading',
		'/billing/reading/update_init_reading',
		'/readings/upload_reading_data',
		'/readings/upload_reading_data/rnb_v1',
		'/readings/officer_login',
		'/readings/update_gps_data_to_server',
        //
    ];
}
