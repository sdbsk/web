<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace Deployer;

require 'recipe/common.php';

import('hosts.yml');

set('application', 'saleziani.sk');
set('repository', 'git@bitbucket.org:bratiask/saleziani.git');
set('git_tty', true);
set('shared_files', ['.env.local']);
set('shared_dirs', ['web/app/uploads']);
set('writable_dirs', []);
set('allow_anonymous_stats', false);

task('deploy:install_languages', function (): void {
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar language core install en_US sk_SK');
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar language plugin install --all en_US sk_SK');
});

task('deploy:flush_rewrite', function (): void {
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar rewrite flush --hard');
});

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:install_languages',
    'deploy:flush_rewrite',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
