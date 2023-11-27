<?php

namespace App\Http\Controllers;

use App\Support\HandleJsonResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HandleJsonResponses;

    /**
     * Make API call with exception handling.
     * This allows to gracefully catch all possible exceptions and handle them properly.
     *
     * @param $callback
     *
     * @return mixed
     */
    protected function withErrorHandling($callback): mixed
    {
        try {
            return $callback();
        } catch (\Exception) {
            return $this->message(__('An unexpected error occurred. Please try again later.'))
                ->respondBadRequest();
        }
    }

    /**
     * @param $callback
     * @return mixed
     */
    protected function withMessageErrorHandling($callback): mixed
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $this->message($e->getMessage())
                ->respondBadRequest();
        }
    }

    /**
     * Use when has custom exception
     *
     * @param $callback
     * @return mixed
     */
    protected function withOverlapErrorHandling($callback): mixed
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $this->handleComponentError($e);
        }
    }
}
