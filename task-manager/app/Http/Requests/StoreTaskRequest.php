<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'context_id' => 'nullable|exists:contexts,id',
            'user_id' => 'nullable|exists:users,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'week_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la tâche est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 5000 caractères.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être: à faire, en cours, ou terminé.',
            'priority.required' => 'La priorité est obligatoire.',
            'priority.in' => 'La priorité doit être: basse, moyenne, haute, ou urgente.',
            'context_id.exists' => 'Le contexte sélectionné n\'existe pas.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif, ou webp.',
            'image.max' => 'L\'image ne peut pas dépasser 2 Mo.',
            'week_date.date' => 'La date de semaine n\'est pas valide.',
            'due_date.date' => 'La date d\'échéance n\'est pas valide.',
            'due_date.after_or_equal' => 'La date d\'échéance ne peut pas être dans le passé.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'status' => 'statut',
            'priority' => 'priorité',
            'context_id' => 'contexte',
            'user_id' => 'utilisateur',
            'image' => 'image',
            'week_date' => 'date de semaine',
            'due_date' => 'date d\'échéance',
        ];
    }
}
