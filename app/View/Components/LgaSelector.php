<?php


namespace App\View\Components;

use Illuminate\View\Component;

class LgaSelector extends Component
{
    public $lgaNames;
    public $selectedLga;

    public function __construct($lgaNames, $selectedLga)
    {
        $this->lgaNames = $lgaNames;
        $this->selectedLga = $selectedLga;
    }

    public function render()
    {
        return view('components.lga-selector');
    }
}
