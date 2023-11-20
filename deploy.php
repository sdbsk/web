<?php
namespace Deployer;

require 'recipe/common.php';

import('hosts.yml');

// Project name
set('application', 'saleziani.sk');

// Project repository
set('repository', 'git@bitbucket.org:bratiask/saleziani.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', ['.env.local']);
set('shared_dirs', ['web/app/uploads']);

// Writable dirs by web server
set('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

//host('saleziani.sk')
//    ->setHostname('2.saleziani.sk')
////    ->setPort(20001)
////    ->setRemoteUser('ssh-175876')
//    ->set('deploy_path', '~/{{application}}/_sub/stage');

task('deploy:flush_rewrite', function (): void {
    run('cd {{ release_path }} && php8.1 bin/wp-cli.phar rewrite flush');
});

task('deploy:theme', function (): void {
//    run('cd {{ release_path }} && npm i && npm run prod');
//    run('cd {{ release_path }} && bin/wp-cli.phar acorn migrate --force');
//    run('cd {{ release_path }} && bin/wp-cli.phar language core install en_US sk_SK');
//    run('cd {{ release_path }} && bin/wp-cli.phar language plugin install --all en_US sk_SK');
});

// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:theme',
    'deploy:flush_rewrite',
    'deploy:publish',
]);

//task('deploy', [
//    'deploy:info',
//    'deploy:prepare',
//    'deploy:lock',
//    'deploy:release',
//    'deploy:update_code',
//    'deploy:shared',
//    'deploy:writable',
//    'deploy:vendors',
//    'deploy:clear_paths',
//    'deploy:symlink',
//    'deploy:unlock',
////    'cleanup',
////    'success'
//]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
