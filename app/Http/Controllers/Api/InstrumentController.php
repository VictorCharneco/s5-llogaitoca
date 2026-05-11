<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Instruments\StoreInstrumentRequest;
use App\Http\Requests\Instruments\UpdateInstrumentRequest;
use App\Http\Resources\InstrumentResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Instrument;
use App\Services\InstrumentService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstrumentController extends Controller
{
    private InstrumentService $instrumentService;


    public function __construct(InstrumentService $instrumentService){
        $this->instrumentService = $instrumentService;
    }

    public function index(Request $request): JsonResponse{
        $instruments = $this->instrumentService->getInstruments($request->query('type'));
        return response()->json(['data' => InstrumentResource::collection($instruments)]);
    }

    public function show(int $id):JsonResponse{
        $instrument = Instrument::findOrFail($id);
        return response()->json(['data' => new InstrumentResource($instrument)]); 
    }

    public function store(StoreInstrumentRequest $request): JsonResponse{
        $validated = $request->validated();

        if (!empty($validated['image_url']) && empty($validated['image'])) {
            $validated['image_path'] = $validated['image_url'];
        }

        $instrument = $this->instrumentService->createInstrument($validated, $request->file('image'));

        if (!empty($validated['image_path']) && !$instrument->image_path) {
            $instrument->image_path = $validated['image_path'];
            $instrument->save();
        }

        return response()->json(['data' => new InstrumentResource($instrument)], 201);
    }

    public function update(UpdateInstrumentRequest $request, int $id): JsonResponse
    {
        $instrument = Instrument::findOrFail($id);
        $validated = $request->validated();

        if (!empty($validated['image_url']) && empty($validated['image'])) {
            $validated['image_path'] = $validated['image_url'];
        }

        $updated = $this->instrumentService->updateInstrument($instrument, $validated, $request->file('image'));

        if (!empty($validated['image_path'])) {
            $fresh = $updated->fresh();
            if (!$fresh->image_path || $fresh->image_path !== $validated['image_path']) {
                $fresh->image_path = $validated['image_path'];
                $fresh->save();
            }
        }

        return response()->json(['data' => new InstrumentResource($updated->fresh())], 200);

    }



    public function destroy(int $id): JsonResponse{
        $instrument = Instrument::findOrFail($id);

        $this->instrumentService->deleteInstrument($instrument);

        return response()->json(['message' => "Instrument deleted successfully",]);
    }

    public function uploadImage(Request $request, int $id): JsonResponse{
        $instrument = Instrument::findOrFail($id);

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $old = $instrument->image_path;
        if ($old && !str_starts_with($old, 'demo/') && !str_starts_with($old, 'http')) {
            if (Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        $file = $request->file('image');
        $ext = $file->getClientOriginalExtension() ?: 'webp';
        $name = Str::uuid()->toString() . '.' . $ext;

        $path = $file->storeAs('instruments', $name, 'public');

        $instrument->image_path = $path;
        $instrument->save();

        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => new InstrumentResource($instrument->fresh()),
        ], 200);
    }
}
