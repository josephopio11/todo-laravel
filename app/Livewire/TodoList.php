<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:255')]
    public $name;

    public $search;

    public $editing_id;
    public $new_name;

    public function create()
    {
        // Validate
        $binankani = $this->validateOnly('name');

        Todo::create($binankani);

        $this->reset('name');
        session()->flash('success', 'Created!');

        // ddd($this->name);
    }

    public function delete(Todo $todo)
    {
        $todo->delete();
        session()->flash('success', 'Deleted!');
    }

    public function toggle(Todo $todo)
    {
        $todo->update(['completed' => !$todo->completed]);
        session()->flash('updated'.$todo->id, 'Task updated successfully!');
    }

    public function edit(Todo $todo)
    {
        $this->editing_id = $todo->id;
        $this->new_name = $todo->name;
    }

    public function cancel_edit()
    {
        $this->reset('new_name', 'editing_id');
    }

    public function update(Todo $todo)
    {
        $this->validateOnly('new_name');

        $todo->update(['name' => $this->new_name]);

        $this->reset('new_name', 'editing_id');
        session()->flash('updated'.$todo->id, 'Task modified successfully!');
    }

    public function render()
    {
        $todos = Todo::latest()->where('name','like',"%$this->search%")->paginate(5);
        return view('livewire.todo-list',['todos' => $todos]);
    }
}
