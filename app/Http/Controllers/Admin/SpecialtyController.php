<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of specialties.
     */
    public function index(): Response
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $specialties = Especialidad::orderBy('nombre_especialidad', 'asc')->get();

        return Inertia::render('Admin/Specialties/Index', [
            'specialties' => $specialties,
        ]);
    }

    /**
     * Store a newly created specialty.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombre_especialidad' => 'required|string|max:100|unique:especialidades,nombre_especialidad',
            'descripcion' => 'required|string|max:255',
        ], [
            'nombre_especialidad.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre_especialidad.unique' => 'Esta especialidad ya está registrada en el sistema.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        Especialidad::create($validated);

        return redirect()->back()->with('success', 'Especialidad médica registrada exitosamente.');
    }

    /**
     * Update the specified specialty.
     */
    public function update(Request $request, Especialidad $especialidad): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombre_especialidad' => 'required|string|max:100|unique:especialidades,nombre_especialidad,' . $especialidad->id_especialidad . ',id_especialidad',
            'descripcion' => 'required|string|max:255',
        ], [
            'nombre_especialidad.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre_especialidad.unique' => 'Esta especialidad ya está registrada.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        $especialidad->update($validated);

        return redirect()->back()->with('success', 'Especialidad médica modificada exitosamente.');
    }

    /**
     * Remove the specified specialty.
     */
    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $especialidad->delete();

        return redirect()->back()->with('success', 'Especialidad médica eliminada.');
    }
}
