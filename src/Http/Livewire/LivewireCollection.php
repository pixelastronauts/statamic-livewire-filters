<?php

namespace Reach\StatamicLivewireFilters\Http\Livewire;

use Jonassiewertsen\Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Statamic\Tags\Collection\Entries;

class LivewireCollection extends Component
{
    use Traits\GenerateParams, Traits\HandleParams, WithPagination;

    #[Url]
    public $params;

    #[Locked]
    public $collections;

    public $view = 'livewire-collection';

    public function mount($params)
    {
        if (is_null($this->params)) {
            $this->setParameters($params);
        } else {
            $this->setParameters(array_merge($params, $this->params));
        }
    }

    public function setParameters($params)
    {
        $collection_keys = ['from', 'in', 'folder', 'use', 'collection'];

        foreach ($collection_keys as $key) {
            if (array_key_exists($key, $params)) {
                $this->collections = $params[$key];
                unset($params[$key]);
            }
        }
        if (array_key_exists('view', $params)) {
            $this->view = $params['view'];
            unset($params['view']);
        }
        $this->params = $params;
        $this->handlePresetParams();
    }

    #[On('filter-updated')]
    public function filterUpdated($field, $condition, $payload, $command, $modifier)
    {
        if ($condition === 'taxonomy') {
            $this->handleTaxonomyCondition($field, $payload, $command, $modifier);

            return;
        }
        $this->handleCondition($field, $condition, $payload, $command);
    }

    #[On('sort-updated')]
    public function sortUpdated($sort)
    {
        if ($sort === '' || $sort === null) {
            unset($this->params['sort']);

            return;
        }
        $this->params['sort'] = $sort;
    }

    public function entries()
    {
        $entries = (new Entries($this->generateParams()))->get();
        $this->dispatch('entriesUpdated');
        if (isset($this->params['paginate'])) {
            return $this->withPagination('entries', $entries);
        }

        return ['entries' => $entries];
    }

    public function render()
    {
        return view('statamic-livewire-filters::livewire.'.$this->view)->with([
            ...$this->entries(),
        ]);
    }
}
