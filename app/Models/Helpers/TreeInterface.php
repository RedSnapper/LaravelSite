<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 04/04/2017 07:44
 */
namespace App\Models\Helpers;
use Illuminate\Database\Eloquent\Builder;

interface TreeInterface {

	public static function nodeBranch($name = 'ROOT'): array;

	public static function options(string $reference);

	public function scopeParent(Builder $query);

	public function scopeAncestors(Builder $query, bool $self = false);

	public function scopeSiblings(Builder $query, bool $self = true);

	public function scopeChildren(Builder $query);

	public function createNode(int $parent = null, string $name) : TreeInterface;

	public function moveTo(int $parentId = null,int $indexReplace = null);

	public function moveAfter(int $sibling);

	public function moveBefore(int $sibling);

	public function moveInto(int $parent);

	public function scopeIndex(Builder $query, int $index);

	public function scopeReference(Builder $query,string $reference);

	public function scopeDescendants(Builder $query, bool $self = false);

	public function scopeTier(Builder $query, $columns = ['aggregate']);

	public function scopeOrdered(Builder $query);

	public function checkIntegrity(): array;
}