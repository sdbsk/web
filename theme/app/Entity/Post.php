<?php

declare(strict_types=1);

namespace App\Entity;

use WP_Post;

readonly class Post
{
    public string $title;
    public string $perex;
    public string $url;
    public int $thumbnail;

    public function __construct(WP_Post $post)
    {
        $this->title = $post->post_title;
        $this->perex = html_entity_decode(get_the_excerpt($post));
        $this->url = get_post_permalink($post);
        $this->thumbnail = get_post_thumbnail_id($post);
    }
}
