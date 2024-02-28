<?php

namespace Reach\StatamicLivewireFilters\Http\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class LfRadioFilter extends Component
{
    use Traits\IsLivewireFilter, Traits\HandleEntriesCount;

    public $view = 'lf-radio';

    public $options;

    public $selected = '';

    #[Computed(persist: true)]
    public function filterOptions()
    {
        if (isset($this->statamic_field['options'])) {
            return $this->statamic_field['options'];
        } elseif (is_array($this->options)) {
            return $this->options;
        }
    }

    public function updatedSelected()
    {
        if (config('statamic-livewire-filters.validate_filter_values')) {
            $this->validate();
        }

        $this->dispatch('filter-updated',
            field: $this->field,
            condition: $this->condition,
            payload: $this->selected,
            command: 'replace',
            modifier: $this->modifier,
        )
            ->to(LivewireCollection::class);
    }

    public function clear()
    {
        $this->selected = '';
        $this->clearFilters();
    }

    public function rules()
    {
        return [
            'selected' => ['required', Rule::in(array_keys($this->filterOptions()))],
        ];
    }

    #[On('preset-params')]
    public function setPresetSort($params)
    {
        if (array_key_exists($this->getParamKey(), $params)) {
            $this->selected = $params[$this->getParamKey()];
        }
    }

    public function render()
    {
        return view('statamic-livewire-filters::livewire.filters.'.$this->view);
    }
}
