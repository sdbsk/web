<?php

remove_action( 'wp_head', 'rel_canonical' );

add_action( 'wp_head', function (): void {
    $fallbackImage = get_template_directory_uri() . '/assets/images/fb-share.jpg';

    if ( is_category() ) {
        $category = get_queried_object();

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $url = get_category_link( $category );

        if ($paged > 1) {
            $url = trailingslashit($url) . 'page/' . $paged;
        }

        $tags = [
            'title'       => $category->name,
            'description' => $category->description,
            'image'       => $fallbackImage,
            'url'         => $url,
        ];
    } else {
        global $post;

        if ( $post instanceof WP_Post ) {
            $image = $fallbackImage;
            $url = get_permalink();

            preg_match_all('/wp:query \{"queryId":(\d+)/', $post->post_content, $matches);

            $params = [];

            foreach ($matches[1] as $queryId) {
                $paramName = "query-$queryId-page";
                $paramValue = $_GET[$paramName] ?? null;

                if (!empty($paramValue)) {
                    $params[$paramName] = $paramValue;
                }
            }

            if (count($params) > 0) {
                ksort($params);

                foreach ($params as $name => $value) {
                    $url = add_query_arg($name, $value, $url);
                }
            }

            foreach ( [ $post->ID, ...get_post_ancestors( $post->ID ) ] as $postId ) {
                $thumbnail = get_the_post_thumbnail_url( $postId, 'large' );

                if ( ! empty( $thumbnail ) ) {
                    $image = $thumbnail;
                    break;
                }
            }

            $tags = [
                'title'       => get_the_title(),
                'description' => get_the_excerpt(),
                'image'       => $image,
                'url'         => $url,
            ];

            if ( is_single() ) {
                $tags['type'] = 'article';
            }
        } else {
            $tags = [];
        }
    }

    if (isset($tags['url'])) {
        echo '<link rel="canonical" href="' . esc_attr($tags['url']) . '" />';
    }

    foreach ( $tags as $name => $value ) {
        if ( ! empty( $value ) ) {
            echo '<meta property="og:' . $name . '" content="' . esc_attr( $value ) . '" />';
        }
    }

    if ( defined( 'WP_ENV' ) && WP_ENV === 'production' ) {
        echo <<<TRACKING
        <!-- Matomo -->
        <script>
          var _paq = window._paq = window._paq || [];
          _paq.push(['trackPageView']);
          _paq.push(['enableLinkTracking']);
          (function() {
            var u='//matomo.saleziani.sk/';
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Matomo Code -->


        <!-- Meta Pixel Code -->
        <script type="text/plain" data-category="targeting">
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '920280066494770');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=920280066494770&ev=PageView&noscript=1"/></noscript>
        <!-- End Meta Pixel Code -->
TRACKING;
    }
} );
