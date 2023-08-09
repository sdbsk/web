<?php

declare(strict_types=1);

namespace App\Entity;

use WP_Term;

readonly class Category
{
    public int $id;
    public string $title;
    public string $url;
    public array $posts;

    public function __construct(WP_Term $term, array $posts = [])
    {
        $this->id = $term->term_id;
        $this->title = $term->name;
        $this->url = get_category_link($term);
        $this->posts = $posts;
    }
}
