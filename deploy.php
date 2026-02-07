<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github-orewire:jonesrussell/orewire-laravel.git');
set('keep_releases', 5);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

task('deploy:build_assets', function (): void {
    run('bash -lc "source ~/.nvm/nvm.sh 2>/dev/null; cd {{release_path}} && npm ci && npm run build:ssr"');
});
after('deploy:vendors', 'deploy:build_assets');

// Hosts

host('orewire.ca')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/orewire-laravel')
    ->set('http_user', 'www-data');

// Hooks

after('deploy:failed', 'deploy:unlock');
task('deploy:migrate', function (): void {
    run('cd {{release_path}} && {{bin/php}} artisan migrate --force');
});
after('deploy:symlink', 'deploy:migrate');
task('deploy:stop_inertia_ssr', function (): void {
    run('cd {{release_path}} && {{bin/php}} artisan inertia:stop-ssr', ['allow_failure' => true]);
});
after('deploy:migrate', 'deploy:stop_inertia_ssr');
task('deploy:reload_php_fpm', function (): void {
    run('sudo systemctl restart php8.4-fpm', ['allow_failure' => true]);
});
after('deploy:stop_inertia_ssr', 'deploy:reload_php_fpm');
