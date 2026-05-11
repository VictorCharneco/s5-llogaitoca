<?php

namespace App\Contracts;

use App\Models\Instrument;
use Illuminate\Http\UploadedFile;

interface InstrumentServiceInterface
{
    public function getInstruments(?string $type);
    public function createInstrument(array $validated, ?UploadedFile $imageFile):Instrument;
    public function updateInstrument(Instrument $instrument, array $validated, ?UploadedFile $imageFile): Instrument;
    public function deleteInstrument(Instrument $instrument):void;
}