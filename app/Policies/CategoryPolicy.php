<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class CategoryPolicy
{
    use HandlesAuthorization;

	/**
	 * @var Collection
	 */
	private $categories;
	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * CategoryPolicy constructor.
	 *
	 * @param Collection $categories
	 * @param Connection $connection
	 */
	public function __construct(Collection $categories,Connection $connection) {
		$this->categories = $categories;
		$this->connection = $connection;
	}

	public function view(User $user, Team $team, Activity $activity){
		return $this->isTeamActivityAvailable($user,$team,$activity);
	}

	public function update(User $user, Category $category){
    	return $this->isCategoryAvailable($user,$category);
	}

	protected function isCategoryAvailable(User $user, Category $category){
		if(!$this->categories->has($user->id)) {
			$this->categories->put($user->id,$this->getAvailableCategories($user->id));
		}
		return $this->categories->get($user->id)->contains($category->id);
	}

	protected function getAvailableCategories(int $user) : Collection {

		//select b.id from categories b,categories c,category_role cr,role_user ru where ru.user_id=1 and cr.role_id=ru.role_id and b.idx < c.nextchild and b.idx >= c.idx and c.id=cr.category_id group by b.id;

		$query = $this->connection->table('categories as self')->select('self.id')
		  ->join('categories', function ($join) {
			  $join->on('self.idx', '<', 'categories.nextchild')->on('self.idx' ,'>=','categories.idx');
		  })
		  ->join('category_role','categories.id','category_role.category_id')
		  ->join('role_user','category_role.role_id','role_user.role_id')
		  ->where('role_user.user_id',$user)
		  ->groupBy('self.id');

		return $query->pluck('id');
	}

}
