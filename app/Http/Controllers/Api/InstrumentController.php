<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Instrument;
use App\Services\InstrumentService;

class InstrumentController extends Controller
{
    private InstrumentService $instrumentService;


    public function __construct(InstrumentService $instrumentService){
        $this->instrumentService = $instrumentService;
    }

    public function index(Request $request): JsonResponse{
        $instruments = $this->instrumentService->getInstruments($request->query('type'));
        return response()->json(['data' => $instruments,]);
    }

    public function show(int $id):JsonResponse{
        $instrument = Instrument::find($id);

        if(!$instrument){
            return response()->json(['message' => "No s'ha trobat l'instrument"], 404);
        }

        return response()->json(['data' => $instrument,]); 
    }

    public function store(Request $request): JsonResponse{
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:STRING,KEYBOARD,PERCUSSION,WIND'],
            'status' => ['required', 'in:AVAILABLE,OUT_OF_STOCK,MAINTENANCE'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $instrument = $this->instrumentService->createInstrument($validated, $request->file('image'));

        return response()->json(['data' => $instrument,], 201);
    }

    public function update(Request $request, int $id): JsonResponse{
        $instrument = Instrument::find($id);

        if(!$instrument){
            return response()->json(['message' => "No s'ha trobat l'instrument"], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:STRING,KEYBOARD,PERCUSSION,WIND'],
            'status' => ['required', 'in:AVAILABLE,OUT_OF_STOCK,MAINTENANCE'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $updated = $this->instrumentService->updateInstrument($instrument, $validated, $request->file('image'));

        return response()->json(['data' =>$updated->fresh(),]);

    }



    public function destroy(int $id): JsonResponse{
        $instrument = Instrument::find($id);

        if(!$instrument){
            return response()->json(['message' => "No s'ha trobat l'instrument"], 404);
        }

        $this->instrumentService->deleteInstrument($instrument);

        return response()->json(['message' => "🗑️ S'ha esborrat l'instrument",]);
    }

}
