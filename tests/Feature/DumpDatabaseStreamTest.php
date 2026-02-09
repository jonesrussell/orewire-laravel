<?php

use Illuminate\Support\Facades\Config;

test('fails when default database connection is not mysql or mariadb', function (): void {
    Config::set('database.default', 'sqlite');

    $this->artisan('db:dump-stream')
        ->assertFailed();
});
