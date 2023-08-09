<?php

declare(strict_types=1);

namespace App\Entity;

use WP_Post;

readonly class Campaign
{
    public int $id;
    public bool $enabled;
    public string $description;
    public string $externalUrl;

    public function __construct(WP_Post $post)
    {
        $this->id = $post->ID;
        $this->enabled = (bool)get_post_meta($post->ID, 'campaign_enabled', true);
        $this->description = get_post_meta($post->ID, 'campaign_description', true);
        $this->externalUrl = get_post_meta($post->ID, 'campaign_external_url', true);
    }
}
