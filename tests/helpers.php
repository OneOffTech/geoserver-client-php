<?php

if (! function_exists('tap')) {

    /**
     * Call the given Closure with the given value then return the value.
     *
     * Borrowed from Laravel Framework https://github.com/laravel/framework/blob/407b7b085223604a82711fedb16e2ea50ac5e807/src/Illuminate/Support/helpers.php#L1029
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @return mixed
     */
    function tap($value, $callback)
    {
        $callback($value);
        return $value;
    }
}
