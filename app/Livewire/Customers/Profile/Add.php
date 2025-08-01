<?php 
namespace App\Livewire\Customers\Profile;

use App\Models\data_tb as Customer;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;
use Livewire\Component;

class Add extends Component
{
    public $customers;
    public $cid;
    public $requirements = [];

    public function mount()
    {
        $this->customers = Customer::all();
        $this->addRequirement(); // Add initial empty requirement
    }

    public function addRequirement()
    {
        $this->requirements[] = [
            'req' => '',
            'viewable' => [],
            'checkboxes' => []
        ];
    }

    public function removeRequirement($index)
    {
        unset($this->requirements[$index]);
        $this->requirements = array_values($this->requirements); // reindex
    }

    public function save()
    {
       // dd($this->requirements);
        $this->validate([
            'cid' => 'required|exists:data_tb,data_id',
            'requirements.*.req' => 'required|string',
        ]);

        // Create profile_tb
        $profile = new Profile();
        $profile->custid = $this->cid;
        $profile->save();

     // $forms = ['quo', 'po', 'con', 'pac', 'inv', 'cre'];

    foreach ($this->requirements as $req) {
       $viewable = '';

      //  $forms = ['quo', 'po', 'con', 'pac', 'inv', 'cre'];

        foreach ($req['viewable'] as $key => $viewab) {
            //$formKey = $form . '1';
            $inSelect = $viewab.'|';
            $viewable .= $inSelect;
        }
        foreach($req['checkboxes'] as $key => $checkvalue) {
            $checkbox = $key.'|';
            $viewable .= $checkbox;
        }
        $viewable = rtrim($viewable, '|');

        $detail = new ProfileDetail();
        $detail->profid = $profile->profid;
        $detail->reqs = $req['req'];
        $detail->viewable = $viewable;
        $detail->save();
    }

        session()->flash('success', 'Profile created successfully!');
        return redirect(route('customers.profile.manage'));

       // return redirect()->to('/manage-profile'); // Update this to your route
    }

    public function render()
    {
        return view('livewire.customers.profile.add')->layout('layouts.app', ['title' => 'Customers Profile']);
    }
}