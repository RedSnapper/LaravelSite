<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class  CategoryCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
	/**
	 * @var Category
	 */
	public $category;

	/**
	 * @var Category
	 */
	public $parent;

	/**
	 * Create a new event instance.
	 *
	 * @param Category $category
	 */
    public function __construct(Category $category)
    {
		$this->category = $category;
		$this->parent = Category::index($category->pa);
	}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('category');
    }

	/**
	 * Get the data to broadcast.
	 *
	 * @return array
	 */
	public function broadcastWith()
	{
		return [
			'id' => $this->category->id,
			'name' => $this->category->name,
			'parent' => $this->parent->id
		];
	}
}
