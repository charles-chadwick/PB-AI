<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags as SpatieHasTags;

/**
 * This trait exists to make tagging easier with our custom tag model that extends Spatie\Tag.
 * See the model in-App\Models\Tag
 */
trait HasTags
{
    use SpatieHasTags;

    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
