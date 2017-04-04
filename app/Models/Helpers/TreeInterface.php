<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 04/04/2017 07:44
 */
namespace App\Models\Helpers;
use Illuminate\Database\Eloquent\Builder;

interface TreeInterface {
	public static function id(int $index, $columns = ['*']);

	public static function index(int $index, $columns = ['*']);

	public static function reference(string $name, $columns = ['*']);

	public static function nodeBranch($name = 'ROOT'): array;

	public static function options(string $reference);

	public function scopeParent(Builder $query, $columns = ['*']);

	public function scopeAncestors(Builder $query, bool $self = false);

	public function scopeSiblings(Builder $query, bool $self = true);

	public function scopeChildren(Builder $query);

	public function scopeDescendants(Builder $query, bool $self = false);

	public function scopeTier(Builder $query, $columns = ['aggregate']);

	public function scopeOrdered(Builder $query);

	public function checkIntegrity(): array;
}