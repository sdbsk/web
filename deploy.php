<?php

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

task('deploy:flush_rewrite', function (): void {
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar rewrite flush --hard');
});

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:flush_rewrite',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
