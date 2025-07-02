<?php

namespace App\Http\Controllers;

use App\Models\Modality;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use DataTables;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

/**
 * ModalityController handles CRUD operations for medical imaging modalities.
 * Provides DataTables integration for listing modalities with search and pagination.
 */
class ModalityController extends Controller
{

    /**
     * Display list of modalities with DataTables integration.
     * Handles AJAX requests for dynamic data loading and search functionality.
     */
    public function index(Request $request)
    {
        // Check if request is AJAX (DataTables request)
        if (request()->ajax()) {
            // Build query for modalities
            $query = Modality::query();

            // Apply custom search filter if provided
            if ($request->has('search_custom') && $request->search_custom !== null) {
                $search = $request->search_custom;
                $query->where(function($q) use ($search) {
                    // Search in both name and price fields
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
                });
            }

            // Return DataTables response with custom action buttons
            return FacadesDataTables::of($query)
                ->addIndexColumn()  // Add row index column
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $show = '';
                    $edit = "";

                    // Create edit button with route to edit page
                    $edit = '<a href="' . route('modalities.edit', [$row->id]) . '" class="edit btn btn-info btn-sm">edit</a>';
                    $btn .= $edit;

                    // Create delete button with JavaScript confirmation
                    $delete = '
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-modality"
                            data-id="' . $row->id . '" data-name="' . e($row->name) . '">
                            Delete
                        </a>';

                    $btn .= $delete;
                    return $btn;
                })
             
                // Mark action column as raw HTML (don't escape)
                ->rawColumns(['action'])

                ->make(true);  // Generate DataTables response
        }
       
        // Return view for non-AJAX requests (initial page load)
        return view('modalities.index');
    }

    /**
     * Show the form for creating a new modality.
     * Returns the create view for modality entry.
     */
    public function create()
    {
        return view('modalities.create');
    }

    /**
     * Store a newly created modality in the database.
     * Validates input data before saving.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',  // Modality name is required, max 255 chars
            'price' => 'required|numeric|min:0'   // Price is required, must be numeric and non-negative
        ]);

        // Create new modality record
        Modality::create($validated);

        // Redirect to index page with success message
        return redirect()->route('modalities.index')
            ->with('status', 'Modality created successfully.');
    }

    /**
     * Display the specified modality details.
     * Shows individual modality information.
     */
    public function show(Modality $modality)
    {
        return view('modalities.show', compact('modality'));
    }

    /**
     * Show the form for editing the specified modality.
     * Returns edit view with pre-populated modality data.
     */
    public function edit(Modality $modality)
    {
        return view('modalities.edit', compact('modality'));
    }

    /**
     * Update the specified modality in the database.
     * Validates input data before updating.
     */
    public function update(Request $request, Modality $modality)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',  // Modality name is required, max 255 chars
            'price' => 'required|numeric|min:0'   // Price is required, must be numeric and non-negative
        ]);

        // Update the modality record
        $modality->update($validated);

        // Redirect to index page with success message
        return redirect()->route('modalities.index')
            ->with('status', 'Modality updated successfully.');
    }

    /**
     * Remove the specified modality from the database.
     * Permanently deletes the modality record.
     */
    public function destroy(Modality $modality)
    {
        // Delete the modality record
        $modality->delete();

        // Redirect to index page with success message
        return redirect()->route('modalities.index')
            ->with('status', 'Modality deleted successfully.');
    }
}
