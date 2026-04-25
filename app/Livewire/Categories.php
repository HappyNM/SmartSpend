<?php
namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Title("Categories - ExpenseApp")]
class Categories extends Component
{
    public $name = "";
    public $color = "#3B82F6";
    public $icon = "";
    public $editingId = null;
    public $isEditing = false;

     public $colors = [
        
'#B86B6B', // Red
'#B88563', // Orange
'#B89E68', // Amber
'#B8A855', // Yellow
'#98A866', // Lime
'#6B9978', // Green
'#6BA895', // Emerald
'#6BA89C', // Teal
'#6BA8B8', // Cyan
'#7A9BB8', // Sky
'#8B8FB8', // Blue
'#9B8FB8', // Indigo
'#A689B8', // Violet
'#B089B8', // Purple
'#B367B8', // Fuchsia
'#B88FA8', // Pink
'#B88888', // Rose
    ];

    protected function rules(){
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . ($this->editingId ?: 'NULL') . ',id,user_id,' . Auth::id(),
            'color' => 'required|string',
            'icon' => 'nullable|string|max:255',
        ];
    }
    protected $messages = [
        'name.required' => 'Please enter a category name.',
        'name.unique' => 'You already have a category with this name.',
        'color.required' => 'Please select a color.',
    ];

    //use computed properties
    #[Computed()]
    public function categories(){
        return Category::withCount('expenses')
            ->where('user_id', Auth::id())
         ->orderBy('name')
         ->get();
    }
    public function edit($categoryId){
        $category = Category::findOrFail($categoryId);
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->color = $category->color;
        $this->icon = $category->icon;
        $this->isEditing = true;
    }
    public function save(){
        $this->validate();
        if ($this->isEditing && $this->editingId) {
            $category = Category::findOrFail($this->editingId);
            if ($category->user_id !== Auth::id()) {
                abort(403);
            }
            $category->update([
            'name'=> $this->name,
            'color'=> $this->color,
            'icon'=> $this->icon
            ]);
            session()->flash('message', 'Category updated successfully');
        }else {
            //creating
        Category::create([
            'user_id' => Auth:: id(),
            'name'=> $this->name,
            'color'=> $this->color,
            'icon'=> $this->icon
        ]);
        session()->flash('message', 'Category created succesfully');
        }
        
        $this->reset(['name', 'color','icon', 'editingId','isEditing']);

    }
    public function cancelEdit() {
        $this->reset(['name', 'color','icon', 'editingId','isEditing']);
        $this->color = "#3B82F6";
    }
    public function delete($categoryId){
        $category = Category::findOrFail($categoryId);

        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        //check if category has exense
        if($category->expenses()->count() > 0){
            session()->flash('error','Can not delete category with existing expenses.');
            return;
        }

        $category->delete();
        session()->flash('message','Category deleted successfully!');
    }
    public function render(){
        return view('livewire.categories', [
            'categories'=> $this->categories,
        ]);
    }
}