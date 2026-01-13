<?php

/** @noinspection PhpExpressionAlwaysNullInspection */

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

trait HasActivitySchema
{
    /**
     * Define the activity schema for this model.
     * Return an array of relationship names whose activities should be aggregated.
     * Use 'self' to include the model's own activities.
     *
     * @return array<string>
     */
    abstract public function activitySchema(): array;

    /**
     * Get aggregated activities grouped by schema order, chronologically within each group.
     *
     * @return Collection<string, Collection>
     */
    public function getAggregatedActivities(): Collection
    {
        $schema = $this->activitySchema();
        $grouped = collect();

        foreach ($schema as $relation) {
            $activities = $this->getActivitiesForRelation($relation);

            if ($activities->isNotEmpty()) {
                $label = $this->getRelationLabel($relation);
                $grouped[$label] = $activities->sortBy('created_at')->values();
            }
        }

        return $grouped;
    }

    /**
     * Get activities for a specific relation.
     */
    protected function getActivitiesForRelation(string $relation): EloquentCollection
    {
        if ($relation === 'self') {
            return $this->queryActivities(static::class, [$this->id]);
        }

        if ($relation === 'media') {
            return $this->getMediaActivities();
        }

        return $this->getRelationActivities($relation);
    }

    /**
     * Get activities for a model relationship.
     */
    protected function getRelationActivities(string $relation): EloquentCollection
    {
        if (! method_exists($this, $relation)) {
            return new EloquentCollection;
        }

        $relationInstance = $this->$relation();
        $relatedModel = $relationInstance->getRelated();
        $relatedClass = get_class($relatedModel);

        // Include trashed related models when getting IDs
        $related = method_exists($relationInstance, 'withTrashed')
            ? $this->$relation()->withTrashed()->get()
            : $this->$relation;

        if ($related instanceof EloquentCollection && $related->isNotEmpty()) {
            $ids = $related->pluck('id')->toArray();

            return $this->queryActivities($relatedClass, $ids);
        }

        if ($related && ! ($related instanceof EloquentCollection)) {
            return $this->queryActivities($relatedClass, [$related->id]);
        }

        return new EloquentCollection;
    }

    /**
     * Get activities for media (Spatie MediaLibrary).
     */
    protected function getMediaActivities(): EloquentCollection
    {
        if (! method_exists($this, 'media')) {
            return new EloquentCollection;
        }

        // Include trashed media
        $media = $this->media()->withTrashed()->get();

        if ($media->isNotEmpty()) {
            $ids = $media->pluck('id')->toArray();

            return $this->queryActivities(\App\Models\Media::class, $ids);
        }

        return new EloquentCollection;
    }

    /**
     * Query activities for a subject type and IDs, including trashed subjects.
     */
    protected function queryActivities(string $subjectClass, array $ids): EloquentCollection
    {
        return Activity::where('subject_type', $subjectClass)
            ->whereIn('subject_id', $ids)
            ->with([
                'causer',
                'subject' => fn (MorphTo $morphTo) => $morphTo->withTrashed(),
            ])
            ->get()
            ->each(fn ($a) => $a->subject_identifier = $this->getSubjectIdentifier($a->subject));
    }

    /**
     * Get a human-readable identifier for a subject.
     */
    protected function getSubjectIdentifier($subject): ?string
    {
        if (! $subject) {
            return null;
        }

        // Try common identifier attributes in order of preference
        // Use null coalescing since isset() doesn't work with accessors
        return $subject->full_name
            ?? $subject->title
            ?? $subject->name
            ?? $subject->file_name
            ?? null;
    }

    /**
     * Get human-readable label for a relation.
     */
    protected function getRelationLabel(string $relation): string
    {
        if ($relation === 'self') {
            return class_basename(static::class);
        }

        return str($relation)->headline()->toString();
    }
}
