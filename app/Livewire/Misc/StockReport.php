<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\stock_tb;
use App\Models\data_tb;
use App\Models\vendor_tb;
use Illuminate\Support\Facades\DB;

class StockReport extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'customer';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'customer'],
        'sortDirection' => ['except' => 'asc'],
        'page'
    ];
    public $filtered = false;

    public function searchByPartNo()
    {
        $this->filtered = true;
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->filtered = false;
        $this->resetPage();
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function delete($id)
    {
        stock_tb::where('stkid', $id)->delete();
        DB::table('stock_ret')->where('stkid', $id)->delete();
        session()->flash('success', 'Stock record deleted successfully.');
    }

    public function render()
        {
            $query = stock_tb::select(
                    'stock_tb.stkid',
                    'stock_tb.customer',
                    'stock_tb.part_no',
                    'stock_tb.rev',
                    'stock_tb.supplier',
                    'stock_tb.dtadded',
                    'stock_tb.dc',
                    'stock_tb.finish',
                    'stock_tb.manuf_dt',
                    'stock_tb.docsready',
                    'stock_tb.qty as ssadd',
                    'data_tb.c_shortname as c_shortname'
                )
                ->leftJoin('data_tb', 'stock_tb.customer', '=', 'data_tb.c_name');

            if ($this->filtered && $this->search !== '') {
                $term = str_replace('+', ' ', $this->search);
                $query->where('stock_tb.part_no', 'like', "%{$term}%");
            }

            $stocks = $query
                ->orderBy(DB::raw("TRIM(" . $this->sortField . ")"), $this->sortDirection)
                ->orderBy('part_no')
                ->paginate(100);

            return view('livewire.misc.stock-report', [
                'stocks' => $stocks
            ])->layout('layouts.app', ['title' => 'Stock Report']);
        }

}