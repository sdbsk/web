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
set('shared_dirs', ['web/app/uploads', 'web/app/w3tc-config', 'web/app/themes/saleziani/assets']);
set('writable_dirs', []);
set('allow_anonymous_stats', false);

task('deploy:theme', function (): void {
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar language core install en_US sk_SK');
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar language plugin install --all en_US sk_SK');
});

task('deploy:flush', function (): void {
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar cache flush all');
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar w3-total-cache flush all 2> /dev/null');
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar w3-total-cache fix_environment apache 2> /dev/null');
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar rewrite flush --hard');
});

task('copy:assets', function (): void {
    run('mkdir {{ release_path }}/web/app/themes/saleziani/assets-new');
    run('cp -r {{ release_path }}/web/app/themes/saleziani/assets/* {{ release_path }}/web/app/themes/saleziani/assets-new/');
});

task('merge:assets', function (): void {
    run('cp -r {{ release_path }}/web/app/themes/saleziani/assets-new/* {{ release_path }}/web/app/themes/saleziani/assets/');
    run('rm -rf {{ release_path }}/web/app/themes/saleziani/assets-new/');
});

task('opcache:reset', function (): void {
    switch (get('branch')) {
        case 'develop':
            run('curl https://stage.saleziani.sk/reset-opcache-b329c841308d500b5f49daeeb3a872cf.php');
            break;
        case 'main':
            run('curl https://main.saleziani.sk/reset-opcache-b329c841308d500b5f49daeeb3a872cf.php');
            break;
    }
});

before('deploy:shared', 'copy:assets');
after('deploy:shared', 'merge:assets');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:theme',
    'deploy:flush',
    'deploy:publish',
]);

after('deploy:symlink', 'opcache:reset');
after('deploy:failed', 'deploy:unlock');
