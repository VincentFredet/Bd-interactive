<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskImageController extends Controller
{
    /**
     * Store a newly uploaded image
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        $uploadedImages = [];
        $order = $task->images()->max('order') ?? 0;

        foreach ($request->file('images') as $image) {
            $order++;

            $filename = time() . '_' . uniqid() . '.' . $image->extension();
            $image->storeAs('tasks', $filename, 'public');

            $taskImage = $task->images()->create([
                'filename' => $filename,
                'original_name' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'order' => $order,
            ]);

            $uploadedImages[] = $taskImage;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedImages) . ' image(s) téléchargée(s) avec succès',
                'images' => $uploadedImages,
            ]);
        }

        return back()->with('success', count($uploadedImages) . ' image(s) téléchargée(s) avec succès');
    }

    /**
     * Delete an image
     */
    public function destroy(TaskImage $image)
    {
        $image->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Image supprimée avec succès',
            ]);
        }

        return back()->with('success', 'Image supprimée avec succès');
    }

    /**
     * Reorder images
     */
    public function reorder(Request $request, Task $task)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:task_images,id',
        ]);

        foreach ($request->order as $index => $imageId) {
            TaskImage::where('id', $imageId)
                ->where('task_id', $task->id)
                ->update(['order' => $index + 1]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordre des images mis à jour',
            ]);
        }

        return back()->with('success', 'Ordre des images mis à jour');
    }
}
