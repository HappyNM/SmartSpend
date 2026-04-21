<?php
namespace App\Livewire\Wallet;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class DepositFunds extends Component{
    public function render(){
        return view('livewire.deposit-funds');
    }
}