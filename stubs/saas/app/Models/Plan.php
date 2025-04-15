<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'stripe_id',
        'features',
        'interval',
        'currency',
        'price',
        'price_description',
        'description',
    ];

    public function getFeaturesListAttribute(): array
    {
        // explode per new line
        $list = explode("\n", $this->features);

        // map the list. If a line begins with a + sign, set the included key to true,else if - sign, set to false
        return array_map(function ($item) {
            return [
                'name' => ltrim($item, '+- '),
                'included' => str_starts_with($item, '+'),
            ];
        }, $list);
    }
}
