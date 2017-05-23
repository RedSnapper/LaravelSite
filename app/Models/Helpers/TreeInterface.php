<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 04/04/2017 07:44
 */
namespace App\Models\Helpers;
use Illuminate\Database\Eloquent\Builder;

interface TreeInterface {

	public function scopeParent(Builder $query);

	public function scopeAncestors(Builder $query, bool $self = false);

	public function scopeSiblings(Builder $query, bool $self = true);

	public function scopeChildren(Builder $query);

	public function moveAfter(TreeInterface $sibling);

	public function moveBefore(TreeInterface $sibling,TreeInterface $parent);

	public function moveInto(TreeInterface $parent);

	public function scopeIndex(Builder $query, $index);

	public function scopeReference(Builder $query,string $reference);

	public function scopeDescendants(Builder $query, bool $self = false);

	public function scopeOrdered(Builder $query);

	public function checkIntegrity(): array;
}