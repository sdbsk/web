<?php

declare(strict_types=1);

namespace App;

use App\Service\Wordpress;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel;

class ThemeKernel extends Kernel
{
    use MicroKernelTrait;

//    protected function configureContainer(ContainerConfigurator $container): void
//    {
//        $container->import('../config/{packages}/*.yaml');
//        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');
//
//        if (is_file(dirname(__DIR__) . '/config/services.yaml')) {
//            $container->import('../config/services.yaml');
//            $container->import('../config/{services}_' . $this->environment . '.yaml');
//        } else {
//            $container->import('../config/{services}.php');
//        }
//    }

    public function initWordpress(): void
    {
        /** @var Wordpress $wordpress */
        $wordpress = $this->getContainer()->get(Wordpress::class);

        $wordpress->registerBlockTypes();
    }

    public function boot(): void
    {
        parent::boot();

        static $initialized = false;

        if ($initialized) {
            return;
        }

        $initialized = true;

//        foreach ($this->getContainer()->get('router')->getRouteCollection() as $routeName => $route) {
//            dd($route->compile());

//            dd($route->compile()->getRegex());
//echo $route->compile()->getRegex();
//            add_filter('query_vars', fn($vars) => ['icon', ...$vars]);
//            add_rewrite_rule($route->compile()->getRegex(), 'index.php?name=' . $routeName, 'top');
//            add_rewrite_rule('{^/test123/(?P[^/]++)$}sDu', 'index.php?name=' . $routeName, 'top');
//            add_action('parse_request', function (\WP $wp) use ($routeName) {
//dd($wp);
//                if ($wp->query_vars['name'] === $routeName) {
//                    dd('ss');
//                }
//            });
//dd(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
//            $response = $this->handle(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
//            dd();
//            echo $response->getContent();
//        }
    }

    public function bootWordpressTheme(): void {
        /** @var Wordpress $wordpress */
        $wordpress = $this->getContainer()->get(Wordpress::class);

        $wordpress->registerBlockTypes();
    }
}
