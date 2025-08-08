<?php

namespace App\Service;

use WP_Post;

class Stack {
    private array $ancestors = [];
    private array $pages = [];
    private array $topLevelPages = [];
    private array $urls = [];

    function ancestors(WP_Post $page): array
    {
        if (!isset($this->ancestors[$page->ID])) {
            $this->ancestors[$page->ID] = get_post_ancestors($page);
        }

        return $this->ancestors[$page->ID];
    }

    function page(?int $pageId = null): WP_Post
    {
        if (null === $pageId || !isset($this->pages[$pageId])) {
            global $post;

            $page = null === $pageId ? ($post ?? get_post()) : get_post($pageId);
            $pageId = $page->ID;
            $this->pages[$pageId] = $page;
        }

        return $this->pages[$pageId];
    }

    function hasSubnavigation(?WP_Post $page = null): bool
    {
        $topLevelPageId = $this->topLevelPageId($page);

        // zisti z meta, ci ma zapnutu subnavigaciu

        return true;
    }

    function topLevelPageId(?WP_Post $page = null): int
    {
        $page = $page ?? $this->page();

        if (!isset($this->topLevelPages[$page->ID])) {
            $ancestors = $this->ancestors($page);
            $this->topLevelPages[$page->ID] = empty($ancestors) ? $page->ID : end($ancestors);
        }

        return $this->topLevelPages[$page->ID];
    }

    function url(WP_Post $page): string
    {
        if (!isset($this->urls[$page->ID])) {
            $this->urls[$page->ID] = get_permalink($page);
        }

        return $this->urls[$page->ID];
    }
}
