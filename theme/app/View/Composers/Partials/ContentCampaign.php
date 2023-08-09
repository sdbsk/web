<?php

declare(strict_types=1);

namespace App\View\Composers\Partials;

use App\Entity\Campaign;
use Roots\Acorn\View\Composer;

class ContentCampaign extends Composer
{
    protected static $views = [
        'partials.content-campaign',
    ];

    protected function with(): array
    {
        return [
            'campaign' => new Campaign(get_post()),
        ];
    }
}
