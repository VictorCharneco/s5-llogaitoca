<?php

namespace App\Services;

use App\Models\Instrument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InstrumentService
{
    public function getInstruments(?string $type){
        $query = Instrument::query();

        if(!empty($type)){
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function createInstrument(array $validated, ?UploadedFile $imageFile): Instrument{
        $imagePath = null;

        if($imageFile){
            $imagePath = $imageFile->store('instruments', 'public');
        }

        return Instrument::create([
            'name'=> $validated['name'],
            'description'=> $validated['description'] ?? null,
            'type'=> $validated['type'],
            'status'=> $validated['status'],
            'image_path'=> $imagePath,
        ]);
    }

    public function updateInstrument(Instrument $instrument, array $validated, ?UploadedFile $imageFile): Instrument{
        if($imageFile){
            if($instrument->image_path){
                Storage::disk('public')->delete($instrument->image_path);
            }

            $validated['image_path'] = $imageFile->store('instruments', 'public');
        }

        $instrument->update($validated);

        return $instrument;
    }

    public function deleteInstrument(Instrument $instrument): void{
        if($instrument->image_path){
            Storage::disk('public')->delete($instrument->image_path);
        }

        $instrument->delete();
    }
}
