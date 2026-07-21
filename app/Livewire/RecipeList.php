<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Recipe;
use Livewire\Component;
use Livewire\WithPagination;

class RecipeList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $difficulty = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'difficulty' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function filterCategory(string $slug): void
    {
        $this->category = $this->category === $slug ? '' : $slug;
        $this->resetPage();
    }

    public function render()
    {
        $recipes = Recipe::with('categories', 'tags')
            ->where('is_published', true)
            ->when($this->category, function ($q) {
                $q->whereHas('categories', fn ($c) => $c->where('category_recipe.category_id', Category::where('slug', $this->category)->value('id')));
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->difficulty !== '', function ($q) {
                $q->where('difficulty', (int) $this->difficulty);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.recipe-list', [
            'recipes' => $recipes,
        ]);
    }
}
