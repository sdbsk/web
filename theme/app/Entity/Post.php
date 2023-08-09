<?php

declare(strict_types=1);

namespace App\Entity;

use WP_Post;
use WP_Term;

readonly class Post
{
    public int $id;
    public string $title;
    public string $perex;
    public string $url;
    public int $thumbnail;
    public array $categories;

    public function __construct(WP_Post $post)
    {
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->perex = html_entity_decode(get_the_excerpt($post));
        $this->url = get_post_permalink($post);
        $this->thumbnail = get_post_thumbnail_id($post);
        $this->categories = array_map(fn(WP_Term $term) => new Category($term), get_the_category($post->ID));
    }
}
