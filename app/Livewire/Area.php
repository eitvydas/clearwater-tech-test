<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use App\Models\Area as AreaModel;

class Area extends Component
{
    public $areas;

    public string $search = '';

    public bool $showModal = false;

    public string $modalTitle = 'Add New Area';

    #[Rule('required', message:'Please add a name.')]
    public string $areaName = '';

    #[Rule('required', message:'Please select a category.')]
    public string $category = '';

    #[Rule('required', message:'Start date is required.')]
    public string $startDate = '';

    #[Rule('required', message:'End date is required.')]
    public string $endDate = '';

    #[Rule('required', message:'File name is required to store area details.')]
    public string|null $filename = '';

    public string|null $polygonData = null;

    public int|null $editAreaId = null;

    public bool $editMode = false;

    public function render()
    {
        $areasQuery = AreaModel::query();

        if(!empty(trim($this->search))) {
            $areasQuery->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('category', 'like', '%'.$this->search.'%')
                ->orWhere('filename', 'like', '%'.$this->search.'%');
        }

        $this->areas = $areasQuery->get();

        return view('livewire.area');
    }

    public function openModal($areaId = null)
    {
        $this->resetForm();
        $this->resetValidation();

        if($areaId) {
            $area = AreaModel::where('id', $areaId)->firstOrFail();

            $this->editAreaId = $areaId;
            $this->areaName = $area->name;
            $this->category = $area->category;
            $this->startDate = Carbon::parse($area->start)->format('Y-m-d');
            $this->endDate = Carbon::parse($area->end)->format('Y-m-d');
            $this->filename = $area->filename;


            $file = Storage::get('areas/'.$area->filename.'.txt');

            $this->polygonData = $file;

            $this->modalTitle = 'Editing - '.$area->name;
            $this->editMode = true;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try
        {
            $filename = str_replace(' ', '_', trim($this->filename));

        if($this->editMode) {
            $area = AreaModel::where('id', $this->editAreaId)->firstOrFail();
            $infoMessage = 'Area updated successfully.';
        } else {
            $area = new AreaModel;
            $infoMessage = 'New area created successfully.';
        }

        $area->name = $this->areaName;
        $area->category = $this->category;
        $area->start = $this->startDate;
        $area->end =  $this->endDate;
        $area->filename = $filename;

        $area->save();

//            AreaModel::create([
//                'name' => $this->areaName,
//                'category' => $this->category,
//                'start' => $this->startDate,
//                'end' => $this->endDate,
//                'filename' => $filename,
//            ]);

            Storage::disk('local')->put('areas/'.$filename.'.txt', $this->polygonData);

            session()->flash('success', $infoMessage);
        }
        catch (\Exception $e)
        {
            Log::error('Failed to create area: '. $e->getMessage());
            session()->flash('error', 'There was an error creating an area. Please try again or contact support.');
            $this->redirect('/');
        }

        $this->closeModal();
    }

    public function resetForm()
    {
        $this->areaName = '';
        $this->category = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->filename = '';
        $this->polygonData = null;
        $this->editAreaId = null;
        $this->editMode = false;
    }

    #[On('polygon-updated')]
    public function updatePolygonData($data)
    {
        $this->polygonData = $data;
    }

    public function downloadAreaFile($areaId) {
        try
        {
            $area = AreaModel::where('id', $areaId)->firstOrFail();

            $fileName = $area->filename . '.txt';

            return Storage::download('areas/'.$fileName);
        }
        catch (\Exception $e)
        {
            Log::error('Failed to download area file: '. $e->getMessage());
            session()->flash('error', 'There was an error trying to retrieve the file. Please try again or contact support.');
        }
    }
}
