<?php

namespace Reach\StatamicLivewireFilters\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class LfCheckbox extends Component
{
    use Traits\IsLivewireFilter;

    public $view = 'lf-checkbox';

    public $options;

    public $selected = [];

    #[Computed]
    public function filterOptions()
    {
        ray($this->statamic_field);
        if (isset($this->statamic_field['options'])) {
            return $this->statamic_field['options'];
        } elseif (is_array($this->options)) {
            return $this->options;
        }
    }

    public function updatedSelected()
    {
        ray($this->selected);
        $this->dispatch('filter-updated',
            field: $this->field,
            condition: $this->condition,
            payload: $this->selected,
            modifer: $this->modifier,
        )
            ->to(LivewireCollection::class);
    }

    public function render()
    {
        return view('statamic-livewire-filters::livewire.filters.'.$this->view);
    }
}
