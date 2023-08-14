<?php

declare(strict_types=1);

namespace Tests\doubles;

use League\Fractal\TransformerAbstract;

class ResponseTransformerDouble extends TransformerAbstract
{
    /**
     * Transform object into response.
     */
    public function transform(): array
    {
        return [];
    }
}
