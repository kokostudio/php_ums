<?php

namespace App\Core;

interface MiddlewareInterface
{
    /**
     * Handle the request before passing it to the controller.
     * Return true to proceed, or redirect/die to abort.
     */
    public function handle(): bool;
}
