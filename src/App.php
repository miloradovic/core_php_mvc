<?php

namespace App;

use App\AppRouter;

class App {
    public function __construct() {
        AppRouter::init();
    }

    public function run() {
        AppRouter::run();
    }
}
